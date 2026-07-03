<?php

namespace Tests\Feature\Caja;

use App\Exports\RcvResumenImpuestosSheet;
use App\Exports\RcvVentasSheet;
use App\Models\CuentaCobro;
use App\Models\Devolucion;
use App\Models\PagoCuenta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Efecto contable/fiscal de las devoluciones (NC): el resumen de contabilidad,
 * el Libro de Ventas IVA y el resumen de impuestos netean el contra-ingreso
 * (ingresos, débito fiscal, bases IT/IUE) sin tocar el pago original.
 */
class DevolucionContabilidadTest extends TestCase
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

    /** Cobra una cuenta nueva y devuelve el pago creado. */
    private function cobrar(string $precio): PagoCuenta
    {
        $cuenta = CuentaCobro::create(['tipo_atencion' => 'consulta_externa', 'estado' => 'pendiente']);
        $cuenta->detalles()->create([
            'tipo_item' => 'servicio', 'descripcion' => 'Consulta',
            'cantidad' => '1', 'precio_unitario' => $precio,
        ]);
        $cuenta->load('detalles');
        $cuenta->recalcularTotales();
        $cuenta->registrarPago((float) $precio, 'efectivo', null, $this->admin()->id, 'k-'.uniqid());

        return $cuenta->pagos()->first();
    }

    private function devolver(PagoCuenta $pago, string $monto): Devolucion
    {
        return Devolucion::registrarPara($pago, [
            'monto' => $monto,
            'metodo_devolucion' => 'efectivo',
            'motivo' => 'Ajuste de prueba',
            'user_id' => $this->admin()->id,
        ]);
    }

    private function resumenHoy(): \Illuminate\Testing\TestResponse
    {
        $hoy = now()->toDateString();

        return $this->actingAs($this->admin())->getJson(route('caja.contabilidad.resumen', [
            'fecha_inicio' => $hoy,
            'fecha_fin' => $hoy,
        ]));
    }

    public function test_resumen_neta_ingresos_debito_y_bases(): void
    {
        $pago = $this->cobrar('200.00');
        $this->devolver($pago, '50.00');

        $res = $this->resumenHoy();

        $res->assertOk();
        // Ingresos netos = 200 − 50; el pago original queda íntegro en BD
        $res->assertJsonPath('totales.ingresos', '150.00');
        $res->assertJsonPath('totales.ingresos_caja', '150.00');
        $res->assertJsonPath('totales.devoluciones', '50.00');
        // Débito fiscal neto = 13% de 200 − 13% de 50 = 26 − 6.50
        $res->assertJsonPath('totales.debito_fiscal', '19.50');
        $res->assertJsonPath('totales.posicion_iva', '19.50');
        // Bases IT/IUE sobre ventas netas
        $res->assertJsonPath('totales.ventas_brutas', '150.00');
        $res->assertJsonPath('totales.it_3', '4.50');
        $res->assertJsonPath('totales.saldo', '150.00');
        // Por método: la devolución en efectivo sale del efectivo
        $res->assertJsonPath('ingresos_por_metodo.efectivo', '150.00');
        // La NC aparece en el listado propio
        $res->assertJsonCount(1, 'devoluciones');
        $res->assertJsonPath('devoluciones.0.monto', '50.00');
    }

    public function test_nc_anulada_no_resta_del_resumen(): void
    {
        $pago = $this->cobrar('200.00');
        $nc = $this->devolver($pago, '50.00');
        $nc->anular($this->admin()->id, 'registrada por error');

        $res = $this->resumenHoy();

        $res->assertOk();
        $res->assertJsonPath('totales.ingresos', '200.00');
        $res->assertJsonPath('totales.devoluciones', '0'); // reduce vacío (misma convención del resto de totales)
        $res->assertJsonPath('totales.debito_fiscal', '26.00');
        // Sigue listada (marcada anulada) para la auditoría
        $res->assertJsonCount(1, 'devoluciones');
        $res->assertJsonPath('devoluciones.0.anulado', true);
    }

    public function test_libro_ventas_iva_incluye_nc_en_negativo(): void
    {
        $pago = $this->cobrar('200.00');
        $nc = $this->devolver($pago, '50.00');

        $sheet = new RcvVentasSheet(now()->startOfDay(), now()->endOfDay());
        $rows = $sheet->collection();

        $filaNc = $rows->firstWhere('origen', 'Nota Crédito');
        $this->assertNotNull($filaNc, 'El Libro de Ventas debe incluir la fila de la NC');
        $this->assertStringContainsString($nc->id, $filaNc['recibo']);
        $this->assertStringContainsString($pago->id, $filaNc['recibo']);
        $this->assertSame(-50.0, $filaNc['total']);
        $this->assertSame(-6.5, $filaNc['debito']);

        // La fila del pago original sigue íntegra (positiva)
        $filaPago = $rows->firstWhere('origen', 'Caja');
        $this->assertSame(200.0, $filaPago['total']);
    }

    public function test_resumen_impuestos_rcv_netea_ventas_y_debito(): void
    {
        $pago = $this->cobrar('200.00');
        $this->devolver($pago, '50.00');

        $sheet = new RcvResumenImpuestosSheet(now()->startOfDay(), now()->endOfDay());
        $filas = collect($sheet->array());

        $ventasNetas = $filas->firstWhere(0, 'Total ventas netas (ventas − devoluciones)');
        $this->assertSame('150.00', $ventasNetas[1]);

        $nc = $filas->firstWhere(0, 'Devoluciones / Notas de Crédito del período');
        $this->assertSame('50.00', $nc[1]);

        $debito = $filas->firstWhere(0, 'Débito fiscal IVA (13%, neto de NC)');
        $this->assertSame('19.50', $debito[1]);

        $it = $filas->firstWhere(0, 'IT (3%) (F-400)');
        $this->assertSame('4.50', $it[1]);
    }

    public function test_devolucion_sin_cobros_del_dia_da_serie_negativa(): void
    {
        // Pago de ayer, devolución hoy: el día de hoy solo tiene el contra-ingreso.
        $pago = $this->cobrar('100.00');
        PagoCuenta::whereKey($pago->id)->update(['created_at' => now()->subDay()]);
        $this->devolver($pago->fresh(), '40.00');

        $res = $this->resumenHoy();

        $res->assertOk();
        $res->assertJsonPath('totales.ingresos', '-40.00');
        $res->assertJsonPath('totales.devoluciones', '40.00');
    }
}
