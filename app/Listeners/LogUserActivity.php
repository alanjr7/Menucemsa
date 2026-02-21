<?php

namespace App\Listeners;

use App\Services\ActivityLogService;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogUserActivity
{
    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        $action = $event instanceof Login ? 'login' : 'logout';
        $description = $event instanceof Login 
            ? "Usuario {$event->user->name} inició sesión" 
            : "Usuario {$event->user->name} cerró sesión";
            
        ActivityLogService::log($action, $description);
    }
}
