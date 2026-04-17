<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class menu extends Model
{
    /** @use HasFactory<\Database\Factories\MenuFactory> */
    use HasFactory;
    protected $guarded = [];

    // Relación: Un menú puede tener submenús (hijos)
    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }

    // Verificar si el usuario puede ver este menú
    public function canBeSeenBy($user)
    {
        // Si no requiere roles específicos, o si el usuario es Admin (suponiendo que admin ve todo)
        if (!$this->roles || $user->isAdmin()) {
            return true;
        }

        $allowedRoles = explode(',', $this->roles); // Ej: ['reception', 'enfermera-emergencia']

        foreach ($allowedRoles as $role) {
            $role = trim($role);
            // Convertir guiones a camelCase (enfermera-emergencia → EnfermeraEmergencia)
            $methodRole = str_replace(' ', '', ucwords(str_replace('-', ' ', $role)));
            $method = 'is' . $methodRole; // Construye el nombre del método (Ej: isEnfermeraEmergencia)

            if (method_exists($user, $method) && $user->$method()) {
                return true;
            }
        }

        return false;
    }
}
