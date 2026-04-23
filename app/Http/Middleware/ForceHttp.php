<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForceHttp
{
    public function handle(Request $request, Closure $next)
    {
        // Forzar HTTP en desarrollo
        if (config('app.env') === 'local' && $request->secure()) {
            return redirect()->to($request->url(), 302, [], false);
        }

        return $next($request);
    }
}
