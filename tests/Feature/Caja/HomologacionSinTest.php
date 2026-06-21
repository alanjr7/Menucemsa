<?php

namespace Tests\Feature\Caja;

use App\Models\Dosificacion;
use App\Models\User;
use App\Support\CodigoProducto;
use App\Support\CodigoSin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Homologación SIN: el codigoProductoSin se deriva de la familia del código interno,
 * y la dosificación (numeración autorizada) expone su vigencia y siguiente número.
 * Prerrequisito de la facturación electrónica (SFE diferido).
 */
class HomologacionSinTest extends TestCase
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

    // --- CodigoSin ---

    public function test_codigo_sin_se_deriva_de_la_familia(): void
    {
        // familia 1 = bienes/medicamentos
        $this->assertSame(
            CodigoSin::POR_FAMILIA[CodigoProducto::FAMILIA_BIENES],
            CodigoSin::paraCodigoItem(CodigoProducto::format(CodigoProducto::FAMILIA_BIENES, 27))
        );
        // familia 2 = admisiones/consultas
        $this->assertSame(
            CodigoSin::POR_FAMILIA[CodigoProducto::FAMILIA_ADMISION],
            CodigoSin::paraCodigoItem(CodigoProducto::format(CodigoProducto::FAMILIA_ADMISION, 3))
        );
    }

    public function test_codigo_sin_cae_al_defecto_si_no_hay_codigo(): void
    {
        $this->assertSame(CodigoSin::DEFECTO, CodigoSin::paraCodigoItem(null));
        $this->assertSame(CodigoSin::DEFECTO, CodigoSin::paraCodigoItem(''));
    }

    // --- Dosificación ---

    public function test_activa_devuelve_la_vigente(): void
    {
        Dosificacion::create(['modalidad' => 'manual', 'activa' => false, 'rango_desde' => 1]);
        $vigente = Dosificacion::create([
            'modalidad' => 'computarizada_en_linea', 'activa' => true,
            'rango_desde' => 1, 'fecha_limite_emision' => now()->addMonth(),
        ]);

        $this->assertNotNull(Dosificacion::activa());
        $this->assertSame($vigente->id, Dosificacion::activa()->id);
    }

    public function test_activa_ignora_dosificacion_vencida(): void
    {
        Dosificacion::create([
            'modalidad' => 'manual', 'activa' => true,
            'rango_desde' => 1, 'fecha_limite_emision' => now()->subDay(),
        ]);

        $this->assertNull(Dosificacion::activa());
    }

    public function test_siguiente_numero_respeta_rango(): void
    {
        $dos = Dosificacion::create([
            'modalidad' => 'manual', 'activa' => true, 'rango_desde' => 100, 'rango_hasta' => 102,
        ]);

        $this->assertSame(100, $dos->siguienteNumero(0));   // arranca en rango_desde
        $this->assertSame(102, $dos->siguienteNumero(101));
        $this->assertNull($dos->siguienteNumero(102));       // agotada
    }

    // --- Pantalla de referencia ---

    public function test_pantalla_homologacion_renderiza(): void
    {
        $res = $this->actingAs($this->admin())->get(route('caja.contabilidad.homologacion-sin'));

        $res->assertOk();
        $res->assertSee('Homologación SIN');
        $res->assertSee('codigoProductoSin', false);
    }
}
