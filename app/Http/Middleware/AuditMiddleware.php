<?php

namespace App\Http\Middleware;

use App\Services\ActivityLogService;
use Closure;
use Illuminate\Http\Request;

class AuditMiddleware
{
    protected $auditableRoutes = [
        'reception.*' => 'recepcion',
        'quirofano.*' => 'quirofano',
        'caja.*' => 'caja',
        'farmacia.*' => 'farmacia',
        'uti.*' => 'uti',
        'medico.*' => 'consulta_externa',
        'consulta.*' => 'consulta_externa',
        'admin.*' => 'administracion',
        'seguridad.*' => 'seguridad',
        'emergencias.*' => 'emergencia',
        'emergency.*' => 'emergencia',
        'internacion.*' => 'internacion',
    ];

    protected $excludedRoutes = [
        'login',
        'logout',
        'profile.*',
        'api.*',
        'dashboard',
        'patients.index',
        'patients.show',
    ];

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if ($this->shouldAudit($request)) {
            $this->logRequest($request, $response);
        }

        return $response;
    }

    protected function shouldAudit(Request $request): bool
    {
        if (!$request->user()) {
            return false;
        }

        $method = $request->method();
        if (!in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return false;
        }

        $routeName = $request->route()?->getName();
        if (!$routeName) {
            return false;
        }

        foreach ($this->excludedRoutes as $excluded) {
            if ($this->routeMatches($routeName, $excluded)) {
                return false;
            }
        }

        return true;
    }

    protected function logRequest(Request $request, $response): void
    {
        $routeName = $request->route()->getName();
        $module = $this->getModuleForRoute($routeName);
        $action = $this->getActionFromMethod($request->method());
        $description = $this->buildDescription($request, $module, $action);

        ActivityLogService::log(
            $action . ($module ? '_' . $module : ''),
            $description,
            null,
            null,
            $this->sanitizeInput($request->all())
        );
    }

    protected function getModuleForRoute(string $routeName): ?string
    {
        foreach ($this->auditableRoutes as $pattern => $module) {
            if ($this->routeMatches($routeName, $pattern)) {
                return $module;
            }
        }
        return null;
    }

    protected function routeMatches(string $routeName, string $pattern): bool
    {
        $pattern = str_replace('.', '\.', $pattern);
        $pattern = str_replace('*', '.*', $pattern);
        return preg_match('/^' . $pattern . '$/', $routeName) === 1;
    }

    protected function getActionFromMethod(string $method): string
    {
        $actions = [
            'POST' => 'create',
            'PUT' => 'update',
            'PATCH' => 'update',
            'DELETE' => 'delete',
        ];
        return $actions[$method] ?? 'action';
    }

    protected function buildDescription(Request $request, ?string $module, string $action): string
    {
        $user = $request->user()?->name ?? 'Sistema';
        $route = $request->route()->getName();
        $moduleLabel = $module ? ucfirst($module) : 'Sistema';

        return "{$user} realizó {$action} en {$moduleLabel} ({$route})";
    }

    protected function sanitizeInput(array $input): array
    {
        $sensitiveFields = ['password', 'password_confirmation', 'remember_token', 'token', 'credit_card'];
        $sanitized = [];

        foreach ($input as $key => $value) {
            if (in_array($key, $sensitiveFields)) {
                $sanitized[$key] = '***REDACTED***';
            } elseif (is_array($value)) {
                $sanitized[$key] = $this->sanitizeInput($value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }
}
