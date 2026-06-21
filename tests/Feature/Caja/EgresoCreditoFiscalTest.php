<?php

namespace Tests\Feature\Caja;

use App\Models\Egreso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Crédito fiscal IVA en egresos (Libro de Compras boliviano): al registrar un
 * egreso con factura de compra válida se capturan NIT/N° factura y se calcula
 * el crédito fiscal = 13% del total. Sin factura válida no hay crédito.
 */
class EgresoCreditoFiscalTest extends TestCase
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
            'categoria' => 'insumos_medicos',
            'descripcion' => 'Compra de gasas',
            'monto' => '150.50',
            'metodo_pago' => 'transferencia',
        ], $attrs);
    }

    public function test_egreso_con_credito_fiscal_calcula_iva_13(): void
    {
        $res = $this->actingAs($this->admin())
            ->postJson(route('caja.contabilidad.egresos.store'), $this->payload([
                'con_credito_fiscal' => true,
                'nit_proveedor' => '1023456789',
                'nro_factura' => 'F-001',
                'codigo_autorizacion' => 'CUF123',
            ]));

        $res->assertOk()->assertJson(['success' => true]);

        $egreso = Egreso::firstOrFail();
        $this->assertTrue($egreso->con_credito_fiscal);
        // 150.50 * 0.13 = 19.565 -> half-up -> 19.57
        $this->assertSame('19.57', (string) $egreso->importe_iva);
        $this->assertSame('1023456789', $egreso->nit_proveedor);
        $this->assertSame('F-001', $egreso->nro_factura);
        $this->assertSame('CUF123', $egreso->codigo_autorizacion);
    }

    public function test_egreso_sin_credito_fiscal_no_tiene_iva(): void
    {
        $res = $this->actingAs($this->admin())
            ->postJson(route('caja.contabilidad.egresos.store'), $this->payload([
                'con_credito_fiscal' => false,
                'nit_proveedor' => '999',   // debe limpiarse al no haber factura
                'nro_factura' => 'X',
            ]));

        $res->assertOk()->assertJson(['success' => true]);

        $egreso = Egreso::firstOrFail();
        $this->assertFalse($egreso->con_credito_fiscal);
        $this->assertSame('0.00', (string) $egreso->importe_iva);
        $this->assertNull($egreso->nit_proveedor);
        $this->assertNull($egreso->nro_factura);
    }

    public function test_credito_fiscal_exige_nit_y_numero_de_factura(): void
    {
        $res = $this->actingAs($this->admin())
            ->postJson(route('caja.contabilidad.egresos.store'), $this->payload([
                'con_credito_fiscal' => true,
                // sin nit_proveedor ni nro_factura
            ]));

        $res->assertStatus(422);
        $this->assertSame(0, Egreso::count());
    }
}
