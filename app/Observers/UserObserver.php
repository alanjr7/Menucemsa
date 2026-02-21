<?php

namespace App\Observers;

use App\Models\User;
use App\Services\ActivityLogService;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        ActivityLogService::log('create', "Creó usuario: {$user->name}", $user);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        $changes = $user->getChanges();
        unset($changes['updated_at']);
        
        if (!empty($changes)) {
            ActivityLogService::log(
                'update', 
                "Actualizó usuario: {$user->name}", 
                $user,
                $user->getOriginal(),
                $changes
            );
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        ActivityLogService::log('delete', "Eliminó usuario: {$user->name}", $user);
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        ActivityLogService::log('restore', "Restauró usuario: {$user->name}", $user);
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        ActivityLogService::log('force_delete', "Eliminó permanentemente usuario: {$user->name}", $user);
    }
}
