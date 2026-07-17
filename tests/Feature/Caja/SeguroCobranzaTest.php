<?php

namespace Tests\Feature\Caja;

use App\Models\CierreContable;
use App\Models\CuentaCobro;
use App\Models\Seguro;
use App\Models\SeguroCobro;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Sprint 2 — Sub-mayor de cobranza a seguros: liquidación (cobro a la aseguradora),
 * liquidación en lote y anulación reversible bloqueada en períodos cerrados.
 */
class SeguroCobranzaTest extends TestCase
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

    private function seguro(): Seguro
    {
        return Seguro::create([
            'formulario' => 'FORM-TEST', 'nombre_empresa' => 'ASEG SA', 'tipo' => 'privado',
            'estado' => 'activo', 'tipo_cobertura' => 'porcentaje',
            'cobertura_porcentaje' => '80.00', 'copago_porcentaje' => '20.00',
        ]);
    }

    private function coberturaPendiente(Seguro $seguro, string $precio = '100.00'): SeguroCobro
    {
        $cuenta = CuentaCobro::create(['tipo_atencion' => 'consulta_externa', 'estado' => 'pendiente']);
        $cuenta->detalles()->create([
            'tipo_item' => 'servicio', 'descripcion' => 'Consulta',
            'cantidad' => '1', 'precio_unitario' => $precio,
        ]);
        $cuenta->load('detalles');
        $cuenta->recalcularTotales();
        $cuenta->setRelation('seguro', $seguro);
        $cuenta->autorizarSeguro($seguro, (float) $precio);

        return SeguroCobro::where('cuenta_cobro_id', $cuenta->id)->firstOrFail();
    }

    public function test_index_responde(): void
    {
        $this->coberturaPendiente($this->seguro());
        $res = $this->actingAs($this->admin())->get(route('admin.seguros.cobranza'));
        $res->assertOk();
        $res->assertSee('Cobranza a Seguros');
    }

    public function test_liquidar_marca_cobrado(): void
    {
        $cobro = $this->coberturaPendiente($this->seguro());

        $res = $this->actingAs($this->admin())
            ->postJson(route('admin.seguros.cobranza.liquidar', $cobro), ['referencia' => 'TRF-123']);

        $res->assertOk()->assertJsonPath('success', true);
        $cobro->refresh();
        $this->assertSame('cobrado', $cobro->estado);
        $this->assertSame('TRF-123', $cobro->liquidado_referencia);
        $this->assertNotNull($cobro->liquidado_en);
    }

    public function test_no_se_liquida_dos_veces(): void
    {
        $cobro = $this->coberturaPendiente($this->seguro());
        $cobro->liquidar($this->admin()->id);

        $res = $this->actingAs($this->admin())
            ->postJson(route('admin.seguros.cobranza.liquidar', $cobro), []);

        $res->assertStatus(422);
    }

    public function test_liquidar_lote_liquida_todas_las_pendientes(): void
    {
        $seguro = $this->seguro();
        $this->coberturaPendiente($seguro, '100.00');
        $this->coberturaPendiente($seguro, '200.00');

        $res = $this->actingAs($this->admin())
            ->postJson(route('admin.seguros.cobranza.liquidar-lote', $seguro), ['referencia' => 'LOTE-1']);

        $res->assertOk()->assertJsonPath('success', true);
        $this->assertSame(0, SeguroCobro::where('seguro_id', $seguro->id)->pendiente()->count());
        $this->assertSame(2, SeguroCobro::where('seguro_id', $seguro->id)->cobrado()->count());
    }

    public function test_anular_marca_anulado(): void
    {
        $cobro = $this->coberturaPendiente($this->seguro());

        $res = $this->actingAs($this->admin())
            ->postJson(route('admin.seguros.cobranza.anular', $cobro), ['motivo' => 'Rechazo de la aseguradora']);

        $res->assertOk()->assertJsonPath('success', true);
        $this->assertSame('anulado', $cobro->fresh()->estado);
    }

    public function test_anular_bloqueado_en_periodo_cerrado(): void
    {
        $cobro = $this->coberturaPendiente($this->seguro());

        CierreContable::create([
            'anio' => (int) now()->year, 'mes' => (int) now()->month,
            'cerrado_at' => now(), 'cerrado_por' => $this->admin()->id,
        ]);

        $res = $this->actingAs($this->admin())
            ->postJson(route('admin.seguros.cobranza.anular', $cobro), ['motivo' => 'tarde']);

        $res->assertStatus(422);
        $this->assertSame('pendiente', $cobro->fresh()->estado);
    }

    public function test_cobertura_anulada_sale_del_debito_fiscal(): void
    {
        $cobro = $this->coberturaPendiente($this->seguro(), '100.00'); // cobertura 80, débito 10.40
        $cobro->anular($this->admin()->id, 'rechazo');

        $hoy = now()->toDateString();
        $res = $this->actingAs($this->admin())->getJson(route('caja.contabilidad.resumen', [
            'fecha_inicio' => $hoy, 'fecha_fin' => $hoy,
        ]));

        $res->assertOk();
        // Sin pagos de paciente y con la cobertura anulada: no hay débito ni ventas a seguro.
        $res->assertJsonPath('totales.debito_fiscal', '0.00');
        $res->assertJsonPath('totales.ventas_seguro', '0');
    }
}
