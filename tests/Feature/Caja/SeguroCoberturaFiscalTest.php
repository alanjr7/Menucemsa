<?php

namespace Tests\Feature\Caja;

use App\Models\CuentaCobro;
use App\Models\Seguro;
use App\Models\SeguroCobro;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Sprint 1 — La cobertura del seguro deja de ser ingreso fantasma: al autorizar se
 * materializa una VENTA devengada a la aseguradora (cuenta por cobrar) con débito
 * fiscal IVA, y el snapshot de cobertura queda congelado.
 */
class SeguroCoberturaFiscalTest extends TestCase
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

    private function cuentaCon(string $precio): CuentaCobro
    {
        $cuenta = CuentaCobro::create(['tipo_atencion' => 'consulta_externa', 'estado' => 'pendiente']);
        $cuenta->detalles()->create([
            'tipo_item' => 'servicio', 'descripcion' => 'Consulta',
            'cantidad' => '1', 'precio_unitario' => $precio,
        ]);
        $cuenta->load('detalles');
        $cuenta->recalcularTotales();

        return $cuenta;
    }

    private function seguroPorcentaje(string $cobertura, string $copago): Seguro
    {
        return Seguro::create([
            'formulario' => 'FORM-TEST',
            'nombre_empresa' => 'ASEG SA',
            'tipo' => 'privado',
            'estado' => 'activo',
            'tipo_cobertura' => 'porcentaje',
            'cobertura_porcentaje' => $cobertura,
            'copago_porcentaje' => $copago,
        ]);
    }

    public function test_autorizar_seguro_crea_venta_devengada_con_debito_fiscal(): void
    {
        $cuenta = $this->cuentaCon('100.00');
        $seguro = $this->seguroPorcentaje('80.00', '20.00');

        $cuenta->load('seguro');
        $cuenta->setRelation('seguro', $seguro);
        $cuenta->autorizarSeguro($seguro, 100.00);
        $cuenta->refresh();

        // Snapshot en la cuenta
        $this->assertSame('80.00', (string) $cuenta->seguro_monto_cobertura);
        $this->assertSame('20.00', (string) $cuenta->seguro_monto_paciente);
        $this->assertEqualsWithDelta(20.00, $cuenta->saldo_pendiente, 0.001);
        $this->assertSame('parcial', $cuenta->estado);

        // Venta devengada a la aseguradora con débito fiscal IVA 13% (80 * 0.13 = 10.40)
        $cobro = SeguroCobro::where('cuenta_cobro_id', $cuenta->id)->firstOrFail();
        $this->assertSame('80.00', (string) $cobro->monto);
        $this->assertSame('80.00', (string) $cobro->base_imponible);
        $this->assertSame('10.40', (string) $cobro->debito_fiscal);
        $this->assertSame('pendiente', $cobro->estado);
        $this->assertSame($seguro->id, $cobro->seguro_id);
    }

    public function test_cobertura_abierta_sigue_los_cargos_nuevos(): void
    {
        $cuenta = $this->cuentaCon('100.00');
        $seguro = $this->seguroPorcentaje('80.00', '20.00');
        $cuenta->setRelation('seguro', $seguro);
        $cuenta->autorizarSeguro($seguro); // cobertura 80, copago 20

        // Cargo agregado DESPUÉS de autorizar: autorización ABIERTA → la cobertura lo cubre.
        $cuenta->detalles()->create([
            'tipo_item' => 'servicio', 'descripcion' => 'Extra',
            'cantidad' => '1', 'precio_unitario' => '50.00',
        ]);
        $cuenta->load(['detalles', 'seguro']);
        $cuenta->recalcularTotales();
        $cuenta->refresh();

        // Cobertura sigue al total: 80% de 150 = 120; copago = 30.
        $this->assertSame('150.00', (string) $cuenta->total_calculado);
        $this->assertSame('120.00', (string) $cuenta->seguro_monto_cobertura);
        $this->assertSame('30.00', (string) $cuenta->seguro_monto_paciente);
        $this->assertEqualsWithDelta(30.00, $cuenta->saldo_pendiente, 0.001);

        // La MISMA venta devengada se sincroniza (no se duplica): monto 120, débito 15.60.
        $cobros = SeguroCobro::where('cuenta_cobro_id', $cuenta->id)->get();
        $this->assertCount(1, $cobros);
        $this->assertSame('120.00', (string) $cobros[0]->monto);
        $this->assertSame('15.60', (string) $cobros[0]->debito_fiscal);
    }

    public function test_cobertura_se_congela_al_liquidar(): void
    {
        $cuenta = $this->cuentaCon('100.00');
        $seguro = $this->seguroPorcentaje('80.00', '20.00');
        $cuenta->setRelation('seguro', $seguro);
        $cuenta->autorizarSeguro($seguro); // cobertura 80

        // Liquidar la venta a la aseguradora congela la cobertura.
        SeguroCobro::where('cuenta_cobro_id', $cuenta->id)->first()->liquidar();

        $cuenta->detalles()->create([
            'tipo_item' => 'servicio', 'descripcion' => 'Extra',
            'cantidad' => '1', 'precio_unitario' => '50.00',
        ]);
        $cuenta->load(['detalles', 'seguro']);
        $cuenta->recalcularTotales();
        $cuenta->refresh();

        // Ya liquidada: la cobertura NO crece; el cargo nuevo lo paga el paciente.
        $this->assertSame('80.00', (string) $cuenta->seguro_monto_cobertura);
        $this->assertEqualsWithDelta(70.00, $cuenta->saldo_pendiente, 0.001);
    }

    public function test_registrar_para_es_idempotente(): void
    {
        $cuenta = $this->cuentaCon('100.00');
        $seguro = $this->seguroPorcentaje('80.00', '20.00');
        $cuenta->setRelation('seguro', $seguro);

        $cuenta->autorizarSeguro($seguro, 100.00);
        $cuenta->autorizarSeguro($seguro, 100.00); // reintento

        $this->assertSame(1, SeguroCobro::where('cuenta_cobro_id', $cuenta->id)->vigentes()->count());
    }

    public function test_resumen_suma_debito_de_seguro_pero_no_lo_cuenta_como_caja(): void
    {
        $user = User::factory()->create();
        $cuenta = $this->cuentaCon('100.00');
        $seguro = $this->seguroPorcentaje('80.00', '20.00');
        $cuenta->setRelation('seguro', $seguro);
        $cuenta->autorizarSeguro($seguro, 100.00);

        // El paciente paga el copago de 20 en efectivo.
        $cuenta->registrarPago(20.00, 'efectivo', null, $user->id, 'k-copago');

        $hoy = now()->toDateString();
        $res = $this->actingAs($this->admin())->getJson(route('caja.contabilidad.resumen', [
            'fecha_inicio' => $hoy,
            'fecha_fin' => $hoy,
        ]));

        $res->assertOk();
        // Débito fiscal = copago (20*0.13=2.60) + seguro (80*0.13=10.40) = 13.00
        $res->assertJsonPath('totales.debito_fiscal', '13.00');
        // La cobertura es venta devengada, NO caja: efectivo solo el copago.
        $res->assertJsonPath('totales.ingresos_caja', '20.00');
        $res->assertJsonPath('totales.ventas_seguro', '80.00');
        // Ventas brutas (base IT/IUE) = caja 20 + seguro 80 = 100.
        $res->assertJsonPath('totales.ventas_brutas', '100.00');
    }
}
