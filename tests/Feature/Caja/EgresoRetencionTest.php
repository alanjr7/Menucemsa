<?php

namespace Tests\Feature\Caja;

use App\Models\Egreso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Retención a honorarios médicos (la clínica como agente de retención): al pagar
 * a un médico sin factura se retiene IUE 12,5% + IT 3% = 15,5% (servicios), el
 * médico cobra el neto y la clínica guarda la retención para declararla.
 */
class EgresoRetencionTest extends TestCase
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

    private function payload(array $attrs = []): array
    {
        return array_merge([
            'fecha' => now()->toDateString(),
            'categoria' => 'honorarios',
            'descripcion' => 'Honorario Dr. Pérez',
            'monto' => '1000.00',
            'metodo_pago' => 'transferencia',
        ], $attrs);
    }

    // --- Cálculo en el modelo ---

    public function test_calcula_retencion_servicios_15_5(): void
    {
        $r = Egreso::calcularRetenciones('1000.00', 'servicios');
        $this->assertSame('125.00', $r['iue']); // 12,5%
        $this->assertSame('30.00', $r['it']);   // 3%
    }

    public function test_calcula_retencion_bienes_8(): void
    {
        $r = Egreso::calcularRetenciones('1000.00', 'bienes');
        $this->assertSame('50.00', $r['iue']); // 5%
        $this->assertSame('30.00', $r['it']);  // 3%
    }

    public function test_accessors_total_y_neto(): void
    {
        $e = new Egreso(['monto' => '1000.00', 'retencion_iue' => '125.00', 'retencion_it' => '30.00']);
        $this->assertSame('155.00', $e->retencion_total);
        $this->assertSame('845.00', $e->neto_pagado);
    }

    // --- Endpoint ---

    public function test_egreso_con_retencion_servicios_guarda_iue_it_y_neto(): void
    {
        $res = $this->actingAs($this->admin())
            ->postJson(route('caja.contabilidad.egresos.store'), $this->payload([
                'aplica_retencion' => true,
                'retencion_tipo' => 'servicios',
                'proveedor' => 'Dr. Juan Pérez',
                'nit_proveedor' => '4567890',
            ]));

        $res->assertOk()->assertJson(['success' => true]);

        $egreso = Egreso::firstOrFail();
        $this->assertTrue($egreso->aplica_retencion);
        $this->assertSame('servicios', $egreso->retencion_tipo);
        $this->assertSame('125.00', (string) $egreso->retencion_iue);
        $this->assertSame('30.00', (string) $egreso->retencion_it);
        $this->assertSame('155.00', $egreso->retencion_total);
        $this->assertSame('845.00', $egreso->neto_pagado);
        // La retención excluye el crédito fiscal.
        $this->assertFalse($egreso->con_credito_fiscal);
        $this->assertSame('0.00', (string) $egreso->importe_iva);
    }

    public function test_retencion_exige_tipo_proveedor_y_nit(): void
    {
        $res = $this->actingAs($this->admin())
            ->postJson(route('caja.contabilidad.egresos.store'), $this->payload([
                'aplica_retencion' => true,
                // sin retencion_tipo / proveedor / nit_proveedor
            ]));

        $res->assertStatus(422);
        $this->assertSame(0, Egreso::count());
    }

    public function test_retencion_y_credito_fiscal_son_excluyentes(): void
    {
        // Si llegan ambos, la retención gana y el crédito fiscal se anula.
        $res = $this->actingAs($this->admin())
            ->postJson(route('caja.contabilidad.egresos.store'), $this->payload([
                'aplica_retencion' => true,
                'retencion_tipo' => 'servicios',
                'proveedor' => 'Dr. Juan Pérez',
                'nit_proveedor' => '4567890',
                'con_credito_fiscal' => true,
                'nro_factura' => 'F-001',
            ]));

        $res->assertOk()->assertJson(['success' => true]);

        $egreso = Egreso::firstOrFail();
        $this->assertTrue($egreso->aplica_retencion);
        $this->assertFalse($egreso->con_credito_fiscal);
        $this->assertSame('0.00', (string) $egreso->importe_iva);
        $this->assertSame('155.00', $egreso->retencion_total);
    }

    public function test_comprobante_de_retencion_se_genera(): void
    {
        $this->actingAs($this->admin())
            ->postJson(route('caja.contabilidad.egresos.store'), $this->payload([
                'aplica_retencion' => true,
                'retencion_tipo' => 'servicios',
                'proveedor' => 'Dr. Juan Pérez',
                'nit_proveedor' => '4567890',
            ]))->assertOk();

        $egreso = Egreso::firstOrFail();

        $res = $this->actingAs($this->admin())
            ->get(route('caja.contabilidad.egresos.comprobante-retencion', $egreso->id));

        $res->assertOk();
        $res->assertSee('COMPROBANTE DE RETENCIÓN', false);
        $res->assertSee('Dr. Juan Pérez');
    }
}
