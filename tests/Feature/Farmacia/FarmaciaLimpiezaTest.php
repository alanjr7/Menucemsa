<?php

namespace Tests\Feature\Farmacia;

use App\Models\AlmacenCatalogo;
use App\Models\AlmacenLote;
use App\Models\AlmacenStock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FarmaciaLimpiezaTest extends TestCase
{
    use RefreshDatabase;

    private function farmaciaUser(): User
    {
        return User::factory()->create(['role' => 'farmacia', 'is_active' => true]);
    }

    private function stockFarmacia(array $catalogo = [], array $lote = [], int $cantidad = 50): AlmacenStock
    {
        $cat = AlmacenCatalogo::create(array_merge([
            'nombre' => 'Paracetamol 500mg',
            'tipo' => 'medicamento',
            'unidad_medida' => 'unidades',
            'activo' => true,
            'requiere_receta' => false,
        ], $catalogo));

        $lot = AlmacenLote::create(array_merge([
            'catalogo_id' => $cat->id,
            'codigo_lote' => 'LOT-TEST-1',
            'laboratorio' => 'LabTest',
            'fecha_vencimiento' => now()->addYear()->toDateString(),
            'precio_compra' => 1.00,
            'precio_venta' => 2.50,
            'cantidad_inicial' => $cantidad,
            'cantidad_recibida' => $cantidad,
        ], $lote));

        return AlmacenStock::create([
            'lote_id' => $lot->id,
            'ubicacion' => 'farmacia',
            'cantidad_actual' => $cantidad,
            'stock_minimo' => 5,
        ]);
    }

    /** Las clases del sistema viejo de farmacia ya no existen. */
    public function test_clases_legacy_eliminadas(): void
    {
        $clases = [
            \App\Models\Insumos::class,
            \App\Models\DetalleInsumos::class,
            \App\Models\CajaFarmacia::class,
            'App\\Http\\Controllers\\Farmacia\\MedicamentosController',
            'App\\Http\\Resources\\MedicamentosResource',
            'App\\Http\\Resources\\FarmaciaResource',
            'App\\Http\\Requests\\Farmacia\\StoreMedicamentosRequest',
        ];

        foreach ($clases as $clase) {
            $this->assertFalse(class_exists($clase), "La clase legacy {$clase} debería estar eliminada");
        }
    }

    /** Las tablas del esquema legacy fueron dropeadas. */
    public function test_tablas_legacy_eliminadas(): void
    {
        foreach (['insumos', 'uti_supplies', 'uti_medications', 'uti_recipes', 'uti_recipe_details', 'inventario_farmacia'] as $tabla) {
            $this->assertFalse(Schema::hasTable($tabla), "La tabla legacy {$tabla} debería estar eliminada");
        }
    }

    /** Las tablas vivas que se mantuvieron siguen presentes. */
    public function test_tablas_vivas_intactas(): void
    {
        foreach (['medicamentos', 'farmacias', 'recetas', 'detalle_receta', 'ventas_farmacia', 'almacen_stocks'] as $tabla) {
            $this->assertTrue(Schema::hasTable($tabla), "La tabla viva {$tabla} no debería haberse tocado");
        }
    }

    public function test_dashboard_farmacia_carga(): void
    {
        $this->actingAs($this->farmaciaUser())
            ->get(route('farmacia.index'))
            ->assertOk();
    }

    public function test_pos_carga_productos_desde_almacen(): void
    {
        $this->stockFarmacia();

        $this->actingAs($this->farmaciaUser())
            ->get(route('farmacia.pos'))
            ->assertOk()
            ->assertSee('Paracetamol 500mg');
    }

    public function test_pos_procesa_venta_y_descuenta_almacen_stock(): void
    {
        $stock = $this->stockFarmacia(cantidad: 50);

        $response = $this->actingAs($this->farmaciaUser())
            ->postJson(route('farmacia.pos.procesar'), [
                'items' => [
                    ['id' => $stock->id, 'cantidad' => 3, 'precio' => 2.50],
                ],
                'cliente_id' => null,
                'metodo_pago' => 'efectivo',
            ]);

        $response->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseHas('ventas_farmacia', [
            'codigo_venta' => $response->json('codigo_venta'),
            'total' => 7.50,
        ]);

        $this->assertEquals(47, $stock->fresh()->cantidad_actual);
    }

    public function test_pos_rechaza_stock_insuficiente(): void
    {
        $stock = $this->stockFarmacia(cantidad: 2);

        $this->actingAs($this->farmaciaUser())
            ->postJson(route('farmacia.pos.procesar'), [
                'items' => [
                    ['id' => $stock->id, 'cantidad' => 10, 'precio' => 2.50],
                ],
                'cliente_id' => null,
                'metodo_pago' => 'efectivo',
            ])
            ->assertStatus(400)
            ->assertJson(['success' => false]);

        $this->assertEquals(2, $stock->fresh()->cantidad_actual);
    }

    public function test_inventario_ventas_y_reporte_cargan(): void
    {
        $user = $this->farmaciaUser();

        $this->actingAs($user)->get(route('farmacia.inventario'))->assertOk();
        $this->actingAs($user)->get(route('farmacia.ventas'))->assertOk();
        $this->actingAs($user)->get(route('farmacia.reporte'))->assertOk();
    }

    public function test_anular_venta_repone_stock_en_farmacia(): void
    {
        $stock = $this->stockFarmacia(cantidad: 50);
        $user = $this->farmaciaUser();

        $venta = $this->actingAs($user)->postJson(route('farmacia.pos.procesar'), [
            'items' => [['id' => $stock->id, 'cantidad' => 4, 'precio' => 2.50]],
            'cliente_id' => null,
            'metodo_pago' => 'efectivo',
        ])->assertOk();

        $this->assertEquals(46, $stock->fresh()->cantidad_actual);

        $this->actingAs($user)
            ->deleteJson(route('farmacia.ventas.destroy', $venta->json('codigo_venta')))
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertEquals(50, $stock->fresh()->cantidad_actual);
        $this->assertDatabaseMissing('ventas_farmacia', ['codigo_venta' => $venta->json('codigo_venta')]);
    }
}
