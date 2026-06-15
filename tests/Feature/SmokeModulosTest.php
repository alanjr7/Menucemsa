<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Humo de los módulos clínicos: que cada dashboard/entrada principal
 * responda sin error 500 (no referencia tablas/columnas/modelos eliminados).
 * El rol admin omite ip.access y CheckRole, por lo que aísla fallas de la vista/controlador.
 */
class SmokeModulosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\MenuSeeder::class);
    }

    public static function rutasModulos(): array
    {
        return [
            'admin dashboard'            => ['admin.dashboard'],
            'farmacia'                   => ['farmacia.index'],
            'caja operativa'             => ['caja.operativa.index'],
            'caja gestion'               => ['caja.gestion.index'],
            'contabilidad'               => ['caja.contabilidad.index'],
            'emergency staff'            => ['emergency-staff.dashboard'],
            'internacion staff'          => ['internacion-staff.dashboard'],
            'gerencial'                  => ['gerencial.dashboard'],
            'user management'            => ['user-management.index'],
            'neonato'                    => ['admin.neonato.dashboard'],
            'almacen medicamentos'       => ['admin.almacen-medicamentos.index'],
            'almacen inventario'         => ['admin.almacen-inventario.index'],
            'ajustes pacientes'          => ['admin.ajustes-pacientes.index'],
        ];
    }

    #[DataProvider('rutasModulos')]
    public function test_modulo_no_devuelve_500(string $routeName): void
    {
        if (!\Route::has($routeName)) {
            $this->markTestSkipped("Ruta inexistente: {$routeName}");
        }

        $admin = User::where('email', 'admin@menucemsa.com')->firstOrFail();

        $response = $this->actingAs($admin)->get(route($routeName));

        $this->assertLessThan(
            500,
            $response->status(),
            "La ruta {$routeName} devolvió {$response->status()} (error de servidor)."
        );
    }
}
