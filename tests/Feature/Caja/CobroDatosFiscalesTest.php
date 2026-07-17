<?php

namespace Tests\Feature\Caja;

use App\Models\CajaSession;
use App\Models\CuentaCobro;
use App\Models\User;
use App\Support\TipoDocumento;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Captura SFE-ready del receptor en el cobro de caja: tipo de documento SIN +
 * número + complemento + razón social, con la regla "sin datos → S/N". Lleva el
 * flujo de caja a paridad con el POS de farmacia (mismo modelo de snapshot fiscal).
 */
class CobroDatosFiscalesTest extends TestCase
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

    private function cajaAbierta(User $user): CajaSession
    {
        return CajaSession::create([
            'user_id' => $user->id,
            'fecha_apertura' => now(),
            'monto_inicial' => '0.00',
            'estado' => 'abierta',
        ]);
    }

    private function cuentaConSaldo(string $precio = '100.00'): CuentaCobro
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

        return $cuenta->fresh();
    }

    // --- Regla S/N en el modelo (fuente única de render) ---

    public function test_receptor_fiscal_sin_credito_fiscal_es_sin_nombre(): void
    {
        $cuenta = new CuentaCobro(['con_credito_fiscal' => false]);
        $r = $cuenta->receptorFiscal();

        $this->assertFalse($r['con_credito_fiscal']);
        $this->assertSame(TipoDocumento::SIN_NOMBRE_RAZON, $r['razon_social']);   // S/N
        $this->assertSame(TipoDocumento::SIN_NOMBRE_TIPO->value, $r['tipo_documento']); // NIT
        $this->assertSame(TipoDocumento::SIN_NOMBRE_DOC, $r['numero_documento']); // 0
    }

    public function test_receptor_fiscal_con_credito_fiscal_devuelve_lo_capturado(): void
    {
        $cuenta = new CuentaCobro([
            'con_credito_fiscal' => true,
            'razon_social' => 'Juan Pérez',
            'factura_tipo_documento' => TipoDocumento::CI->value,
            'ci_nit_facturacion' => '12345678',
            'factura_complemento' => '1A',
        ]);
        $r = $cuenta->receptorFiscal();

        $this->assertTrue($r['con_credito_fiscal']);
        $this->assertSame('Juan Pérez', $r['razon_social']);
        $this->assertSame('CI', $r['tipo_documento_label']);
        $this->assertSame('12345678', $r['numero_documento']);
        $this->assertSame('1A', $r['complemento']);
    }

    // --- Endpoint de cobro ---

    public function test_cobro_con_credito_fiscal_persiste_datos_del_receptor(): void
    {
        $admin = $this->admin();
        $this->cajaAbierta($admin);
        $cuenta = $this->cuentaConSaldo('100.00');

        $res = $this->actingAs($admin)->postJson(route('caja.operativa.procesar-cobro'), [
            'cuenta_cobro_id' => $cuenta->id,
            'monto' => '100.00',
            'metodo_pago' => 'efectivo',
            'es_pago_total' => true,
            'con_credito_fiscal' => true,
            'factura_razon_social' => 'Empresa SRL',
            'factura_tipo_documento' => TipoDocumento::NIT->value,
            'factura_numero_documento' => '1023456789',
            'factura_complemento' => '',
        ]);

        $res->assertOk()->assertJson(['success' => true]);

        $cuenta->refresh();
        $this->assertTrue($cuenta->con_credito_fiscal);
        $this->assertSame(TipoDocumento::NIT->value, $cuenta->factura_tipo_documento);
        $this->assertSame('1023456789', $cuenta->ci_nit_facturacion);
        $this->assertSame('Empresa SRL', $cuenta->razon_social);
        $this->assertNull($cuenta->factura_complemento);
    }

    public function test_cobro_sin_credito_fiscal_no_exige_datos_y_queda_sn(): void
    {
        $admin = $this->admin();
        $this->cajaAbierta($admin);
        $cuenta = $this->cuentaConSaldo('80.00');

        $res = $this->actingAs($admin)->postJson(route('caja.operativa.procesar-cobro'), [
            'cuenta_cobro_id' => $cuenta->id,
            'monto' => '80.00',
            'metodo_pago' => 'efectivo',
            'es_pago_total' => true,
            'con_credito_fiscal' => false,
        ]);

        $res->assertOk()->assertJson(['success' => true]);

        $cuenta->refresh();
        $this->assertFalse($cuenta->con_credito_fiscal);
        $this->assertSame('pagado', $cuenta->estado);
        // Sin crédito fiscal el receptor se resuelve como S/N.
        $this->assertSame(TipoDocumento::SIN_NOMBRE_RAZON, $cuenta->receptorFiscal()['razon_social']);
    }

    public function test_credito_fiscal_exige_razon_tipo_y_numero(): void
    {
        $admin = $this->admin();
        $this->cajaAbierta($admin);
        $cuenta = $this->cuentaConSaldo('50.00');

        $res = $this->actingAs($admin)->postJson(route('caja.operativa.procesar-cobro'), [
            'cuenta_cobro_id' => $cuenta->id,
            'monto' => '50.00',
            'metodo_pago' => 'efectivo',
            'es_pago_total' => true,
            'con_credito_fiscal' => true,
            // faltan razón social / tipo / número
        ]);

        $res->assertStatus(422);
        $this->assertFalse($cuenta->fresh()->con_credito_fiscal);
    }
}
