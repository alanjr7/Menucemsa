<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserStatus
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Si el usuario está autenticado y está inactivo, cerrar sesión y redirigir
        if (Auth::check() && !Auth::user()->is_active) {
            Auth::logout();
            
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')
                ->with('error', 'Tu cuenta ha sido desactivada. Contacta al administrador.');
        }
        
        return $next($request);
    }
}
