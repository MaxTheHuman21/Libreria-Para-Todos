<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if(!Auth::check() || Auth::user()->role !== $role) {
            return redirect('/')->with('error', 'Acceso Denegado! No tienes permisos para ingresar a nuestra pagina');
    }
    return $next($request);
    }
}