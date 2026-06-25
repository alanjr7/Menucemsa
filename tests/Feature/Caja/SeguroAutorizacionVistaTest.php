<?php

namespace Tests\Feature\Caja;

use App\Models\CuentaCobro;
use App\Models\Paciente;
use App\Models\Seguro;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Cuadro de Autorizaciones en /admin/seguros: tabla de aceptadas/rechazadas + detalle
 * (modal "Ver" vía JSON) + hoja imprimible con todos los datos del seguro.
 */
class SeguroAutorizacionVistaTest extends TestCase
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

    private function autorizada(): CuentaCobro
    {
        $seguro = Seguro::create([
            'formulario' => 'FORM', 'nombre_empresa' => 'UNIVida - SOAT', 'tipo' => 'privado', 'nit' => '1023456789',
            'estado' => 'activo', 'tipo_cobertura' => 'porcentaje', 'cobertura_porcentaje' => '80.00', 'copago_porcentaje' => '20.00',
        ]);
        $paciente = Paciente::create([
            'ci' => 8080801, 'nombre' => 'Carlos Mendoza', 'sexo' => 'M', 'is_temp' => false,
            'seguro_id' => $seguro->id, 'seguro_poliza' => 'POL-7788',
            'seguro_vigencia_desde' => '2026-01-01', 'seguro_vigencia_hasta' => '2026-12-31',
        ]);
        $cuenta = CuentaCobro::create([
            'tipo_atencion' => 'consulta_externa', 'estado' => 'pendiente', 'paciente_id' => $paciente->id,
        ]);
        $cuenta->detalles()->create([
            'tipo_item' => 'servicio', 'descripcion' => 'Consulta', 'cantidad' => '1', 'precio_unitario' => '100.00',
        ]);
        $cuenta->load('detalles');
        $cuenta->recalcularTotales();
        $cuenta->setRelation('seguro', $seguro);
        $cuenta->autorizarSeguro($seguro, 100.00, ['nro_autorizacion' => 'AUT-555']);

        return $cuenta;
    }

    public function test_index_muestra_el_cuadro_de_autorizaciones(): void
    {
        $this->autorizada();

        $res = $this->actingAs($this->admin())->get(route('admin.seguros'));

        $res->assertOk();
        $res->assertSee('Autorizaciones');
        $res->assertSee('Carlos Mendoza');
        $res->assertSee('UNIVida - SOAT');
    }

    public function test_ver_autorizacion_devuelve_detalle_completo(): void
    {
        $cuenta = $this->autorizada();

        $res = $this->actingAs($this->admin())->getJson(route('admin.seguros.autorizacion.ver', $cuenta->id));

        $res->assertOk()->assertJsonPath('success', true);
        $res->assertJsonPath('datos.seguro.poliza', 'POL-7788');
        $res->assertJsonPath('datos.seguro.nit', '1023456789');
        $res->assertJsonPath('datos.autorizacion.nro_autorizacion', 'AUT-555');
        $res->assertJsonPath('datos.autorizacion.cobertura', '80.00');
        $res->assertJsonPath('datos.autorizacion.copago', '20.00');
    }

    public function test_imprimir_autorizacion_renderiza_hoja_con_datos(): void
    {
        $cuenta = $this->autorizada();

        $res = $this->actingAs($this->admin())->get(route('admin.seguros.autorizacion.imprimir', $cuenta->id));

        $res->assertOk();
        $res->assertSee('Autorización de Seguro');
        $res->assertSee('Carlos Mendoza');
        $res->assertSee('POL-7788');
        $res->assertSee('AUT-555');
    }

    public function test_no_imprime_una_cuenta_sin_autorizacion(): void
    {
        $cuenta = CuentaCobro::create(['tipo_atencion' => 'consulta_externa', 'estado' => 'pendiente']);

        $this->actingAs($this->admin())
            ->get(route('admin.seguros.autorizacion.imprimir', $cuenta->id))
            ->assertNotFound();
    }
}
