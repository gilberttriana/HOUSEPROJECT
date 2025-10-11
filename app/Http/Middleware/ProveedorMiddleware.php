<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProveedorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next): Response
    {
       if (!Auth::check() || Auth::user()->rol !== 'proveedor') {
            return redirect()->route('bienvenida')->with('error', 'No autorizado');
        }
        return $next($request);
    }
}
