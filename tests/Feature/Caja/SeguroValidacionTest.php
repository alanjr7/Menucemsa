<?php

namespace Tests\Feature\Caja;

use App\Models\Seguro;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Sprint 6 — Validaciones/limpieza del maestro de seguros: cobertura+copago = 100%
 * (deriva el copago si falta), tope obligatorio en tope_monto y limpieza de campos
 * que no aplican al tipo.
 */
class SeguroValidacionTest extends TestCase
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

    public function test_porcentaje_deriva_copago_cuando_falta(): void
    {
        $res = $this->actingAs($this->admin())->postJson(route('admin.seguros.store'), [
            'nombre_empresa' => 'ASEG Deriva', 'tipo' => 'privado',
            'tipo_cobertura' => 'porcentaje', 'cobertura_porcentaje' => '70.00',
        ]);

        $res->assertOk()->assertJsonPath('success', true);
        $seguro = Seguro::where('nombre_empresa', 'ASEG Deriva')->firstOrFail();
        $this->assertSame('30.00', (string) $seguro->copago_porcentaje);
    }

    public function test_porcentaje_rechaza_suma_distinta_de_100(): void
    {
        $res = $this->actingAs($this->admin())->postJson(route('admin.seguros.store'), [
            'nombre_empresa' => 'ASEG Mala', 'tipo' => 'privado',
            'tipo_cobertura' => 'porcentaje', 'cobertura_porcentaje' => '70.00', 'copago_porcentaje' => '40.00',
        ]);

        $res->assertStatus(422);
        $this->assertSame(0, Seguro::where('nombre_empresa', 'ASEG Mala')->count());
    }

    public function test_tope_monto_obligatorio(): void
    {
        $res = $this->actingAs($this->admin())->postJson(route('admin.seguros.store'), [
            'nombre_empresa' => 'ASEG Tope', 'tipo' => 'privado', 'tipo_cobertura' => 'tope_monto',
        ]);

        $res->assertStatus(422);
    }

    public function test_solo_consulta_limpia_campos_no_aplicables(): void
    {
        $res = $this->actingAs($this->admin())->postJson(route('admin.seguros.store'), [
            'nombre_empresa' => 'ASEG Consulta', 'tipo' => 'publico',
            'tipo_cobertura' => 'solo_consulta',
            'cobertura_porcentaje' => '50.00', 'tope_monto' => '999.00',
        ]);

        $res->assertOk();
        $seguro = Seguro::where('nombre_empresa', 'ASEG Consulta')->firstOrFail();
        $this->assertNull($seguro->cobertura_porcentaje);
        $this->assertNull($seguro->tope_monto);
    }

    public function test_seguro_sin_formulario_se_crea(): void
    {
        $res = $this->actingAs($this->admin())->postJson(route('admin.seguros.store'), [
            'nombre_empresa' => 'ASEG SinForm', 'tipo' => 'privado',
            'tipo_cobertura' => 'porcentaje', 'cobertura_porcentaje' => '80.00', 'copago_porcentaje' => '20.00',
        ]);

        $res->assertOk()->assertJsonPath('success', true);
        $this->assertSame(1, Seguro::where('nombre_empresa', 'ASEG SinForm')->count());
    }
}
