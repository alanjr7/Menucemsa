<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Policies\ActivityLogPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        ActivityLog::class => ActivityLogPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Definir permisos para la bitácora
        Gate::define('view-activity-logs', function ($user) {
            return $user->isAdmin() || $user->isGerente();
        });
    }
}
