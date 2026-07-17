<?php

namespace Tests\Feature\Caja;

use App\Models\CuentaCobro;
use App\Models\Paciente;
use App\Models\Seguro;
use App\Models\SeguroCobro;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Sprint 5 — El tope (tope_monto) es un límite AGREGADO por paciente/gestión: se reparte
 * entre todas las cuentas del paciente en el período, no se reinicia en cada cuenta.
 */
class SeguroTopeAgregadoTest extends TestCase
{
    use RefreshDatabase;

    private function seguroTope(string $tope): Seguro
    {
        return Seguro::create([
            'formulario' => 'FORM-TEST', 'nombre_empresa' => 'ASEG SA', 'tipo' => 'privado',
            'estado' => 'activo', 'tipo_cobertura' => 'tope_monto', 'tope_monto' => $tope,
        ]);
    }

    private function autorizar(Paciente $p, Seguro $seguro, string $precio): CuentaCobro
    {
        $cuenta = CuentaCobro::create([
            'tipo_atencion' => 'consulta_externa', 'estado' => 'pendiente', 'paciente_id' => $p->id,
        ]);
        $cuenta->detalles()->create([
            'tipo_item' => 'servicio', 'descripcion' => 'Atención',
            'cantidad' => '1', 'precio_unitario' => $precio,
        ]);
        $cuenta->load('detalles');
        $cuenta->recalcularTotales();
        $cuenta->autorizarSeguro($seguro, (float) $precio);

        return $cuenta->fresh();
    }

    public function test_tope_se_reparte_entre_cuentas_del_paciente(): void
    {
        $paciente = Paciente::create(['nombre' => 'Juan Tope', 'seguro_id' => null]);
        $seguro = $this->seguroTope('100.00');

        $c1 = $this->autorizar($paciente, $seguro, '80.00'); // disponible 100 → cubre 80
        $c2 = $this->autorizar($paciente, $seguro, '80.00'); // disponible 20 → cubre 20, copago 60
        $c3 = $this->autorizar($paciente, $seguro, '50.00'); // disponible 0  → cubre 0, copago 50

        $this->assertSame('80.00', (string) $c1->seguro_monto_cobertura);
        $this->assertSame('20.00', (string) $c2->seguro_monto_cobertura);
        $this->assertSame('60.00', (string) $c2->seguro_monto_paciente);
        $this->assertSame('0.00', (string) $c3->seguro_monto_cobertura);
        $this->assertSame('50.00', (string) $c3->seguro_monto_paciente);

        // El total cubierto nunca excede el tope.
        $this->assertSame('100.00', SeguroCobro::consumoPaciente($paciente->id, $seguro->id, (int) now()->year));
    }

    public function test_pacientes_distintos_no_comparten_tope(): void
    {
        $seguro = $this->seguroTope('100.00');
        $p1 = Paciente::create(['nombre' => 'Uno']);
        $p2 = Paciente::create(['nombre' => 'Dos']);

        $this->autorizar($p1, $seguro, '100.00'); // consume todo el tope de P1
        $c2 = $this->autorizar($p2, $seguro, '80.00'); // P2 tiene su propio tope

        $this->assertSame('80.00', (string) $c2->seguro_monto_cobertura);
    }
}
