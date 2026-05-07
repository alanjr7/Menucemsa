<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Menu;

class TestMenuCommand extends Command
{
    protected $signature = 'test:menu';
    protected $description = 'Test menu permissions';

    public function handle()
    {
        $user = User::where('role', 'emergencia')->first();
        $menu = Menu::where('name', 'Pacientes')->first();

        $this->info("User role: " . $user->role);
        $this->info("Menu name: " . $menu->name);
        $this->info("Menu roles: " . var_export($menu->roles, true));
        $this->info("Can be seen: " . ($menu->canBeSeenBy($user) ? 'YES' : 'NO'));

        // Test submenu
        $submenu = Menu::where('name', 'Maestro de Pacientes')->first();
        $this->info("Submenu name: " . $submenu->name);
        $this->info("Submenu roles: " . var_export($submenu->roles, true));
        $this->info("Submenu can be seen: " . ($submenu->canBeSeenBy($user) ? 'YES' : 'NO'));

        return 0;
    }
}
