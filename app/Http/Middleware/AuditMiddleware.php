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

    /** Etiquetas legibles de módulo para el texto genérico de respaldo. */
    protected $moduleLabels = [
        'recepcion' => 'Recepción',
        'quirofano' => 'Quirófano',
        'caja' => 'Caja',
        'farmacia' => 'Farmacia',
        'uti' => 'UTI',
        'consulta_externa' => 'Consulta Externa',
        'administracion' => 'Administración',
        'seguridad' => 'Seguridad',
        'emergencia' => 'Emergencia',
        'internacion' => 'Internación',
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

        // POST de solo lectura (búsquedas, filtros, previsualizaciones): no son acciones.
        if (in_array($routeName, config('audit.ignore', []), true)) {
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

        // Solo se guarda la acción legible; el payload del request no se persiste.
        ActivityLogService::log(
            $action . ($module ? '_' . $module : ''),
            $this->buildDescription($routeName, $module, $action)
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

    /**
     * Frase legible de la acción (sin el usuario; la vista ya lo antepone).
     * Usa el mapa de config/audit.php y, si la ruta no está, un texto genérico
     * que no expone el nombre técnico de la ruta.
     */
    protected function buildDescription(string $routeName, ?string $module, string $action): string
    {
        $map = config('audit.descriptions', []);
        if (isset($map[$routeName])) {
            return $map[$routeName];
        }

        $verbo = [
            'create' => 'registró un elemento',
            'update' => 'actualizó un elemento',
            'delete' => 'eliminó un elemento',
        ][$action] ?? 'realizó una acción';

        $moduleLabel = $module ? ($this->moduleLabels[$module] ?? ucfirst($module)) : null;

        return $moduleLabel ? "{$verbo} en {$moduleLabel}" : $verbo;
    }
}
