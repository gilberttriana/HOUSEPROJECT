<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class ProyectoController extends Controller
{
    public function index()
    {
        $proyectos = Proyecto::orderBy('fecha_creacion', 'desc')->get();
        // calcular progreso para cada proyecto y anexarlo como atributo para la vista
        try{
            foreach($proyectos as $proj){
                $computedProgress = null;
                $now = Carbon::now();
                $start = null; $end = null;
                // admitir varias columnas posibles
                if (!empty($proj->fecha_inicio) || !empty($proj->fecha_inicio_estimado)){
                    $start = Carbon::parse($proj->fecha_inicio ?? $proj->fecha_inicio_estimado);
                } elseif (!empty($proj->created_at)){
                    $start = Carbon::parse($proj->created_at);
                }
                if (!empty($proj->fecha_fin) || !empty($proj->fecha_finalizacion)){
                    $end = Carbon::parse($proj->fecha_fin ?? $proj->fecha_finalizacion);
                }
                // si existe un valor explícito en la columna 'progreso' respetarlo
                if (isset($proj->progreso) && is_numeric($proj->progreso)){
                    $computedProgress = intval(max(0, min(100, $proj->progreso)));
                } elseif (isset($proj->progress) && is_numeric($proj->progress)){
                    $computedProgress = intval(max(0, min(100, $proj->progress)));
                } else {
                    if ($start && $end){
                        if ($end->lessThanOrEqualTo($start)){
                            $computedProgress = $now->greaterThanOrEqualTo($end) ? 100 : 0;
                        } else {
                            // usar timestamps para mayor precisión y evitar signos raros
                                $total = max(1, intval($end->getTimestamp() - $start->getTimestamp()));
                                $done = $now->lessThanOrEqualTo($start) ? 0 : intval($now->getTimestamp() - $start->getTimestamp());
                                $done = max(0, min($total, $done));
                                $computedProgress = intval(floor(($done / $total) * 100));
                        }
                    } elseif ($start && !$end){
                        $computedProgress = $now->lessThan($start) ? 0 : 100;
                    } elseif ($end && !$start){
                        $computedProgress = $now->greaterThanOrEqualTo($end) ? 100 : 0;
                    } else {
                        $computedProgress = 0;
                    }
                }
                $proj->computed_progress = intval(max(0, min(100, $computedProgress)));
            }
        } catch (\Throwable $e){
            // no bloquear la carga del index si falla
            Log::warning('Error calculando computed_progress en index: '.$e->getMessage());
        }
        // pasar también la lista de materiales disponibles para el modal
        $materialsList = \App\Models\Material::where('stock','>',0)->orderBy('nombre')->get();
        return view('dashboard.proyectos.index', compact('proyectos', 'materialsList'));
    }

    public function create()
    {
    // Redirigir a la lista con query param open=1 para abrir el modal una sola vez
    return redirect()->route('proyectos.index', ['open' => 1]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'tipo_proyecto' => 'nullable|string|max:100',
            'contratista' => 'nullable|string|max:255',
            'fecha_inicio' => 'nullable|date', // will map to fecha_inicio_estimado
            'fecha_fin' => 'nullable|date', // will map to fecha_finalizacion
            'presupuesto_est' => 'nullable|numeric', // will map to presupuesto_estimado
            'duracion_estimada' => 'nullable|integer',
            'Numero_responsable' => 'nullable|integer',
            'materials' => 'nullable|array'
        ]);
        $usuario = Auth::user();
        if(!$usuario) return redirect()->back()->with('error', 'Autenticación requerida');

        // Solo admin y clientes ('usuario') pueden crear proyectos
        if(!in_array($usuario->rol, ['admin','usuario'])){
            return redirect()->back()->with('error', 'No tienes permisos para crear proyectos');
        }

        // log request for debugging
        Log::info('ProyectoController@store request', ['user' => $usuario->id_usuario ?? null, 'data' => $data]);

        try {
            DB::beginTransaction();

            // crear explícitamente para evitar problemas de mass-assignment invisibles
            $proj = new Proyecto();
            $proj->id_usuario = $usuario->id_usuario;
            $proj->nombre = $data['nombre'];
            $proj->descripcion = $data['descripcion'] ?? null;
            $proj->tipo_proyecto = $data['tipo_proyecto'] ?? 'General';
            // Guardar 'contratista' si la columna existe (ahora debe ser VARCHAR/STRING)
            if (!empty($data['contratista']) && Schema::hasColumn('proyectos', 'contratista')){
                $proj->contratista = $data['contratista'];
            }
            // Map request fields to DB column names
            $proj->fecha_inicio_estimado = $data['fecha_inicio'] ?? null;
            // fecha de finalización estimada (opcional)
            if (!empty($data['fecha_fin'])){
                // usar fecha_finalizacion si la columna existe
                if (Schema::hasColumn('proyectos', 'fecha_finalizacion')){
                    $proj->fecha_finalizacion = $data['fecha_fin'];
                } else {
                    // si no existe, intentar guardar en duracion_estimada como fallback
                    try{
                        $start = $proj->fecha_inicio_estimado ? Carbon::parse($proj->fecha_inicio_estimado) : null;
                        $end = Carbon::parse($data['fecha_fin']);
                        if ($start){
                            $proj->duracion_estimada = max(0, $end->diffInDays($start));
                        }
                    } catch (\Throwable $e){}
                }
            }
            // not persisting 'fecha_fin' because the DB schema does not include that column
            $proj->presupuesto_estimado = $data['presupuesto_est'] ?? null;
            // 'estado' se gestiona desde el módulo de aprobaciones; no asignamos aquí.
            // columnas NOT NULL en tu esquema: dar defaults si no vienen
            $proj->duracion_estimada = $data['duracion_estimada'] ?? 0; // columna NOT NULL en esquema
            $proj->Numero_responsable = $data['Numero_responsable'] ?? ($usuario->id_usuario ?? 0); // columna NOT NULL

            if (!$proj->save()){
                throw new \Exception('save returned false');
            }
            Log::info('Proyecto created', ['id' => $proj->id_proyecto ?? null, 'model' => $proj->toArray()]);

            // Asociar materiales seleccionados con cantidad (si existen)
            if (!empty($data['materials']) && is_array($data['materials'])){
                $sync = [];
                foreach($data['materials'] as $mid => $info){
                    // info expected as ['selected' => '1', 'cantidad' => '2']
                    if (is_array($info) && array_key_exists('selected', $info)){
                        $cantidad = intval($info['cantidad'] ?? 0);
                        if ($cantidad > 0){
                            $sync[intval($mid)] = ['cantidad' => $cantidad];
                        }
                    }
                }
                    if (!empty($sync)){
                        // Filtrar por materiales válidos y lock para evitar condiciones de carrera
                        $ids = array_keys($sync);
                        $materials = Material::whereIn('id_material', $ids)->lockForUpdate()->get()->keyBy('id_material');

                        // Validar stock disponible
                        foreach($ids as $mid){
                            $requested = intval($sync[$mid]['cantidad'] ?? 0);
                            $mat = $materials->get(intval($mid));
                            if (!$mat){
                                throw new \Exception('Material no encontrado: ' . $mid);
                            }
                            $available = intval($mat->stock ?? 0);
                            if ($requested > $available){
                                throw new \Exception("Stock insuficiente para {$mat->nombre}: solicitado={$requested}, disponible={$available}");
                            }
                        }

                        // Decrementar stock y preparar data final para sync
                        $final = [];
                        foreach($ids as $id){
                            $qty = intval($sync[$id]['cantidad']);
                            $mat = $materials->get(intval($id));
                            // decrementar stock
                                $mat->stock = intval($mat->stock) - $qty;
                            $mat->save();
                            $final[intval($id)] = ['cantidad' => $qty];
                        }

                        $proj->materiales()->sync($final);
                    }
            }
            // Calcular y persistir progreso basado en fechas si no hay progreso explícito
            try{
                $computedProgress = null;
                $start = null; $end = null;
                if (!empty($proj->fecha_inicio_estimado)){
                    $start = Carbon::parse($proj->fecha_inicio_estimado);
                } elseif (!empty($proj->created_at)){
                    $start = Carbon::parse($proj->created_at);
                }
                if (!empty($proj->fecha_finalizacion)){
                    $end = Carbon::parse($proj->fecha_finalizacion);
                }
                if ($start && $end){
                    // usar timestamps directos para evitar problemas con diffInSeconds
                    $total = max(1, intval($end->getTimestamp() - $start->getTimestamp()));
                    $done = Carbon::now()->lessThanOrEqualTo($start) ? 0 : intval(Carbon::now()->getTimestamp() - $start->getTimestamp());
                    $done = max(0, min($total, $done));
                    $pct = intval(floor( ($done / $total) * 100 ));
                    $computedProgress = max(0, min(100, $pct));
                }
                if (!is_null($computedProgress)){
                    $proj->progreso = $computedProgress;
                    $proj->save();
                }
            } catch (\Throwable $e){
                // no bloquear el flujo si falla el cálculo
                Log::warning('No se pudo calcular progreso: '.$e->getMessage());
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            // log the exception for debugging
            Log::error('Proyecto store error: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error','No se pudo crear el proyecto: '.$e->getMessage());
        }

        return redirect()->route('proyectos.index')->with('success', 'Proyecto creado correctamente');
    }

    /**
     * Aprobar un proyecto (marcar estado = 'activo')
     */
    public function aprobar(Request $request, $id)
    {
        $usuario = Auth::user();
        if(!$usuario || ($usuario->rol ?? '') !== 'admin'){
            return redirect()->back()->with('error', 'No tienes permisos para esta acción');
        }

        $proyecto = Proyecto::find($id);
        if(!$proyecto){
            return redirect()->back()->with('error', 'Proyecto no encontrado');
        }

        $proyecto->estado = 'activo';
        $proyecto->save();

        return redirect()->route('aprobaciones.dashboard')->with('success', 'Proyecto aprobado y activado');
    }

    /**
     * Rechazar un proyecto (marcar estado = 'cancelado')
     */
    public function rechazar(Request $request, $id)
    {
        $usuario = Auth::user();
        if(!$usuario || ($usuario->rol ?? '') !== 'admin'){
            return redirect()->back()->with('error', 'No tienes permisos para esta acción');
        }

        $proyecto = Proyecto::find($id);
        if(!$proyecto){
            return redirect()->back()->with('error', 'Proyecto no encontrado');
        }

        $proyecto->estado = 'cancelado';
        $proyecto->save();

        return redirect()->route('aprobaciones.dashboard')->with('success', 'Proyecto rechazado y marcado como cancelado');
    }
}
