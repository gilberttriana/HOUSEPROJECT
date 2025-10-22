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
                'rol'        => 'usuario', 
            ]);

            Auth::login($usuario);

            $redirectRoute = $this->redirectToRole($usuario->rol);

            return response()->json([
                'success'  => true,
                'redirect' => $redirectRoute,
                'message'  => 'Usuario registrado con éxito.'
            ]);

        } catch (ValidationException $e) {
           
            $errors = $e->validator->errors()->all();
            return response()->json([
                'success' => false,
                'message' => $errors[0] ?? 'Error en la validación.'
            ], 422);
        } catch (\Exception $e) {
            
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
    // Vista para gestionar usuarios (solo admin)
  public function gestionarUsuarios(Request $request)
  {
    $roles = ['usuario', 'proveedor', 'maestro', 'admin'];
    $usuariosPorRol = [];
    $q = trim($request->input('q', ''));

    foreach ($roles as $rol) {
        $query = Usuario::where('rol', $rol);
        if ($q !== '') {
            $query->where(function($sub) use ($q) {
                $sub->where('nombre', 'like', "%{$q}%")
                    ->orWhere('apellido', 'like', "%{$q}%")
                    ->orWhere('correo', 'like', "%{$q}%");
            });
        }
        $usuariosPorRol[$rol] = $query->get();
    }

    return view('dashboard.usuarios', compact('usuariosPorRol', 'roles'));
}

    // Cambiar el rol de un usuario (solo admin)
    public function cambiarRol(Request $request)
    {
        $request->validate([
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

       
        $adminCount = Usuario::where('rol', 'admin')->count();
        if ($usuario->rol === 'admin' && $request->rol !== 'admin' && $adminCount <= 1) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No se puede eliminar el último administrador.'], 403);
            }
            return redirect()->back()->with('error', 'No se puede eliminar el último administrador.');
        }

    
        if ($actor && $usuario->id_usuario === $actor->id_usuario && $request->rol !== 'admin') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No puedes cambiar tu propio rol a uno sin privilegios.'], 403);
            }
            return redirect()->back()->with('error', 'No puedes cambiar tu propio rol a uno sin privilegios.');
        }

        $usuario->rol = $request->rol;
        $usuario->save();

      
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Rol actualizado correctamente.', 'usuario_id' => $usuario->id_usuario, 'rol' => $usuario->rol]);
        }

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

    // Generar reporte PDF de usuarios
    public function reportarUsuarios(Request $request)
    {
        // Solo admin
        $actor = Auth::user();
        if (!$actor || $actor->rol !== 'admin') {
            abort(403, 'No autorizado');
        }

        $roleFilter = $request->input('role', 'all');
        $from = $request->input('from');
        $to = $request->input('to');

        $q = Usuario::query();
        if ($roleFilter && $roleFilter !== 'all') {
            $q->where('rol', $roleFilter);
        }
        if ($from) {
            $q->where('fecha_registro', '>=', $from);
        }
        if ($to) {
            $q->where('fecha_registro', '<=', $to);
        }

        $usuarios = $q->orderBy('rol')->orderBy('nombre')->get(['nombre','apellido','correo','rol','fecha_registro']);

        // Si está disponible la fachada de barryvdh/laravel-dompdf, úsala
        if (class_exists('\\Barryvdh\\DomPDF\\Facade\\Pdf')) {
            $pdfClass = '\\Barryvdh\\DomPDF\\Facade\\Pdf';
            $pdf = $pdfClass::loadView('dashboard.usuarios_report', compact('usuarios'));
            return $pdf->download('usuarios_report_' . now()->format('Ymd_His') . '.pdf');
        }

        // Fallback: devolver la vista para que el navegador pueda imprimir/guardar como PDF
        return response()->view('dashboard.usuarios_report', compact('usuarios'))
            ->header('Content-Disposition', 'attachment; filename="usuarios_report_' . now()->format('Ymd_His') . '.html"');
    }

    // Eliminar usuario (solo admin)
    public function eliminarUsuario(Request $request)
    {
        $request->validate([
            'usuario_id' => 'required|integer|exists:usuarios,id_usuario',
        ]);

        $actor = Auth::user();
        if (!$actor || $actor->rol !== 'admin') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No autorizado.'], 403);
            }
            return redirect()->back()->with('error', 'No autorizado.');
        }

        $usuario = Usuario::find($request->usuario_id);
        if (!$usuario) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Usuario no encontrado.'], 404);
            }
            return redirect()->back()->with('error', 'Usuario no encontrado.');
        }

        // No permitir eliminarse a sí mismo
        if ($actor->id_usuario === $usuario->id_usuario) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No puedes eliminar tu propia cuenta.'], 403);
            }
            return redirect()->back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        // No permitir eliminar al último admin
        if ($usuario->rol === 'admin') {
            $adminCount = Usuario::where('rol', 'admin')->count();
            if ($adminCount <= 1) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'No se puede eliminar al último administrador.'], 403);
                }
                return redirect()->back()->with('error', 'No se puede eliminar al último administrador.');
            }
        }

        // Proceder a eliminar
        try {
            $usuario->delete();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Usuario eliminado correctamente.', 'usuario_id' => $usuario->id_usuario]);
            }
            return redirect()->back()->with('success', 'Usuario eliminado correctamente.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error al eliminar usuario.'], 500);
            }
            return redirect()->back()->with('error', 'Error al eliminar usuario.');
        }
    }

}
