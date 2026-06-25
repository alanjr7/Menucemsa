<?php

namespace Tests\Feature\Caja;

use App\Models\CuentaCobro;
use App\Models\Paciente;
use App\Models\Seguro;
use App\Models\SeguroCobro;
use App\Models\User;
use App\Services\AplicarSeguroService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Sprint 4 — Datos reales del seguro: vigencia de la póliza, NIT de la aseguradora
 * en el RCV y N° de autorización emitido por el seguro.
 */
class SeguroDatosRealesTest extends TestCase
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

    private function seguro(array $extra = []): Seguro
    {
        return Seguro::create(array_merge([
            'formulario' => 'FORM-TEST', 'nombre_empresa' => 'ASEG SA', 'tipo' => 'privado',
            'estado' => 'activo', 'tipo_cobertura' => 'porcentaje',
            'cobertura_porcentaje' => '80.00', 'copago_porcentaje' => '20.00',
        ], $extra));
    }

    private function cuentaConPaciente(Seguro $seguro, ?string $vigenciaHasta): CuentaCobro
    {
        $cuenta = CuentaCobro::create(['tipo_atencion' => 'consulta_externa', 'estado' => 'pendiente']);
        $cuenta->detalles()->create([
            'tipo_item' => 'servicio', 'descripcion' => 'Consulta',
            'cantidad' => '1', 'precio_unitario' => '100.00',
        ]);
        $cuenta->load('detalles');
        $cuenta->recalcularTotales();

        // Paciente en memoria con su póliza y vigencia (evita columnas obligatorias del registro).
        $paciente = new Paciente(['seguro_id' => $seguro->id, 'seguro_vigencia_hasta' => $vigenciaHasta]);
        $paciente->setRelation('seguro', $seguro);
        $cuenta->setRelation('paciente', $paciente);

        return $cuenta;
    }

    public function test_no_aplica_seguro_vencido(): void
    {
        $cuenta = $this->cuentaConPaciente($this->seguro(), now()->subDay()->toDateString());

        $res = AplicarSeguroService::aplicarSiCorresponde($cuenta);

        $this->assertFalse($res['aplicado']);
        $this->assertNull($cuenta->fresh()->seguro_estado);
        $this->assertSame(0, SeguroCobro::count());
    }

    public function test_aplica_seguro_vigente(): void
    {
        $cuenta = $this->cuentaConPaciente($this->seguro(), now()->addMonth()->toDateString());

        $res = AplicarSeguroService::aplicarSiCorresponde($cuenta);

        $this->assertTrue($res['aplicado']);
        $this->assertSame('autorizado', $cuenta->fresh()->seguro_estado);
        $this->assertEqualsWithDelta(80.0, $res['cubierto'], 0.001);
    }

    public function test_receptor_fiscal_usa_nit_de_la_aseguradora(): void
    {
        $seguro = $this->seguro(['nit' => '1023456789']);
        $cuenta = CuentaCobro::create(['tipo_atencion' => 'consulta_externa', 'estado' => 'pendiente']);
        $cuenta->detalles()->create([
            'tipo_item' => 'servicio', 'descripcion' => 'Consulta',
            'cantidad' => '1', 'precio_unitario' => '100.00',
        ]);
        $cuenta->load('detalles');
        $cuenta->recalcularTotales();
        $cuenta->setRelation('seguro', $seguro);
        $cuenta->autorizarSeguro($seguro, 100.00);

        $cobro = SeguroCobro::where('cuenta_cobro_id', $cuenta->id)->firstOrFail();
        $this->assertSame('1023456789', $cobro->receptorFiscal()['numero_documento']);
    }

    public function test_nro_autorizacion_se_persiste_en_la_cuenta(): void
    {
        $seguro = $this->seguro();
        $cuenta = CuentaCobro::create([
            'tipo_atencion' => 'consulta_externa', 'estado' => 'pendiente',
            'seguro_id' => $seguro->id, 'seguro_estado' => 'pendiente_autorizacion',
        ]);
        $cuenta->detalles()->create([
            'tipo_item' => 'servicio', 'descripcion' => 'Consulta',
            'cantidad' => '1', 'precio_unitario' => '100.00',
        ]);
        $cuenta->load('detalles');
        $cuenta->recalcularTotales();

        $res = $this->actingAs($this->admin())
            ->postJson(route('admin.seguros.api.cambiar-estado', ['cuentaId' => $cuenta->id]), [
                'estado' => 'autorizado',
                'nro_autorizacion' => 'AUT-9988',
            ]);

        $res->assertOk()->assertJsonPath('success', true);
        $this->assertSame('AUT-9988', $cuenta->fresh()->seguro_nro_autorizacion);
    }
}
