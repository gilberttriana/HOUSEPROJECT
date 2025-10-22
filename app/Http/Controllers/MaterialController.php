<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Proveedor;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class MaterialController extends Controller
{
    public function index()
    {
        $materiales = Material::orderBy('nombre')->get();

        // Usar los usuarios con rol 'proveedor' como opciones del select
        $proveedoresList = Usuario::where('rol','proveedor')->with('proveedor')->get();
        $options = [];
        foreach($proveedoresList as $u){
            $empresa = trim((string) ($u->proveedor->empresa ?? ''));
            $label = $empresa !== '' ? $empresa . ' (' . ($u->nombre ?? 'Usuario '.$u->id_usuario) . ')' : ($u->nombre . ' ' . ($u->apellido ?? '')) . ' (' . ($u->correo ?? '') . ')';
            // value será el id de usuario (no el id_proveedor)
            $options[] = ['value' => $u->id_usuario, 'label' => $label];
        }

        return view('dashboard.materiales.index', ['materiales' => $materiales, 'proveedores' => $options, 'proveedoresList' => $proveedoresList]);
    }

    public function create()
    {
        // Construir lista de opciones: proveedores existentes y (si faltan) usuarios con rol 'proveedor' sin entrada en proveedores
        $options = [];
        $proveedores = Proveedor::with('usuario')->get();
        foreach($proveedores as $p){
            // Preferir empresa si está definida, sino mostrar nombre del usuario
            $empresa = trim((string) ($p->empresa ?? ''));
            if($empresa !== ''){
                $label = $empresa . ' (' . ($p->usuario->nombre ?? 'Usuario '.$p->usuario_id) . ')';
            } else {
                $label = ($p->usuario->nombre ?? 'Usuario '.$p->usuario_id) . (!empty($p->empresa) ? ' - '.$p->empresa : '');
            }
            $options[] = ['value' => 'prov:'.$p->id_proveedor, 'label' => $label];
        }

        // Usuarios con rol 'proveedor' que no tengan registro en proveedores
        $usuariosSinProv = Usuario::where('rol','proveedor')->whereDoesntHave('proveedor')->get();
        foreach($usuariosSinProv as $u){
            $label = $u->nombre . ' ' . $u->apellido . ' (' . $u->correo . ')';
            $options[] = ['value' => 'user:'.$u->id_usuario, 'label' => $label];
        }

        return view('dashboard.materiales.create', compact('options'));
    }

    public function store(Request $request)
    {
        $request->validate([
        
            'id_proveedor' => 'required|integer',
            'nombre' => 'required|string|max:150',
            'unidad' => 'nullable|string|max:50',
            'precio' => 'required|numeric',
            'stock' => 'nullable|integer',
            'imagen' => 'nullable|image|max:2048',
            'descripcion' => 'nullable|string|max:500'
        ]);

    $usuario = Auth::user();
        if(!$usuario) return redirect()->back()->with('error', 'Autenticación requerida');

        // Solo proveedores y administradores pueden crear materiales
        if(!in_array($usuario->rol, ['proveedor','admin'])){
            return redirect()->back()->with('error', 'No tienes permisos para crear materiales');
        }

            // Resolver id_proveedor desde usuario con rol 'proveedor'
            $userId = intval($request->input('id_proveedor'));
            $user = Usuario::find($userId);
            if(!$user || $user->rol !== 'proveedor'){
                return redirect()->back()->with('error','Usuario seleccionado no es un proveedor válido');
            }
            $prov = $user->proveedor;
            if(!$prov){
                $prov = Proveedor::create([
                    'usuario_id' => $user->id_usuario,
                    'empresa' => trim($user->nombre . ' ' . $user->apellido),
                ]);
            }
            $provId = $prov->id_proveedor;

    $data = $request->only(['nombre','unidad','precio','stock','descripcion']);
        $data['estado'] = $request->input('estado', 'pendiente');
        $data['id_proveedor'] = $provId;
        if($request->hasFile('imagen')){
            $path = $request->file('imagen')->store('materiales','public');
            $data['imagen'] = $path;
        }

        Material::create($data);

        return redirect()->route('materiales.index')->with('success', 'Material creado correctamente');
    }

    // Obtener material (JSON) para edición
    public function show($id)
    {
        $m = Material::find($id);
        if(!$m) return response()->json(['error'=>'No encontrado'],404);
        return response()->json($m);
    }

    // Actualizar material
    public function update(Request $request, $id)
    {
        $m = Material::find($id);
        if(!$m) return redirect()->back()->with('error','Material no encontrado');

        $request->validate([
            'nombre' => 'required|string|max:150',
            'unidad' => 'nullable|string|max:50',
            'precio' => 'required|numeric',
            'stock' => 'nullable|integer',
            'imagen' => 'nullable|image|max:2048',
         
            'id_proveedor' => 'required|integer',
            'estado' => 'nullable|in:pendiente,aprobado,desaprobado',
            'descripcion' => 'nullable|string|max:500'
        ]);

        $userId = intval($request->input('id_proveedor'));
        $user = Usuario::find($userId);
        if(!$user || $user->rol !== 'proveedor'){
            return redirect()->back()->with('error','Usuario seleccionado no es un proveedor válido');
        }
        $prov = $user->proveedor;
        if(!$prov){
            $prov = Proveedor::create([
                'usuario_id' => $user->id_usuario,
                'empresa' => trim($user->nombre . ' ' . $user->apellido),
            ]);
        }

        
        $m->nombre = $request->input('nombre', $m->nombre);
        $m->unidad = $request->input('unidad', $m->unidad);
        $m->precio = $request->input('precio', $m->precio);
        $m->descripcion = $request->input('descripcion', $m->descripcion);
        $m->stock = $request->input('stock', $m->stock);
        if($request->hasFile('imagen')){
            $path = $request->file('imagen')->store('materiales','public');
            $m->imagen = $path;
        }
        // asignar el id_proveedor real
        if($prov) $m->id_proveedor = $prov->id_proveedor;

        
        $m->estado = $request->input('estado', $m->estado);

        $m->save();

        if($request->expectsJson() || $request->ajax()){
            $fresh = $m->fresh();
            $usuarioProv = $prov->usuario ?? null;
            $usuarioId = $usuarioProv ? $usuarioProv->id_usuario : null;
            $label = $prov->empresa ?? ($usuarioProv ? ($usuarioProv->nombre . ' ' . ($usuarioProv->apellido ?? '')) : null);
            $fresh->id_usuario_proveedor = $usuarioId;
            $fresh->proveedor_label = $label;
            return response()->json(['ok' => true, 'material' => $fresh, 'id_usuario_proveedor' => $usuarioId, 'proveedor_label' => $label]);
        }

        return redirect()->route('materiales.index')->with('success','Material actualizado');
    }

    // Eliminar material
    public function destroy($id)
    {
        $m = Material::find($id);
        if(!$m) return response()->json(['error'=>'No encontrado'],404);
        $m->delete();
        return response()->json(['ok'=>true]);
    }

    // Cambiar estado (aprobado/desaprobado/pendiente)
    public function changeStatus(Request $request, $id)
    {
        $m = Material::find($id);
        if(!$m) return response()->json(['error'=>'No encontrado'],404);
        $estado = $request->input('estado');
        if(!in_array($estado, ['pendiente','aprobado','desaprobado'])) return response()->json(['error'=>'Estado inválido'],422);
        $m->estado = $estado;
        $m->save();
        return response()->json(['ok'=>true]);
    }
}
