<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica si el usuario está logueado y es admin
        if (!Auth::check() || Auth::user()->rol !== 'admin') {
            abort(403, 'No autorizado');
        }

        return $next($request);
    }
}

