<?php

namespace Tests\Feature\Reception;

use App\Models\Paciente;
use App\Models\Registro;
use App\Models\Seguro;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regresión: el comprobante de registro mostraba "Sin seguro" aunque el paciente
 * tuviera uno, porque leía la columna fantasma `seguro->nombre` (es `nombre_empresa`).
 * Además debe mostrar la póliza y vigencia que se registran.
 */
class ConfirmacionRegistroSeguroTest extends TestCase
{
    use RefreshDatabase;

    public function test_muestra_seguro_poliza_y_vigencia(): void
    {
        $user = User::factory()->create(['role' => 'reception', 'is_active' => true]);

        $seguro = Seguro::create([
            'formulario' => 'FORM', 'nombre_empresa' => 'UNIVida - SOAT', 'tipo' => 'privado',
            'estado' => 'activo', 'tipo_cobertura' => 'tope_monto', 'tope_monto' => '24000.00',
        ]);

        $codigo = 'REG-99-0101-ZZZ';
        Registro::create([
            'codigo' => $codigo, 'fecha' => now()->toDateString(), 'hora' => now()->toTimeString(),
            'motivo' => 'Test', 'user_id' => $user->id,
        ]);

        Paciente::create([
            'ci' => 9090901, 'nombre' => 'Paciente Con Seguro', 'sexo' => 'M', 'is_temp' => false,
            'seguro_id' => $seguro->id, 'registro_codigo' => $codigo,
            'seguro_poliza' => '5555',
            'seguro_vigencia_desde' => '2026-06-24', 'seguro_vigencia_hasta' => '2026-07-30',
        ]);

        $res = $this->actingAs($user)->get(route('reception.confirmacion-registro', $codigo));

        $res->assertOk();
        $res->assertSee('UNIVida - SOAT');
        $res->assertSee('5555');
        $res->assertDontSee('Sin seguro');
    }
}
