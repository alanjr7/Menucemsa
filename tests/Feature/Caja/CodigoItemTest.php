<?php

namespace Tests\Feature\Caja;

use App\Models\AlmacenCatalogo;
use App\Models\CodigoItem;
use App\Models\CuentaCobro;
use App\Models\IngresoPrecio;
use App\Models\Procedimiento;
use App\Models\TipoCirugia;
use App\Support\CodigoProducto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Códigos internos de producto/servicio (formato Incor: familia + id).
 *
 * - Catálogos: autollenan su `codigo` por familia.
 * - Cargos: CuentaCobroDetalle::creating estampa codigo_item (catálogo o
 *   diccionario familia 9). Chokepoint único, defensivo.
 */
class CodigoItemTest extends TestCase
{
    use RefreshDatabase;

    private function cuenta(): CuentaCobro
    {
        return CuentaCobro::create(['tipo_atencion' => 'consulta_externa', 'estado' => 'pendiente']);
    }

    // ── Catálogos: autollenado por familia ──

    public function test_catalogos_autollenan_codigo_por_familia(): void
    {
        $med = AlmacenCatalogo::create(['nombre' => 'Paracetamol 500mg', 'tipo' => 'medicamento']);
        $this->assertSame(CodigoProducto::format('1', $med->id), $med->fresh()->codigo);

        $proc = Procedimiento::create(['nombre' => 'Curación simple', 'area' => 'emergencia', 'precio' => '50.00']);
        $this->assertSame(CodigoProducto::format('5', $proc->id), $proc->fresh()->codigo);

        $cir = TipoCirugia::create(['nombre' => 'especial', 'duracion_minutos' => 30, 'costo_base' => '400.00']);
        $this->assertSame(CodigoProducto::format('6', $cir->id), $cir->fresh()->codigo);

        $adm = IngresoPrecio::create(['tipo_ingreso' => 'consulta_externa', 'precio' => '70.00']);
        $this->assertSame(CodigoProducto::format('2', $adm->id), $adm->fresh()->codigo);
    }

    // ── Cargo de texto libre → diccionario familia 9, estable ──

    public function test_cargo_texto_libre_recibe_codigo_de_diccionario_familia_9(): void
    {
        $cuenta = $this->cuenta();

        $d1 = $cuenta->detalles()->create([
            'tipo_item' => 'laboratorio', 'descripcion' => 'Hemograma completo',
            'cantidad' => '1', 'precio_unitario' => '73.00',
        ]);

        $this->assertNotNull($d1->codigo_item);
        $this->assertStringStartsWith('9', $d1->codigo_item);

        // Misma descripción (otro caso/espacios) → MISMO código (idempotente).
        $d2 = $cuenta->detalles()->create([
            'tipo_item' => 'laboratorio', 'descripcion' => '  HEMOGRAMA   COMPLETO ',
            'cantidad' => '1', 'precio_unitario' => '73.00',
        ]);
        $this->assertSame($d1->codigo_item, $d2->codigo_item);
        $this->assertSame(1, CodigoItem::count());
    }

    // ── Cargo que coincide con un catálogo → toma SU código (familia 5) ──

    public function test_cargo_que_coincide_con_procedimiento_toma_su_codigo(): void
    {
        $proc = Procedimiento::create(['nombre' => 'Sutura mayor', 'area' => 'emergencia', 'precio' => '120.00']);
        $cuenta = $this->cuenta();

        $d = $cuenta->detalles()->create([
            'tipo_item' => 'procedimiento', 'descripcion' => 'sutura mayor',
            'cantidad' => '1', 'precio_unitario' => '120.00',
        ]);

        $this->assertSame($proc->fresh()->codigo, $d->codigo_item);
        $this->assertSame(0, CodigoItem::count()); // no tocó el diccionario
    }

    // ── Cargo de admisión → toma el código familia 2 de IngresoPrecio ──

    public function test_cargo_de_admision_toma_codigo_familia_2(): void
    {
        $adm = IngresoPrecio::create(['tipo_ingreso' => 'emergencia', 'precio' => '200.00']);
        $cuenta = $this->cuenta();

        $d = $cuenta->detalles()->create([
            'tipo_item' => 'servicio', 'descripcion' => 'Admisión de Emergencia',
            'cantidad' => '1', 'precio_unitario' => '200.00',
        ]);

        $this->assertSame($adm->fresh()->codigo, $d->codigo_item);
        $this->assertStringStartsWith('2', $d->codigo_item);
        $this->assertSame(0, CodigoItem::count()); // no tocó el diccionario familia 9
    }

    // ── Admisión sin precio configurado → degrada al diccionario familia 9 ──

    public function test_cargo_de_admision_sin_precio_cae_a_diccionario(): void
    {
        $cuenta = $this->cuenta(); // ingreso_precios vacío

        $d = $cuenta->detalles()->create([
            'tipo_item' => 'servicio', 'descripcion' => 'Admisión de Internación',
            'cantidad' => '1', 'precio_unitario' => '150.00',
        ]);

        $this->assertStringStartsWith('9', $d->codigo_item);
        $this->assertSame(1, CodigoItem::count());
    }

    // ── Código explícito se respeta ──

    public function test_codigo_explicito_no_se_sobreescribe(): void
    {
        $cuenta = $this->cuenta();
        $d = $cuenta->detalles()->create([
            'tipo_item' => 'servicio', 'codigo_item' => '299999999',
            'descripcion' => 'Cargo con código fijo', 'cantidad' => '1', 'precio_unitario' => '10.00',
        ]);

        $this->assertSame('299999999', $d->codigo_item);
        $this->assertSame(0, CodigoItem::count());
    }

    // ── Backfill: idempotente y cubre filas raw (insert del seeder de cirugías) ──

    public function test_backfill_asigna_codigos_faltantes_y_es_idempotente(): void
    {
        // La migración de tipos_cirugia inserta 4 filas vía DB::table()->insert()
        // (sin pasar por el hook Eloquent): nacen sin código.
        $menor = TipoCirugia::where('nombre', 'menor')->first();
        $this->assertNotNull($menor);
        $this->assertNull($menor->codigo);

        // Un cargo viejo sin código (simulado saltando el hook con saveQuietly).
        $cuenta = $this->cuenta();
        $viejo = new \App\Models\CuentaCobroDetalle([
            'tipo_item' => 'servicio', 'descripcion' => 'Cargo antiguo',
            'cantidad' => '1', 'precio_unitario' => '10.00', 'subtotal' => '10.00',
        ]);
        $viejo->cuenta_cobro_id = $cuenta->id;
        $viejo->codigo_item = null;
        $viejo->saveQuietly(); // salta el hook → simula un cargo previo a esta feature
        $this->assertNull($viejo->fresh()->codigo_item);

        $this->artisan('codigos:backfill')->assertSuccessful();

        $this->assertSame(CodigoProducto::format('6', $menor->id), $menor->fresh()->codigo);
        $this->assertNotNull($viejo->fresh()->codigo_item);

        // Idempotente: segunda corrida no cambia nada.
        $codigoMenor = $menor->fresh()->codigo;
        $codigoViejo = $viejo->fresh()->codigo_item;
        $this->artisan('codigos:backfill')->assertSuccessful();
        $this->assertSame($codigoMenor, $menor->fresh()->codigo);
        $this->assertSame($codigoViejo, $viejo->fresh()->codigo_item);
    }
}
