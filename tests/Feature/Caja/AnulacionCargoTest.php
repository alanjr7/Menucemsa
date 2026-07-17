<?php

namespace Tests\Feature\Caja;

use App\Models\CuentaCobro;
use App\Models\CuentaCobroDetalle;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Núcleo de eliminaciones seguras de cargos (anulación parcial/total reversible).
 *
 * Fuente única CuentaCobroDetalle::anular() / revertirAnulacion(), usada por las
 * tres vistas (Correcciones, Cuenta del paciente, Caja-gestión). No se borra: se
 * reduce la cantidad y se registra un evento auditable y reversible.
 */
class AnulacionCargoTest extends TestCase
{
    use RefreshDatabase;

    private function cuenta(array $attrs = []): CuentaCobro
    {
        return CuentaCobro::create(array_merge([
            'tipo_atencion' => 'consulta_externa',
            'estado'        => 'pendiente',
        ], $attrs));
    }

    private function detalle(CuentaCobro $cuenta, $cantidad, $precio, array $attrs = []): CuentaCobroDetalle
    {
        return $cuenta->detalles()->create(array_merge([
            'tipo_item'       => 'servicio',
            'descripcion'     => 'Item',
            'cantidad'        => $cantidad,
            'precio_unitario' => $precio,
        ], $attrs));
    }

    private function recalcular(CuentaCobro $cuenta): void
    {
        $cuenta->load('detalles');
        $cuenta->recalcularTotales();
    }

    // --- Anulación total ---

    public function test_anular_total_deshabilita_la_linea_y_recalcula(): void
    {
        $user = User::factory()->create();
        $cuenta = $this->cuenta();
        $d = $this->detalle($cuenta, '2', '50.00'); // subtotal 100
        $this->recalcular($cuenta);
        $this->assertSame('100.00', (string) $cuenta->fresh()->total_calculado);

        $evento = $d->anular('2', 'Cargo mal aplicado', $user->id);

        // El evento registra lo anulado
        $this->assertSame('2.00', (string) $evento->cantidad);
        $this->assertSame('100.00', (string) $evento->subtotal);
        $this->assertSame($d->id, (int) $evento->cuenta_cobro_detalle_id);
        $this->assertSame($user->id, (int) $evento->usuario_eliminacion_id);

        // La línea queda en 0 y deshabilitada (persiste, no se borra)
        $reload = CuentaCobroDetalle::conDeshabilitados()->find($d->id);
        $this->assertNotNull($reload);
        $this->assertSame('0.00', (string) $reload->cantidad);
        $this->assertNotNull($reload->deshabilitado_en);

        // Deja de facturarse y se oculta del scope normal
        $this->assertSame('0.00', (string) $cuenta->fresh()->total_calculado);
        $this->assertNull(CuentaCobroDetalle::find($d->id));
    }

    // --- Anulación parcial ---

    public function test_anular_parcial_reduce_cantidad_y_mantiene_linea_activa(): void
    {
        $user = User::factory()->create();
        $cuenta = $this->cuenta();
        $d = $this->detalle($cuenta, '5', '10.00'); // 50
        $this->recalcular($cuenta);

        $evento = $d->anular('2', 'Devolución de 2 unidades', $user->id);

        $this->assertSame('2.00', (string) $evento->cantidad);
        $this->assertSame('20.00', (string) $evento->subtotal);

        $reload = $d->fresh();
        $this->assertNull($reload->deshabilitado_en);          // sigue activa
        $this->assertSame('3.00', (string) $reload->cantidad);  // 5 - 2
        $this->assertSame('30.00', (string) $reload->subtotal); // 3 * 10
        $this->assertSame('30.00', (string) $cuenta->fresh()->total_calculado);
    }

    public function test_varias_anulaciones_parciales_acumulan_hasta_deshabilitar(): void
    {
        $user = User::factory()->create();
        $cuenta = $this->cuenta();
        $d = $this->detalle($cuenta, '3', '10.00');
        $this->recalcular($cuenta);

        $d->anular('1', 'parcial 1', $user->id);
        $this->assertSame('2.00', (string) $d->fresh()->cantidad);

        $d->anular('2', 'parcial 2', $user->id);

        $reload = CuentaCobroDetalle::conDeshabilitados()->find($d->id);
        $this->assertSame('0.00', (string) $reload->cantidad);
        $this->assertNotNull($reload->deshabilitado_en);
        $this->assertSame(2, $reload->anulaciones()->count());
        $this->assertSame('0.00', (string) $cuenta->fresh()->total_calculado);
    }

    public function test_anular_mas_de_lo_disponible_se_limita_a_la_cantidad_viva(): void
    {
        $user = User::factory()->create();
        $cuenta = $this->cuenta();
        $d = $this->detalle($cuenta, '2', '10.00');
        $this->recalcular($cuenta);

        $evento = $d->anular('10', 'todo', $user->id); // clamp a 2

        $this->assertSame('2.00', (string) $evento->cantidad);
        $reload = CuentaCobroDetalle::conDeshabilitados()->find($d->id);
        $this->assertSame('0.00', (string) $reload->cantidad);
        $this->assertNotNull($reload->deshabilitado_en);
    }

    // --- Guards ---

    public function test_anular_cantidad_invalida_lanza_excepcion(): void
    {
        $user = User::factory()->create();
        $cuenta = $this->cuenta();
        $d = $this->detalle($cuenta, '2', '10.00');

        $this->expectException(\InvalidArgumentException::class);
        $d->anular('0', 'sin sentido', $user->id);
    }

    public function test_no_se_puede_anular_un_cargo_liquidado(): void
    {
        $user = User::factory()->create();
        $cuenta = $this->cuenta();
        $d = $this->detalle($cuenta, '1', '10.00', ['liquidado_en' => now()]);

        $this->expectException(\RuntimeException::class);
        $d->anular('1', 'ya pagado', $user->id);
    }

    /**
     * Hueco del pago PARCIAL: ningún cargo queda "liquidado" hasta saldar del todo,
     * así que el flag liquidado_en no protege el dinero ya pagado de una cuenta a
     * medio pagar. El guard del saldo pendiente sí lo protege.
     */
    public function test_no_se_puede_anular_mas_que_el_saldo_pendiente_en_pago_parcial(): void
    {
        $user = User::factory()->create();
        $cuenta = $this->cuenta();
        $d = $this->detalle($cuenta, '1', '150.00'); // total 150
        $cuenta->total_pagado = '100.00';            // pago parcial: saldo 50
        $this->recalcular($cuenta);
        $this->assertSame('parcial', $cuenta->fresh()->estado);

        // Anular el cargo (150) tocaría dinero ya pagado: solo hay 50 de saldo.
        $this->expectException(\RuntimeException::class);
        $d->anular('1', 'intento sobre lo pagado', $user->id);
    }

    public function test_se_puede_anular_dentro_del_saldo_pendiente_en_pago_parcial(): void
    {
        $user = User::factory()->create();
        $cuenta = $this->cuenta();
        $grande  = $this->detalle($cuenta, '1', '100.00');
        $pequeno = $this->detalle($cuenta, '1', '30.00'); // total 130
        $cuenta->total_pagado = '100.00';                 // pago parcial: saldo 30
        $this->recalcular($cuenta);

        // El cargo de 30 cabe en el saldo de 30 → se anula.
        $evento = $pequeno->anular('1', 'cargo no pagado', $user->id);
        $this->assertSame('30.00', (string) $evento->subtotal);
        $this->assertNull(CuentaCobroDetalle::find($pequeno->id)); // deshabilitado

        // El cargo de 100 excede el saldo ya consumido → bloqueado.
        $this->expectException(\RuntimeException::class);
        $grande->fresh()->anular('1', 'sobre lo pagado', $user->id);
    }

    // --- Reversión ---

    public function test_revertir_anulacion_total_reactiva_la_linea(): void
    {
        $user = User::factory()->create();
        $cuenta = $this->cuenta();
        $d = $this->detalle($cuenta, '2', '50.00'); // 100
        $this->recalcular($cuenta);

        $evento = $d->anular('2', 'error', $user->id);
        $this->assertSame('0.00', (string) $cuenta->fresh()->total_calculado);

        // La línea quedó oculta: se recupera con conDeshabilitados para revertir
        $disabled = CuentaCobroDetalle::conDeshabilitados()->find($d->id);
        $disabled->revertirAnulacion($evento, $user->id);

        $reload = $d->fresh(); // vuelve a ser visible
        $this->assertNotNull($reload);
        $this->assertNull($reload->deshabilitado_en);
        $this->assertSame('2.00', (string) $reload->cantidad);
        $this->assertSame('100.00', (string) $cuenta->fresh()->total_calculado);
        $this->assertNotNull($evento->fresh()->revertido_en);
        $this->assertSame($user->id, (int) $evento->fresh()->revertido_por);
    }

    public function test_revertir_anulacion_parcial_devuelve_las_unidades(): void
    {
        $user = User::factory()->create();
        $cuenta = $this->cuenta();
        $d = $this->detalle($cuenta, '5', '10.00');
        $this->recalcular($cuenta);

        $evento = $d->anular('2', 'parcial', $user->id);
        $this->assertSame('3.00', (string) $d->fresh()->cantidad);

        $d->revertirAnulacion($evento, $user->id);

        $this->assertSame('5.00', (string) $d->fresh()->cantidad);
        $this->assertSame('50.00', (string) $cuenta->fresh()->total_calculado);
    }

    public function test_revertir_es_idempotente(): void
    {
        $user = User::factory()->create();
        $cuenta = $this->cuenta();
        $d = $this->detalle($cuenta, '5', '10.00');
        $this->recalcular($cuenta);

        $evento = $d->anular('2', 'parcial', $user->id);
        $d->revertirAnulacion($evento, $user->id);
        // Segunda reversión: no vuelve a sumar unidades
        $d->revertirAnulacion($evento->fresh(), $user->id);

        $this->assertSame('5.00', (string) $d->fresh()->cantidad);
    }

    public function test_revertir_anulacion_de_otra_linea_lanza_excepcion(): void
    {
        $user = User::factory()->create();
        $cuenta = $this->cuenta();
        $d1 = $this->detalle($cuenta, '2', '10.00');
        $d2 = $this->detalle($cuenta, '2', '10.00');
        $this->recalcular($cuenta);

        $evento = $d1->anular('1', 'm', $user->id);

        $this->expectException(\InvalidArgumentException::class);
        $d2->revertirAnulacion($evento, $user->id);
    }

    public function test_no_se_puede_revertir_si_el_cargo_quedo_liquidado(): void
    {
        $user = User::factory()->create();
        $cuenta = $this->cuenta();
        $d = $this->detalle($cuenta, '5', '10.00');
        $this->recalcular($cuenta);

        $evento = $d->anular('2', 'parcial', $user->id);
        // El cargo se salda luego por un pago
        $d->update(['liquidado_en' => now()]);

        $this->expectException(\RuntimeException::class);
        $d->fresh()->revertirAnulacion($evento, $user->id);
    }
}
