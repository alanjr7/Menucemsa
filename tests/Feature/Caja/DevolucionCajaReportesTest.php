<?php

namespace Tests\Feature\Caja;

use App\Models\CajaSession;
use App\Models\CuentaCobro;
use App\Models\Devolucion;
use App\Models\MovimientoCaja;
use App\Models\PagoCuenta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Sprint 3 — devoluciones en caja y reportes: los dashboards de caja, el
 * resumen financiero y el arqueo (MovimientoCaja) netean las NC vigentes.
 */
class DevolucionCajaReportesTest extends TestCase
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

    private function cobrar(string $precio, string $metodo = 'efectivo'): PagoCuenta
    {
        $cuenta = CuentaCobro::create(['tipo_atencion' => 'consulta_externa', 'estado' => 'pendiente']);
        $cuenta->detalles()->create([
            'tipo_item' => 'servicio', 'descripcion' => 'Consulta',
            'cantidad' => '1', 'precio_unitario' => $precio,
        ]);
        $cuenta->load('detalles');
        $cuenta->recalcularTotales();
        $cuenta->registrarPago((float) $precio, $metodo, null, $this->admin()->id, 'k-'.uniqid());

        return $cuenta->pagos()->first();
    }

    public function test_resumen_financiero_caja_gestion_netea_devoluciones(): void
    {
        $pago = $this->cobrar('300.00', 'efectivo');
        Devolucion::registrarPara($pago, [
            'monto' => '100.00', 'metodo_devolucion' => 'efectivo',
            'motivo' => 'ajuste', 'user_id' => $this->admin()->id,
        ]);

        $hoy = now()->toDateString();
        $res = $this->actingAs($this->admin())->getJson(route('caja.gestion.resumen-financiero', [
            'fecha_inicio' => $hoy, 'fecha_fin' => $hoy,
        ]));

        $res->assertOk();
        $res->assertJsonPath('resumen.totales_generales.total_recaudado', 200);
        $res->assertJsonPath('resumen.totales_generales.total_devoluciones', 100);
        $res->assertJsonPath('resumen.por_metodo_pago.efectivo', 200);
    }

    public function test_devolucion_con_caja_abierta_deja_movimiento_de_arqueo(): void
    {
        $sesion = CajaSession::create([
            'user_id' => $this->admin()->id,
            'fecha_apertura' => now(),
            'monto_inicial' => '100.00',
            'estado' => 'abierta',
        ]);

        $pago = $this->cobrar('200.00');

        $res = $this->actingAs($this->admin())
            ->postJson(route('caja.gestion.devoluciones.store', $pago->id), [
                'monto' => '80.00',
                'metodo_devolucion' => 'efectivo',
                'motivo' => 'cobro en exceso',
            ]);

        $res->assertOk()->assertJson(['success' => true]);

        $nc = Devolucion::first();
        $this->assertSame($sesion->id, $nc->caja_session_id);

        // Arqueo: egreso ligado a la NC, resta del efectivo esperado al cierre
        $mov = MovimientoCaja::where('caja_session_id', $sesion->id)->where('tipo', 'egreso')->first();
        $this->assertNotNull($mov);
        $this->assertSame('Devolución '.$nc->id, $mov->concepto);
        $this->assertSame('80.00', (string) $mov->monto);
        $this->assertSame($nc->id, $mov->referencia);

        // Anular la NC repone el dinero con un ingreso que las fórmulas de
        // cierre cuentan (concepto "Cobro...")
        $this->actingAs($this->admin())
            ->postJson(route('caja.gestion.devoluciones.anular', $nc->id), ['motivo' => 'error'])
            ->assertOk();

        $reposicion = MovimientoCaja::where('caja_session_id', $sesion->id)->where('tipo', 'ingreso')->first();
        $this->assertNotNull($reposicion);
        $this->assertStringStartsWith('Cobro', $reposicion->concepto);
        $this->assertSame('80.00', (string) $reposicion->monto);
    }

    public function test_devolucion_sin_caja_abierta_no_crea_movimiento(): void
    {
        $pago = $this->cobrar('200.00');

        $this->actingAs($this->admin())
            ->postJson(route('caja.gestion.devoluciones.store', $pago->id), [
                'monto' => '50.00',
                'metodo_devolucion' => 'transferencia',
                'motivo' => 'ajuste',
            ])->assertOk();

        $this->assertNull(Devolucion::first()->caja_session_id);
        $this->assertSame(0, MovimientoCaja::count());
    }

    public function test_helper_suma_vigente_por_metodo(): void
    {
        $pago = $this->cobrar('500.00');
        Devolucion::registrarPara($pago, [
            'monto' => '100.00', 'metodo_devolucion' => 'efectivo',
            'motivo' => 'a', 'user_id' => $this->admin()->id,
        ]);
        Devolucion::registrarPara($pago, [
            'monto' => '30.00', 'metodo_devolucion' => 'qr',
            'motivo' => 'b', 'user_id' => $this->admin()->id,
        ]);
        $anulada = Devolucion::registrarPara($pago, [
            'monto' => '20.00', 'metodo_devolucion' => 'efectivo',
            'motivo' => 'c', 'user_id' => $this->admin()->id,
        ]);
        $anulada->anular($this->admin()->id, 'error');

        $hoy = now()->toDateString();
        $this->assertSame('130.00', Devolucion::sumaVigenteDelDia($hoy));
        $this->assertSame('100.00', Devolucion::sumaVigenteDelDia($hoy, 'efectivo'));
        $this->assertSame('30.00', Devolucion::sumaVigenteDelDia($hoy, 'qr'));
        $this->assertSame('130.00', Devolucion::sumaVigente(now()->startOfDay(), now()->endOfDay()));
    }
}
