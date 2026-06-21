<?php

namespace Tests\Feature\Caja;

use App\Models\CuentaCobro;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Recibo de pago correlativo: PAGO-AAAAMMDD-NNN, incremental y reiniciando
 * por día. Reemplaza al antiguo PAGO-{timestamp}-{random} no secuencial.
 */
class ReciboPagoNumeroTest extends TestCase
{
    use RefreshDatabase;

    private function cuentaConSaldo(string $precio): CuentaCobro
    {
        $cuenta = CuentaCobro::create([
            'tipo_atencion' => 'consulta_externa',
            'estado' => 'pendiente',
        ]);
        $cuenta->detalles()->create([
            'tipo_item' => 'servicio',
            'descripcion' => 'Consulta',
            'cantidad' => '1',
            'precio_unitario' => $precio,
        ]);
        $cuenta->load('detalles');
        $cuenta->recalcularTotales();

        return $cuenta;
    }

    public function test_recibos_son_correlativos_por_dia(): void
    {
        $user = User::factory()->create();
        $cuenta = $this->cuentaConSaldo('100.00');

        $cuenta->registrarPago(40.00, 'efectivo', null, $user->id, 'k1');
        $cuenta->registrarPago(60.00, 'efectivo', null, $user->id, 'k2');

        $ids = $cuenta->pagos()->orderBy('created_at')->pluck('id')->all();
        $hoy = now()->format('Ymd');

        $this->assertSame("PAGO-{$hoy}-001", $ids[0]);
        $this->assertSame("PAGO-{$hoy}-002", $ids[1]);
    }

    public function test_formato_recibo_es_pago_fecha_correlativo(): void
    {
        $user = User::factory()->create();
        $cuenta = $this->cuentaConSaldo('50.00');

        $cuenta->registrarPago(50.00, 'efectivo', null, $user->id, 'k1');

        $id = $cuenta->pagos()->first()->id;
        $this->assertMatchesRegularExpression('/^PAGO-\d{8}-\d{3,}$/', $id);
    }
}
