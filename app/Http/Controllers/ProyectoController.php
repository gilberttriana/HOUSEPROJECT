<?php

namespace App\Http\Controllers;

use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProyectoController extends Controller
{
    public function index()
    {
        $proyectos = Proyecto::orderBy('fecha_creacion', 'desc')->get();
        return view('dashboard.proyectos.index', compact('proyectos'));
    }

    public function create()
    {
        return view('dashboard.proyectos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
        ]);

        $usuario = Auth::user();
        if(!$usuario) return redirect()->back()->with('error', 'Autenticación requerida');

        // Solo admin y clientes ('usuario') pueden crear proyectos
        if(!in_array($usuario->rol, ['admin','usuario'])){
            return redirect()->back()->with('error', 'No tienes permisos para crear proyectos');
        }

        Proyecto::create([
            'id_usuario' => $usuario->id_usuario,
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->route('proyectos.index')->with('success', 'Proyecto creado correctamente');
    }
}
