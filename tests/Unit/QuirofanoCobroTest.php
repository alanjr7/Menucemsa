<?php

namespace Tests\Unit;

use App\Models\CitaQuirurgica;
use PHPUnit\Framework\TestCase;

/**
 * Cobro de cirugía: regla de 3 sobre la duración con PISO en el costo base.
 * Fórmula centralizada en CitaQuirurgica::calcularCobroCirugia (antes triplicada).
 */
class QuirofanoCobroTest extends TestCase
{
    public function test_cirugia_mas_corta_que_la_base_no_baja_del_costo_base(): void
    {
        // BUG documentado: cirugía programada mayor (120 min) que termina en 30 min.
        // Regla de 3 cruda = 500*30/120 = 125 -> ANTES facturaba 125 (por debajo del base).
        // Ahora el cobro tiene piso en el base: 500, extra 0.
        $c = CitaQuirurgica::calcularCobroCirugia('500.00', 30, 120);

        $this->assertSame('500.00', $c['cirugia']);
        $this->assertSame('0.00', $c['extra']);
        $this->assertSame('500.00', $c['base']);
    }

    public function test_cirugia_que_se_excede_cobra_extra_por_regla_de_3(): void
    {
        // Programada menor (60 min), tomó 120 -> 500*120/60 = 1000, extra 500.
        $c = CitaQuirurgica::calcularCobroCirugia('500.00', 120, 60);

        $this->assertSame('1000.00', $c['cirugia']);
        $this->assertSame('500.00', $c['extra']);
    }

    public function test_cirugia_en_la_duracion_exacta_cobra_el_base(): void
    {
        $c = CitaQuirurgica::calcularCobroCirugia('500.00', 60, 60);

        $this->assertSame('500.00', $c['cirugia']);
        $this->assertSame('0.00', $c['extra']);
    }

    public function test_excedente_parcial_calcula_extra_proporcional(): void
    {
        // 300 base, 90 min reales sobre base 60 -> 300*90/60 = 450, extra 150.
        $c = CitaQuirurgica::calcularCobroCirugia('300.00', 90, 60);

        $this->assertSame('450.00', $c['cirugia']);
        $this->assertSame('150.00', $c['extra']);
    }

    public function test_duracion_base_cero_no_divide_por_cero(): void
    {
        // Sin duración de referencia válida -> cae a la duración real (regla3 = base).
        $c = CitaQuirurgica::calcularCobroCirugia('400.00', 50, 0);

        $this->assertSame('400.00', $c['cirugia']);
        $this->assertSame('0.00', $c['extra']);
    }
}
