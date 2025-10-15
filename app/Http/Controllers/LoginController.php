<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    // Registro de nuevo usuario
    public function register(Request $request)
    {
        try {
            $request->validate([
                'nombre'     => 'required|string|max:100',
                'apellido'   => 'required|string|max:255',
                'correo'     => 'required|email|unique:usuarios,correo',
                'contrasena' => 'required|string|min:6',
            ]);

            $usuario = Usuario::create([
                'nombre'     => $request->nombre,
                'apellido'   => $request->apellido,
                'correo'     => $request->correo,
                'contrasena' => Hash::make($request->contrasena),
                'rol'        => 'usuario', // Rol por defecto
            ]);

            Auth::login($usuario);

            $redirectRoute = $this->redirectToRole($usuario->rol);

            return response()->json([
                'success'  => true,
                'redirect' => $redirectRoute,
                'message'  => 'Usuario registrado con éxito.'
            ]);

        } catch (ValidationException $e) {
            // Devuelve el primer error de validación como JSON
            $errors = $e->validator->errors()->all();
            return response()->json([
                'success' => false,
                'message' => $errors[0] ?? 'Error en la validación.'
            ], 422);
        } catch (\Exception $e) {
            // Otros errores inesperados
            return response()->json([
                'success' => false,
                'message' => 'Error en el servidor.'
            ], 500);
        }
    }

    // Lógica para iniciar sesión
    public function login(Request $request)
    {
        $request->validate([
            'correo'     => 'required|email',
            'contrasena' => 'required|string',
        ]);

        $usuario = Usuario::where('correo', $request->correo)->first();

        if ($usuario && Hash::check($request->contrasena, $usuario->contrasena)) {
            Auth::login($usuario);

            $redirectRoute = $this->redirectToRole($usuario->rol);

            return response()->json([
                'success'  => true,
                'redirect' => $redirectRoute,
                'message'  => 'Inicio de sesión correcto.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Credenciales inválidas.'
        ], 401);
    }

    // Cerrar sesión
    public function logout(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('bienvenida');
}

    // Determina la ruta de redirección según el rol del usuario
    private function redirectToRole($rol)
    {
        switch ($rol) {
            case 'admin':
                return route('admin2.dashboard');
            case 'proveedor':
                return route('proveedor.dashboard');
            case 'maestro':
                return route('maestro.dashboard');
            default:
                return route('dashuser');
        }
    }
 public function gestionarUsuarios()
{
    $roles = ['usuario', 'proveedor', 'maestro', 'admin'];
    $usuariosPorRol = [];
    foreach ($roles as $rol) {
        $usuariosPorRol[$rol] = Usuario::where('rol', $rol)->get();
    }
    return view('dashboard.usuarios', compact('usuariosPorRol', 'roles'));
}

    // Cambiar el rol de un usuario (solo admin)
    public function cambiarRol(Request $request)
    {
        $request->validate([
            // la tabla usa id_usuario como primary key
            'usuario_id' => 'required|integer|exists:usuarios,id_usuario',
            'rol' => 'required|string|in:usuario,proveedor,maestro,admin',
        ]);

        $actor = Auth::user();
        if (!$actor || $actor->rol !== 'admin') {
            return response()->json(['success' => false, 'message' => 'No autorizado.'], 403);
        }

        $usuario = Usuario::find($request->usuario_id);
        if (!$usuario) {
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado.'], 404);
        }

        $usuario->rol = $request->rol;
        $usuario->save();

        return redirect()->back()->with('success', 'Rol actualizado correctamente.');
    }

    // Almacenar nuevo usuario desde el dashboard (solo admin puede asignar rol distinto de 'usuario')
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:255',
            'correo' => 'required|email|unique:usuarios,correo',
            'rol' => 'required|string|in:usuario,proveedor,maestro,admin',
            'contrasena' => 'required|string|min:6|confirmed',
        ]);

        $actor = Auth::user();
        // Si el actor no es admin, forzamos rol 'usuario' para seguridad
        $rol = ($actor && $actor->rol === 'admin') ? $request->rol : 'usuario';

        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'correo' => $request->correo,
            'contrasena' => Hash::make($request->contrasena),
            'rol' => $rol,
        ]);

        return redirect()->back()->with('success', 'Usuario creado correctamente.');
    }

}
