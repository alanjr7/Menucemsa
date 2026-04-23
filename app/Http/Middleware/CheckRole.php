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
public function handle(Request $request, Closure $next, $roles): Response
    {
        if (!auth()->check()) {
            abort(403, 'No tienes permisos.');
        }

        // Separador por | (más seguro que la coma)
        $allowedRoles = explode('|', $roles);
        $userRole = auth()->user()->role;

        if (!in_array($userRole, $allowedRoles)) {
            abort(403, 'No tienes permisos.');
        }

        return $next($request);
    }
}
