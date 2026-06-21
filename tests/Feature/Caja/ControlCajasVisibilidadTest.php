<?php

namespace Tests\Feature\Caja;

use App\Models\CajaSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Control de Cajas: una caja ABIERTA es el estado vigente y debe verse siempre,
 * aunque se haya abierto fuera del rango de fechas (sesión que cruza la
 * medianoche). El filtro explícito 'cerrada' sí la excluye.
 */
class ControlCajasVisibilidadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    private function admin(): User
    {
        return User::where('email', 'admin@ClinicaSantaCruz.com')->firstOrFail();
    }

    private function caja(string $estado, $fechaApertura): CajaSession
    {
        return CajaSession::create([
            'user_id' => $this->admin()->id,
            'fecha_apertura' => $fechaApertura,
            'monto_inicial' => 100,
            'estado' => $estado,
        ]);
    }

    public function test_caja_abierta_se_ve_aunque_el_filtro_sea_solo_hoy(): void
    {
        $caja = $this->caja('abierta', now()->subDays(2)); // abierta anteayer

        $res = $this->actingAs($this->admin())->getJson(route('caja.gestion.control-cajas', [
            'fecha_inicio' => now()->toDateString(),
            'fecha_fin' => now()->toDateString(),
            'estado' => 'todas',
        ]));

        $res->assertOk();
        $ids = collect($res->json('cajas.data'))->pluck('id')->all();
        $this->assertContains($caja->id, $ids);
    }

    public function test_filtro_cerrada_no_inyecta_las_abiertas(): void
    {
        // Abierta fuera del rango: solo la inyección 'abierta' podría colarla.
        $abierta = $this->caja('abierta', now()->subDays(2));

        $res = $this->actingAs($this->admin())->getJson(route('caja.gestion.control-cajas', [
            'fecha_inicio' => now()->toDateString(),
            'fecha_fin' => now()->toDateString(),
            'estado' => 'cerrada',
        ]));

        $res->assertOk();
        $ids = collect($res->json('cajas.data'))->pluck('id')->all();
        $this->assertNotContains($abierta->id, $ids);
    }
}
