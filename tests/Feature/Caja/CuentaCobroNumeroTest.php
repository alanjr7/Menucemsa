<?php

namespace Tests\Feature\Caja;

use App\Models\CuentaCobro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Número de cuenta correlativo REC-AAAA-NNNNNN: legible, incremental por gestión
 * fiscal y sin saltos (principio de secuencialidad del control interno).
 * Reemplaza al antiguo CC-{timestamp}-{random}, ilegible y no auditable.
 */
class CuentaCobroNumeroTest extends TestCase
{
    use RefreshDatabase;

    private function cuenta(): CuentaCobro
    {
        return CuentaCobro::create([
            'tipo_atencion' => 'consulta_externa',
            'estado' => 'pendiente',
        ]);
    }

    public function test_formato_es_correlativo_anual(): void
    {
        $c = $this->cuenta();

        $this->assertMatchesRegularExpression('/^REC-\d{4}-\d{6}$/', $c->id);
        $this->assertSame('REC-' . now()->format('Y') . '-000001', $c->id);
    }

    public function test_numeros_son_incrementales_sin_saltos(): void
    {
        $anio = now()->format('Y');

        $this->assertSame("REC-{$anio}-000001", $this->cuenta()->id);
        $this->assertSame("REC-{$anio}-000002", $this->cuenta()->id);
        $this->assertSame("REC-{$anio}-000003", $this->cuenta()->id);
    }
}
