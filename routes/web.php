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
    // Cargar materiales y proyectos y pasarlos a la vista para mostrar inventario y proyectos en el dashboard
    try {
        // Eager-load proveedor y usuario; mapear a formato ligero para la vista.
        $materiales = App\Models\Material::with('proveedor.usuario')->orderBy('nombre')->get()->map(function($m){
            $prov = $m->proveedor ?? null;
            $provUser = $prov && isset($prov->usuario) ? $prov->usuario : null;
            $empresa = trim((string) ($prov->empresa ?? ''));
            $provLabel = null;
            if($prov){
                $provLabel = $empresa !== '' ? $empresa . ($provUser ? ' (' . ($provUser->nombre ?? 'Usuario '.$prov->usuario_id) . ')' : '') : ($provUser ? (($provUser->nombre ?? '') . ' ' . ($provUser->apellido ?? '')) : null);
            }

            // intentar resolver URL de imagen primaria de forma segura
            $imgUrl = null;
            try{
                $rawImg = $m->imagen ?? null;
                if (!empty($rawImg)){
                    $dec = json_decode($rawImg, true);
                    if(is_array($dec) && count($dec)) $first = $dec[0]; else $first = $rawImg;
                    if(preg_match('#^https?://#i', $first)) $imgUrl = $first;
                    elseif(\Illuminate\Support\Facades\Storage::disk('public')->exists($first)) $imgUrl = \Illuminate\Support\Facades\Storage::url($first);
                    elseif(strpos($first, 'materiales/') === 0) $imgUrl = asset('storage/' . ltrim($first, '/'));
                } else {
                    $imgUrl = $m->primary_image_url ?? null;
                }
            }catch(\Throwable $e){ $imgUrl = null; }

            return [
                'id' => $m->id_material ?? $m->id ?? null,
                'nombre' => $m->nombre ?? '-',
                'descripcion' => $m->descripcion ?? $m->DESCRIPCION ?? '-',
                'cantidad' => intval($m->stock ?? $m->cantidad ?? 0),
                'estado' => $m->estado ?? $m->ESTADO ?? 'pendiente',
                'fecha_actualizacion' => $m->updated_at ?? null,
                'proveedor_label' => $provLabel,
                'id_proveedor' => $prov->id_proveedor ?? null,
                'id_usuario_proveedor' => $provUser->id_usuario ?? null,
                'imagen' => $m->imagen ?? null,
                'primary_image_url' => $imgUrl,
            ];
        });
    } catch (Throwable $e) {
        // Si algo falla al mapear, en lugar de devolver null usamos la colección cruda para evitar mostrar datos de ejemplo.
        try{
            $materiales = App\Models\Material::orderBy('nombre')->get();
        } catch (Throwable $e2){
            $materiales = collect();
        }
    }

    try{
        $proyectos = App\Models\Proyecto::with('materiales')->get();
    } catch (Throwable $e){
        $proyectos = collect();
    }

    // lista de materiales disponibles (por defecto, con stock > 0)
    try{
        $materialsList = App\Models\Material::where('stock','>',0)->get();
    } catch (Throwable $e){
        $materialsList = collect();
    }

    return view('dashboard.admin2', ['materiales' => $materiales, 'proyectos' => $proyectos, 'materialsList' => $materialsList]); 
})->name('admin2.dashboard');

use Illuminate\Http\Request;

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


use App\Models\Proyecto;

Route::get('/dashboard/aprobaciones', function () {
    // Mostrar proyectos que requieren aprobación: sin estado o en espera/pendiente
    try{
        $proyectos = Proyecto::with('materiales')->where(function($q){
            $q->whereNull('estado')
              ->orWhere('estado','')
              ->orWhere('estado','En Espera')
              ->orWhere('estado','En espera')
              ->orWhere('estado','Pendiente')
              ->orWhere('estado','pendiente');
        })->get();
    } catch (Throwable $e){
        $proyectos = collect();
    }

    return view('dashboard.aprobaciones', compact('proyectos'));
})->name('aprobaciones.dashboard');

// Rutas para aprobar/rechazar proyectos desde el dashboard de aprobaciones
Route::post('/proyectos/{id}/aprobar', [App\Http\Controllers\ProyectoController::class, 'aprobar'])->name('proyectos.aprobar');
Route::post('/proyectos/{id}/rechazar', [App\Http\Controllers\ProyectoController::class, 'rechazar'])->name('proyectos.rechazar');
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
    Route::get('/materiales/json-all', [App\Http\Controllers\MaterialController::class, 'allJson'])->name('materiales.allJson');
    Route::post('/materiales', [App\Http\Controllers\MaterialController::class, 'store'])->name('materiales.store');
    Route::get('/materiales/{id}', [App\Http\Controllers\MaterialController::class, 'show'])->name('materiales.show');
    Route::patch('/materiales/{id}', [App\Http\Controllers\MaterialController::class, 'update'])->name('materiales.update');
    Route::delete('/materiales/{id}', [App\Http\Controllers\MaterialController::class, 'destroy'])->name('materiales.destroy');
    Route::patch('/materiales/{id}/status', [App\Http\Controllers\MaterialController::class, 'changeStatus'])->name('materiales.changeStatus');
    // Estadísticas para proveedores
    Route::get('/proveedor/stats', [App\Http\Controllers\ProveedorStatsController::class, 'index'])->name('proveedor.stats');
});
Route::get('/proveedor/stock', [LoginController::class, 'stock'])->name('proveedor.stock');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');