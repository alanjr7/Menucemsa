<?php

namespace App\Http\Middleware;

use App\Models\AllowedIp;
use App\Models\IpAccessSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckIpAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        if ($user->role === 'admin') {
            return $next($request);
        }

        if (!IpAccessSetting::isSpecificMode()) {
            return $next($request);
        }

        $clientIp = $this->getClientIp($request);

        if (!AllowedIp::isIpAllowed($clientIp)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Usted no está en la clínica. Intente ingresar desde una ubicación autorizada o comuníquese con soporte.');
        }

        return $next($request);
    }

    private function getClientIp(Request $request): string
    {
        $ipHeaders = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($ipHeaders as $header) {
            if ($request->server->has($header)) {
                $ips = explode(',', $request->server->get($header));
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
                if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                    return $ip;
                }
            }
        }

        return $request->ip() ?? '0.0.0.0';
    }
}
