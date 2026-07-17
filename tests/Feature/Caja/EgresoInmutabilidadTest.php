<?php

namespace Tests\Feature\Caja;

use App\Models\CierreContable;
use App\Models\Egreso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Inmutabilidad contable: los egresos no se borran (se anulan, reversible + auditado)
 * y un período cerrado no admite registrar ni anular egresos con fecha en ese mes.
 */
class EgresoInmutabilidadTest extends TestCase
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

    private function gerente(): User
    {
        return User::where('email', 'gerente@ClinicaSantaCruz.com')->firstOrFail();
    }

    private function nuevoEgreso(array $attrs = []): Egreso
    {
        return Egreso::create(array_merge([
            'fecha' => now()->toDateString(),
            'categoria' => 'otros',
            'descripcion' => 'Compra de prueba',
            'monto' => '100.00',
            'metodo_pago' => 'efectivo',
            'user_id' => $this->admin()->id,
        ], $attrs));
    }

    public function test_anular_no_borra_el_egreso_y_lo_saca_del_total(): void
    {
        $egreso = $this->nuevoEgreso();

        $res = $this->actingAs($this->admin())
            ->postJson(route('caja.contabilidad.egresos.anular', $egreso->id), [
                'motivo' => 'Cargado por error',
            ]);

        $res->assertOk()->assertJson(['success' => true]);

        // El registro sigue existiendo (no hard-delete) pero queda marcado y auditado.
        $egreso->refresh();
        $this->assertTrue($egreso->anulado);
        $this->assertNotNull($egreso->anulado_at);
        $this->assertSame($this->admin()->id, $egreso->anulado_por);
        $this->assertSame('Cargado por error', $egreso->motivo_anulacion);
        $this->assertSame(1, Egreso::count());

        // El scope vigentes lo excluye del flujo de caja.
        $this->assertSame(0, Egreso::vigentes()->count());
    }

    public function test_anular_exige_motivo(): void
    {
        $egreso = $this->nuevoEgreso();

        $res = $this->actingAs($this->admin())
            ->postJson(route('caja.contabilidad.egresos.anular', $egreso->id), []);

        $res->assertStatus(422);
        $this->assertFalse($egreso->refresh()->anulado);
    }

    public function test_revertir_restaura_el_egreso(): void
    {
        $egreso = $this->nuevoEgreso();
        $egreso->anular($this->admin()->id, 'error');

        $res = $this->actingAs($this->admin())
            ->postJson(route('caja.contabilidad.egresos.revertir', $egreso->id));

        $res->assertOk()->assertJson(['success' => true]);

        $egreso->refresh();
        $this->assertFalse($egreso->anulado);
        $this->assertNull($egreso->anulado_at);
        $this->assertSame(1, Egreso::vigentes()->count());
    }

    public function test_no_se_registra_egreso_en_periodo_cerrado(): void
    {
        CierreContable::create([
            'anio' => (int) now()->year,
            'mes' => (int) now()->month,
            'cerrado_at' => now(),
            'cerrado_por' => $this->admin()->id,
        ]);

        $res = $this->actingAs($this->admin())
            ->postJson(route('caja.contabilidad.egresos.store'), [
                'fecha' => now()->toDateString(),
                'categoria' => 'otros',
                'descripcion' => 'Compra tardía',
                'monto' => '50.00',
                'metodo_pago' => 'efectivo',
            ]);

        $res->assertStatus(422);
        $this->assertSame(0, Egreso::count());
    }

    public function test_no_se_anula_egreso_en_periodo_cerrado(): void
    {
        $egreso = $this->nuevoEgreso();
        CierreContable::create([
            'anio' => (int) now()->year,
            'mes' => (int) now()->month,
            'cerrado_at' => now(),
            'cerrado_por' => $this->admin()->id,
        ]);

        $res = $this->actingAs($this->admin())
            ->postJson(route('caja.contabilidad.egresos.anular', $egreso->id), [
                'motivo' => 'no debería poder',
            ]);

        $res->assertStatus(422);
        $this->assertFalse($egreso->refresh()->anulado);
    }

    public function test_admin_cierra_periodo_pasado(): void
    {
        $mesPasado = now()->subMonthNoOverflow();

        $res = $this->actingAs($this->admin())
            ->postJson(route('caja.contabilidad.cierres.store'), [
                'anio' => (int) $mesPasado->year,
                'mes' => (int) $mesPasado->month,
                'observaciones' => 'Declarado F-200',
            ]);

        $res->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseHas('cierres_contables', [
            'anio' => (int) $mesPasado->year,
            'mes' => (int) $mesPasado->month,
        ]);
    }

    public function test_no_se_cierra_un_periodo_futuro(): void
    {
        $mesProximo = now()->addMonthNoOverflow();

        $res = $this->actingAs($this->admin())
            ->postJson(route('caja.contabilidad.cierres.store'), [
                'anio' => (int) $mesProximo->year,
                'mes' => (int) $mesProximo->month,
            ]);

        $res->assertStatus(422);
        $this->assertSame(0, CierreContable::count());
    }

    public function test_gerente_no_puede_cerrar_periodo(): void
    {
        $mesPasado = now()->subMonthNoOverflow();

        $res = $this->actingAs($this->gerente())
            ->postJson(route('caja.contabilidad.cierres.store'), [
                'anio' => (int) $mesPasado->year,
                'mes' => (int) $mesPasado->month,
            ]);

        $res->assertStatus(403);
        $this->assertSame(0, CierreContable::count());
    }
}
