<?php

namespace Tests\Feature\Caja;

use App\Models\CuentaCobro;
use App\Models\Egreso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Registro de Compras y Ventas (RCV) + bases IT/IUE: el resumen expone las bases
 * tributarias y el export arma las tres hojas (Ventas IVA, Compras IVA, Resumen).
 */
class RcvExportTest extends TestCase
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

    private function cobrar(string $precio): void
    {
        $cuenta = CuentaCobro::create(['tipo_atencion' => 'consulta_externa', 'estado' => 'pendiente']);
        $cuenta->detalles()->create([
            'tipo_item' => 'servicio', 'descripcion' => 'Consulta',
            'cantidad' => '1', 'precio_unitario' => $precio,
        ]);
        $cuenta->load('detalles');
        $cuenta->recalcularTotales();
        $cuenta->registrarPago((float) $precio, 'efectivo', null, $this->admin()->id, 'k-'.uniqid());
    }

    public function test_resumen_expone_bases_it_iue(): void
    {
        $this->cobrar('100.00');

        $hoy = now()->toDateString();
        $res = $this->actingAs($this->admin())->getJson(route('caja.contabilidad.resumen', [
            'fecha_inicio' => $hoy,
            'fecha_fin' => $hoy,
        ]));

        $res->assertOk();
        $res->assertJsonPath('totales.it_3', '3.00');             // 3% de 100
        $res->assertJsonPath('totales.utilidad_estimada', '100.00'); // sin egresos
        $res->assertJsonPath('totales.iue_estimado', '25.00');    // 25% de 100
    }

    public function test_iue_estimado_cero_si_utilidad_negativa(): void
    {
        // Egreso mayor a los ingresos → utilidad negativa → IUE estimado 0.
        Egreso::create([
            'fecha' => now()->toDateString(), 'categoria' => 'alquiler', 'descripcion' => 'Alquiler',
            'monto' => '500.00', 'metodo_pago' => 'efectivo', 'user_id' => $this->admin()->id,
        ]);
        $this->cobrar('100.00');

        $hoy = now()->toDateString();
        $res = $this->actingAs($this->admin())->getJson(route('caja.contabilidad.resumen', [
            'fecha_inicio' => $hoy, 'fecha_fin' => $hoy,
        ]));

        $res->assertOk();
        $res->assertJsonPath('totales.iue_estimado', '0.00');
    }

    public function test_exporta_rcv_xlsx(): void
    {
        // Una venta (pago) y una compra con crédito fiscal → las 3 hojas tienen datos.
        $this->cobrar('226.00');
        Egreso::create([
            'fecha' => now()->toDateString(), 'categoria' => 'insumos_medicos', 'descripcion' => 'Gasas',
            'monto' => '226.00', 'metodo_pago' => 'transferencia', 'user_id' => $this->admin()->id,
            'con_credito_fiscal' => true, 'nit_proveedor' => '1023456789', 'nro_factura' => 'F-001',
            'importe_iva' => '29.38',
        ]);

        $hoy = now()->toDateString();
        $res = $this->actingAs($this->admin())->get(route('caja.contabilidad.exportar-rcv', [
            'fecha_inicio' => $hoy, 'fecha_fin' => $hoy,
        ]));

        $res->assertOk();
        $this->assertStringContainsString(
            'spreadsheetml',
            (string) $res->headers->get('content-type')
        );
    }
}
