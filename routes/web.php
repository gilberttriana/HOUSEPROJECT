<?php
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Middleware\AdminMiddleware;
// Rutas de tu aplicación
Route::get('/', function () {
    return view('Home.Bienvenida');
})->name('bienvenida');

// Ruta del dashboard de usuario
Route::get('/dashboard/usuario', function () {
    return view('Home.dashuser');
})->name('dashuser');

// Rutas para los otros roles
Route::get('/dashboard/admin', function () {
    // Cargar materiales y pasarlos a la vista para mostrar inventario real en el dashboard
    try {
        $materiales = App\Models\Material::all()->map(function($m){
            // usar toArray() para resolver accessors y obtener claves limpias
            $arr = $m->toArray();
            return [
                'nombre' => $arr['nombre'] ?? ($arr['nombre'] ?? '-'),
                'descripcion' => $arr['descripcion'] ?? $arr['DESCRIPCION'] ?? '-',
                'cantidad' => $arr['stock'] ?? $arr['cantidad'] ?? 0,
                // normalizar distintos nombres de columna para la fecha de actualización
                'fecha_actualizacion' => $arr['fecha_actualizacion'] ?? $arr['FECHA_ACTUALIZACION'] ?? $arr['updated_at'] ?? null,
            ];
        });
    } catch (Throwable $e) {
        // Si falla, pasar null y dejar que la vista use los datos de ejemplo
        $materiales = null;
    }
    return view('dashboard.admin2', ['materiales' => $materiales]); 
})->name('admin2.dashboard');

Route::get('/dashboard/dashboardProv', function () {
    return view('dashboard.dashboardProv');
})->name('proveedor.dashboard');

Route::get('/dashboard/maestro', function () {
    return view('dashboard.maestro');
})->name('maestro.dashboard');

// Rutas de autenticación
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/register', [LoginController::class, 'register'])->name('register.post');
Route::post('/dashboard/usuarios', [LoginController::class, 'store'])->name('usuarios.store');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');


Route::get('/dashboard/aprobaciones', function () {
    return view('dashboard.aprobaciones');
})->name('aprobaciones.dashboard');
Route::get('/dashboard/usuarios', [LoginController::class, 'gestionarUsuarios'])->name('usuarios.dashboard');
Route::post('/dashboard/usuarios/cambiar-rol', [LoginController::class, 'cambiarRol'])->name('usuarios.cambiarRol');
Route::get('/dashboard/usuarios/report', [LoginController::class, 'reportarUsuarios'])->name('usuarios.report');
Route::post('/dashboard/usuarios/eliminar', [LoginController::class, 'eliminarUsuario'])->name('usuarios.eliminar');

// Rutas para Proyectos (autenticación requerida)
Route::middleware('auth')->group(function(){
    Route::get('/proyectos', [App\Http\Controllers\ProyectoController::class, 'index'])->name('proyectos.index');
    Route::get('/proyectos/create', [App\Http\Controllers\ProyectoController::class, 'create'])->name('proyectos.create');
    Route::post('/proyectos', [App\Http\Controllers\ProyectoController::class, 'store'])->name('proyectos.store');

    // Rutas para Materiales (autenticación requerida)
    Route::get('/materiales', [App\Http\Controllers\MaterialController::class, 'index'])->name('materiales.index');
    Route::post('/materiales', [App\Http\Controllers\MaterialController::class, 'store'])->name('materiales.store');
    Route::get('/materiales/{id}', [App\Http\Controllers\MaterialController::class, 'show'])->name('materiales.show');
    Route::patch('/materiales/{id}', [App\Http\Controllers\MaterialController::class, 'update'])->name('materiales.update');
    Route::delete('/materiales/{id}', [App\Http\Controllers\MaterialController::class, 'destroy'])->name('materiales.destroy');
    Route::patch('/materiales/{id}/status', [App\Http\Controllers\MaterialController::class, 'changeStatus'])->name('materiales.changeStatus');
});
Route::get('/proveedor/stock', [LoginController::class, 'stock'])->name('proveedor.stock');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');