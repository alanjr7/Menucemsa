<?php

namespace App\Policies;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MenuPolicy
{
    // Solo un Admin puede gestionar los menús
    public function viewAny(User $user): bool { return $user->isAdmin(); }
    public function view(User $user, Menu $menu): bool { return $user->isAdmin(); }
    public function create(User $user): bool { return $user->isAdmin(); }
    public function update(User $user, Menu $menu): bool { return $user->isAdmin(); }
    public function delete(User $user, Menu $menu): bool { return $user->isAdmin(); }
}