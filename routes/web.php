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
    return view('dashboard.admin2'); // <--- debe ser 'admin2', no 'admin'
})->name('admin2.dashboard');

Route::get('/dashboard/dashboardProv', function () {
    return view('dashboard.dashboardProv');
})->name('proveedor.dashboard');

Route::get('/dashboard/maestro', function () {
    return view('dashboard.maestro');
})->name('maestro.dashboard');

Route::get('/dashboard/usuarios', function () {
    return view('dashboard.usuarios');
})->name('usuarios.dashboard');

// Rutas de autenticación
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/register', [LoginController::class, 'register'])->name('register.post');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');


Route::get('/dashboard/aprobaciones', function () {
    return view('dashboard.aprobaciones');
})->name('aprobaciones.dashboard');
Route::get('/dashboard/usuarios', [LoginController::class, 'gestionarUsuarios'])->name('usuarios.dashboard');
// Ruta para cambiar rol de un usuario (POST)
Route::post('/dashboard/usuarios/cambiar-rol', [LoginController::class, 'cambiarRol'])->name('usuarios.cambiarRol');
Route::get('/proveedor/stock', [LoginController::class, 'stock'])->name('proveedor.stock');

// En routes/web.php (mejor que GET)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');