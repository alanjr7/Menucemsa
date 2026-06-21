<?php

namespace Tests\Feature\Caja;

use App\Models\CuentaCobro;
use App\Models\User;
use App\Models\VentaFarmacia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Débito fiscal IVA en ventas (Libro de Ventas boliviano): el monto cobrado /
 * total de venta incluye IVA 13% por dentro. base_imponible = total;
 * debito_fiscal = total * 13%. Contraparte del crédito fiscal de compras.
 */
class DebitoFiscalIvaTest extends TestCase
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

    public function test_pago_cuenta_calcula_debito_fiscal_13(): void
    {
        $user = User::factory()->create();
        $cuenta = CuentaCobro::create(['tipo_atencion' => 'consulta_externa', 'estado' => 'pendiente']);
        $cuenta->detalles()->create([
            'tipo_item' => 'servicio', 'descripcion' => 'Consulta',
            'cantidad' => '1', 'precio_unitario' => '100.00',
        ]);
        $cuenta->load('detalles');
        $cuenta->recalcularTotales();

        $cuenta->registrarPago(100.00, 'efectivo', null, $user->id, 'k1');

        $pago = $cuenta->pagos()->first();
        $this->assertSame('100.00', (string) $pago->base_imponible);
        $this->assertSame('13.00', (string) $pago->debito_fiscal);
    }

    public function test_venta_farmacia_calcula_debito_fiscal_13(): void
    {
        $user = User::factory()->create();
        $venta = VentaFarmacia::create([
            'codigo_venta' => 'VTF-TEST-1',
            'usuario_id' => $user->id,
            'total' => '226.00',
            'metodo_pago' => 'efectivo',
        ]);

        // 226.00 * 0.13 = 29.38
        $this->assertSame('226.00', (string) $venta->base_imponible);
        $this->assertSame('29.38', (string) $venta->debito_fiscal);
    }

    public function test_resumen_expone_debito_fiscal_y_posicion_iva(): void
    {
        $user = User::factory()->create();
        $cuenta = CuentaCobro::create(['tipo_atencion' => 'consulta_externa', 'estado' => 'pendiente']);
        $cuenta->detalles()->create([
            'tipo_item' => 'servicio', 'descripcion' => 'Consulta',
            'cantidad' => '1', 'precio_unitario' => '100.00',
        ]);
        $cuenta->load('detalles');
        $cuenta->recalcularTotales();
        $cuenta->registrarPago(100.00, 'efectivo', null, $user->id, 'k1');

        $hoy = now()->toDateString();
        $res = $this->actingAs($this->admin())->getJson(route('caja.contabilidad.resumen', [
            'fecha_inicio' => $hoy,
            'fecha_fin' => $hoy,
        ]));

        $res->assertOk();
        $res->assertJsonPath('totales.debito_fiscal', '13.00');
        // Sin compras con crédito fiscal: posición IVA = 13.00 a pagar.
        $res->assertJsonPath('totales.posicion_iva', '13.00');
    }
}
