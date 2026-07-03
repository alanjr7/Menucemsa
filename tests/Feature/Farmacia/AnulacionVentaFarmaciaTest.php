<?php

namespace Tests\Feature\Farmacia;

use App\Models\AlmacenCatalogo;
use App\Models\AlmacenLote;
use App\Models\AlmacenStock;
use App\Models\CierreContable;
use App\Models\DetalleVentaFarmacia;
use App\Models\User;
use App\Models\VentaFarmacia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Anulación segura de ventas de farmacia (devolución): la venta NO se borra
 * (estado ANULADA + auditoría), el stock reingresa a farmacia y la venta deja
 * de contar como ingreso en reportes/RCV (que filtran COMPLETADA).
 */
class AnulacionVentaFarmaciaTest extends TestCase
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

    /** Crea stock en farmacia y una venta COMPLETADA que lo descontó. */
    private function venta(int $cantidadVendida = 3, int $stockRestante = 7): array
    {
        $cat = AlmacenCatalogo::create([
            'nombre' => 'Ibuprofeno 400mg',
            'tipo' => 'medicamento',
            'unidad_medida' => 'unidades',
            'activo' => true,
            'requiere_receta' => false,
        ]);
        $lote = AlmacenLote::create([
            'catalogo_id' => $cat->id,
            'codigo_lote' => 'LOT-DEV-1',
            'laboratorio' => 'LabTest',
            'fecha_vencimiento' => now()->addYear()->toDateString(),
            'precio_compra' => 1.00,
            'precio_venta' => 5.00,
            'cantidad_inicial' => 10,
            'cantidad_recibida' => 10,
        ]);
        $stock = AlmacenStock::create([
            'lote_id' => $lote->id,
            'ubicacion' => 'farmacia',
            'cantidad_actual' => $stockRestante,
            'stock_minimo' => 1,
        ]);

        $venta = VentaFarmacia::create([
            'codigo_venta' => VentaFarmacia::generarCodigoVenta(),
            'usuario_id' => $this->admin()->id,
            'cliente' => 'Cliente General',
            'total' => '15.00',
            'metodo_pago' => 'efectivo',
            'fecha_venta' => now(),
            'estado' => 'COMPLETADA',
        ]);
        DetalleVentaFarmacia::create([
            'codigo_venta' => $venta->codigo_venta,
            'codigo_producto' => (string) $cat->id,
            'tipo_producto' => 'medicamento',
            'nombre_producto' => $cat->nombre,
            'cantidad' => $cantidadVendida,
            'precio_unitario' => '5.00',
            'subtotal' => '15.00',
        ]);

        return [$venta, $stock];
    }

    public function test_anular_reingresa_stock_y_conserva_la_venta(): void
    {
        [$venta, $stock] = $this->venta(cantidadVendida: 3, stockRestante: 7);

        $res = $this->actingAs($this->admin())
            ->postJson(route('farmacia.ventas.anular', $venta->codigo_venta), [
                'motivo' => 'Cliente devolvió el producto',
            ]);

        $res->assertOk()->assertJson(['success' => true]);

        // La venta y sus detalles persisten (no hard-delete), marcadas ANULADA
        $venta->refresh();
        $this->assertSame('ANULADA', $venta->estado);
        $this->assertNotNull($venta->anulado_at);
        $this->assertSame($this->admin()->id, $venta->anulado_por);
        $this->assertSame('Cliente devolvió el producto', $venta->motivo_anulacion);
        $this->assertSame(1, DetalleVentaFarmacia::where('codigo_venta', $venta->codigo_venta)->count());

        // El stock volvió a farmacia: 7 + 3 = 10
        $this->assertSame(10, (int) $stock->fresh()->cantidad_actual);
    }

    public function test_no_se_anula_dos_veces(): void
    {
        [$venta, $stock] = $this->venta();
        $this->actingAs($this->admin())
            ->postJson(route('farmacia.ventas.anular', $venta->codigo_venta), ['motivo' => 'devolución'])
            ->assertOk();

        $res = $this->actingAs($this->admin())
            ->postJson(route('farmacia.ventas.anular', $venta->codigo_venta), ['motivo' => 'otra vez']);

        $res->assertStatus(422);
        // El stock no se infla con el reintento
        $this->assertSame(10, (int) $stock->fresh()->cantidad_actual);
    }

    public function test_no_se_anula_venta_de_periodo_cerrado(): void
    {
        [$venta, $stock] = $this->venta();
        CierreContable::create([
            'anio' => (int) now()->year,
            'mes' => (int) now()->month,
            'cerrado_at' => now(),
            'cerrado_por' => $this->admin()->id,
        ]);

        $res = $this->actingAs($this->admin())
            ->postJson(route('farmacia.ventas.anular', $venta->codigo_venta), ['motivo' => 'tarde']);

        $res->assertStatus(422);
        $this->assertSame('COMPLETADA', $venta->fresh()->estado);
        $this->assertSame(7, (int) $stock->fresh()->cantidad_actual);
    }

    public function test_exige_motivo(): void
    {
        [$venta] = $this->venta();

        $this->actingAs($this->admin())
            ->postJson(route('farmacia.ventas.anular', $venta->codigo_venta), [])
            ->assertStatus(422);

        $this->assertSame('COMPLETADA', $venta->fresh()->estado);
    }

    public function test_anulacion_es_visible_como_devolucion_en_todo_el_sistema(): void
    {
        // La anulación de farmacia no puede ser invisible: debe aparecer como
        // devolución en contabilidad, en la página de devoluciones, en el Excel
        // (fila de rastro) y el ticket debe poder marcarse como ANULADA.
        [$venta] = $this->venta();
        $this->actingAs($this->admin())
            ->postJson(route('farmacia.ventas.anular', $venta->codigo_venta), ['motivo' => 'Cliente devolvió'])
            ->assertOk();
        $venta->refresh();

        // 1) Resumen de contabilidad: fila con origen Farmacia + total informativo
        $hoy = now()->toDateString();
        $res = $this->actingAs($this->admin())->getJson(route('caja.contabilidad.resumen', [
            'fecha_inicio' => $hoy, 'fecha_fin' => $hoy,
        ]));
        $res->assertOk();
        $res->assertJsonPath('totales.devoluciones_farmacia', '15.00');
        $fila = collect($res->json('devoluciones'))->firstWhere('id', $venta->codigo_venta);
        $this->assertNotNull($fila, 'La venta anulada debe listarse en devoluciones de contabilidad');
        $this->assertSame('Farmacia', $fila['origen']);
        $this->assertSame('Cliente devolvió', $fila['motivo']);
        // Informativa: NO vuelve a restar de ingresos (ya quedó excluida por estado)
        $res->assertJsonPath('totales.ingresos_farmacia', '0');

        // 2) Página de devoluciones: aparece en el listado unificado
        $res = $this->actingAs($this->admin())
            ->getJson(route('caja.gestion.devoluciones.listar', ['q' => $venta->codigo_venta]));
        $res->assertOk()->assertJsonCount(1, 'devoluciones.data');
        $res->assertJsonPath('devoluciones.data.0.origen', 'Farmacia');
        $res->assertJsonPath('devoluciones.data.0.monto', '15.00');
        $res->assertJsonPath('stats.total_vigente', '15.00');

        // 3) Excel de contabilidad: fila de rastro sin monto (no rompe las sumas)
        $filas = (new \App\Exports\ContabilidadExport(now()->startOfDay(), now()->endOfDay()))->collection();
        $rastro = $filas->firstWhere('categoria', 'Devolución farmacia');
        $this->assertNotNull($rastro, 'El Excel debe incluir la fila de rastro de la anulación');
        $this->assertSame('', $rastro['monto']);
        $this->assertStringContainsString($venta->codigo_venta, $rastro['descripcion']);
        $this->assertStringContainsString('15.00', $rastro['descripcion']);

        // 4) El endpoint del detalle expone los datos que el ticket ANULADA necesita
        $res = $this->actingAs($this->admin())
            ->getJson(route('farmacia.ventas.show', $venta->codigo_venta));
        $res->assertOk();
        $res->assertJsonPath('estado', 'ANULADA');
        $res->assertJsonPath('motivo_anulacion', 'Cliente devolvió');
        $this->assertNotNull($res->json('anulado_at'));
    }

    public function test_venta_anulada_sale_de_contabilidad_y_rcv(): void
    {
        [$venta] = $this->venta();
        $this->actingAs($this->admin())
            ->postJson(route('farmacia.ventas.anular', $venta->codigo_venta), ['motivo' => 'devolución'])
            ->assertOk();

        // Resumen de contabilidad: sin ingresos de farmacia
        $hoy = now()->toDateString();
        $res = $this->actingAs($this->admin())->getJson(route('caja.contabilidad.resumen', [
            'fecha_inicio' => $hoy, 'fecha_fin' => $hoy,
        ]));
        $res->assertOk();
        $res->assertJsonPath('totales.ingresos_farmacia', '0');

        // Libro de Ventas IVA: la venta anulada no aparece
        $sheet = new \App\Exports\RcvVentasSheet(now()->startOfDay(), now()->endOfDay());
        $this->assertNull($sheet->collection()->firstWhere('origen', 'Farmacia'));
    }
}
