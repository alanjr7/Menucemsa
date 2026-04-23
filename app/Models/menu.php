<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    /** @use HasFactory<\Database\Factories\MenuFactory> */
    use HasFactory;
    protected $guarded = [];

    // Relación: Un menú puede tener submenús (hijos)
    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }

    public function canBeSeenBy($user): bool
    {
        if (!$this->roles || $user->isAdmin()) {
            return true;
        }

        $allowedRoles = array_map('trim', explode(',', $this->roles));

        return in_array($user->role, $allowedRoles);
    }
}
