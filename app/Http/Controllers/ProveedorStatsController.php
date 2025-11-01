<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProveedorStatsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $proveedor = $user ? $user->proveedor : null;
        if(!$proveedor){
            return view('dashboard.proveedor.proveedor_stats', ['proveedor' => null, 'summary' => null, 'materials' => collect(), 'projects' => collect()]);
        }

        $provId = $proveedor->id_proveedor;

        // Resumen: total revenue and total units sold for this provider
        $summary = DB::table('proyecto_material')
            ->join('materiales','proyecto_material.id_material','materiales.id_material')
            ->where('materiales.id_proveedor',$provId)
            ->selectRaw('COALESCE(SUM(proyecto_material.cantidad * COALESCE(materiales.precio,0)),0) as total_revenue, COALESCE(SUM(proyecto_material.cantidad),0) as total_units, COUNT(DISTINCT proyecto_material.id_proyecto) as projects_count')
            ->first();

        // Per-material aggregation
        $materials = DB::table('proyecto_material')
            ->join('materiales','proyecto_material.id_material','materiales.id_material')
            ->where('materiales.id_proveedor',$provId)
            ->selectRaw('materiales.id_material, materiales.nombre, materiales.precio, COALESCE(SUM(proyecto_material.cantidad),0) as units_sold, COALESCE(SUM(proyecto_material.cantidad * COALESCE(materiales.precio,0)),0) as revenue')
            ->groupBy('materiales.id_material','materiales.nombre','materiales.precio')
            ->orderByDesc('revenue')
            ->get();

        // Projects that bought these materials with totals
        // Nota: la tabla `proyectos` no contiene la columna `ubicacion` en este esquema.
        // Seleccionamos sólo los campos existentes (id y nombre) y agregamos el total gastado.
        // Incluir el campo 'contratista' (existe en el esquema) en lugar de la columna inexistente 'ubicacion'.
        $projects = DB::table('proyecto_material')
            ->join('materiales','proyecto_material.id_material','materiales.id_material')
            ->join('proyectos','proyecto_material.id_proyecto','proyectos.id_proyecto')
            ->where('materiales.id_proveedor',$provId)
            ->selectRaw('proyectos.id_proyecto, proyectos.nombre as proyecto_nombre, proyectos.contratista as contratista, COALESCE(SUM(proyecto_material.cantidad * COALESCE(materiales.precio,0)),0) as total_spent')
            ->groupBy('proyectos.id_proyecto','proyectos.nombre','proyectos.contratista')
            ->orderByDesc('total_spent')
            ->get();

        return view('dashboard.proveedor.proveedor_stats', compact('proveedor','summary','materials','projects'));
    }
}
