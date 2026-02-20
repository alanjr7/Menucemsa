<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
public function handle(Request $request, Closure $next, string $roles): Response
{
    if (!auth()->check()) {
        abort(403, 'No tienes permisos para acceder a esta página.');
    }

    // Convertimos "admin,reception" en array real
    $allowedRoles = array_map('trim', explode(',', $roles));
    $userRole = strtolower(trim(auth()->user()->role));

    // Seguridad: si no hay roles definidos, bloquear
    if (empty($allowedRoles)) {
        abort(403, 'Acceso no autorizado.');
    }

    if (!in_array($userRole, $allowedRoles)) {
        abort(403, 'No tienes permisos para acceder a esta página.');
    }

    return $next($request);
}
}
