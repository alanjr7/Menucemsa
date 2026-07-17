<?php

namespace Tests\Feature\Caja;

use App\Models\CuentaCobro;
use App\Models\CuentaCobroDetalle;
use App\Models\Seguro;
use App\Models\User;
use App\Support\Money;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Núcleo de cobro (caja): aritmética monetaria, recálculo de totales,
 * pagos parciales/totales, liquidación de cargos y cobertura de seguro.
 *
 * Es el área de mayor riesgo del HMS (dinero del paciente), por eso se
 * prueba la lógica de dominio directamente sobre los modelos.
 */
class CobroNucleoTest extends TestCase
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

    // --- Subtotal con BCMath/Money ---

    public function test_subtotal_se_calcula_con_money_half_up(): void
    {
        $cuenta = $this->cuenta();
        // 2.5 * 3.33 = 8.325 -> half-up -> 8.33
        $d = $this->detalle($cuenta, '2.50', '3.33');

        $this->assertSame('8.33', (string) $d->subtotal);
    }

    public function test_subtotal_se_recalcula_al_cambiar_cantidad(): void
    {
        $cuenta = $this->cuenta();
        $d = $this->detalle($cuenta, '1', '10.00');
        $this->assertSame('10.00', (string) $d->subtotal);

        $d->update(['cantidad' => '3']);
        $this->assertSame('30.00', (string) $d->fresh()->subtotal);
    }

    // --- Recálculo de totales ---

    public function test_recalcular_suma_solo_cargos_activos(): void
    {
        $cuenta = $this->cuenta();
        $this->detalle($cuenta, '1', '100.00');
        $this->detalle($cuenta, '2', '25.50'); // 51.00

        $cuenta->load('detalles');
        $cuenta->recalcularTotales();

        $this->assertSame('151.00', (string) $cuenta->total_calculado);
        $this->assertSame('pendiente', $cuenta->estado);
    }

    public function test_recalcular_excluye_cargos_deshabilitados(): void
    {
        $cuenta = $this->cuenta();
        $this->detalle($cuenta, '1', '100.00');
        $this->detalle($cuenta, '1', '40.00', ['deshabilitado_en' => now()]);

        $cuenta->load('detalles');
        $cuenta->recalcularTotales();

        // El cargo deshabilitado (40) no se factura
        $this->assertSame('100.00', (string) $cuenta->total_calculado);
    }

    // --- Pagos ---

    public function test_pago_parcial_deja_estado_parcial_y_saldo_correcto(): void
    {
        $user = User::factory()->create();
        $cuenta = $this->cuenta();
        $this->detalle($cuenta, '1', '200.00');
        $cuenta->load('detalles');
        $cuenta->recalcularTotales();

        $cuenta->registrarPago(50.00, 'efectivo', null, $user->id);
        $cuenta->refresh();

        $this->assertSame('parcial', $cuenta->estado);
        $this->assertSame('50.00', (string) $cuenta->total_pagado);
        $this->assertEqualsWithDelta(150.00, $cuenta->saldo_pendiente, 0.001);
        // En pago parcial nada se liquida
        $this->assertNull($cuenta->detalles()->first()->liquidado_en);
    }

    public function test_pago_total_deja_estado_pagado_y_liquida_cargos(): void
    {
        $user = User::factory()->create();
        $cuenta = $this->cuenta();
        $this->detalle($cuenta, '1', '200.00');
        $cuenta->load('detalles');
        $cuenta->recalcularTotales();

        $cuenta->registrarPago(200.00, 'efectivo', null, $user->id);
        $cuenta->refresh();

        $this->assertSame('pagado', $cuenta->estado);
        $this->assertEqualsWithDelta(0.0, $cuenta->saldo_pendiente, 0.001);
        $this->assertNotNull($cuenta->detalles()->first()->liquidado_en);
    }

    public function test_sobrepago_no_genera_saldo_negativo(): void
    {
        $user = User::factory()->create();
        $cuenta = $this->cuenta();
        $this->detalle($cuenta, '1', '100.00');
        $cuenta->load('detalles');
        $cuenta->recalcularTotales();

        $cuenta->registrarPago(120.00, 'efectivo', null, $user->id);
        $cuenta->refresh();

        $this->assertSame('pagado', $cuenta->estado);
        $this->assertEqualsWithDelta(0.0, $cuenta->saldo_pendiente, 0.001);
    }

    public function test_metodo_pago_invalido_cae_a_efectivo(): void
    {
        $user = User::factory()->create();
        $cuenta = $this->cuenta();
        $this->detalle($cuenta, '1', '10.00');
        $cuenta->load('detalles');
        $cuenta->recalcularTotales();

        $cuenta->registrarPago(10.00, 'cheque_raro', null, $user->id);

        $this->assertSame('efectivo', $cuenta->pagos()->first()->metodo_pago);
    }

    public function test_dos_pagos_parciales_suman_hasta_pagar(): void
    {
        $user = User::factory()->create();
        $cuenta = $this->cuenta();
        $this->detalle($cuenta, '1', '100.00');
        $cuenta->load('detalles');
        $cuenta->recalcularTotales();

        $cuenta->registrarPago(60.00, 'efectivo', null, $user->id);
        $cuenta->registrarPago(40.00, 'qr', null, $user->id);
        $cuenta->refresh();

        $this->assertSame('pagado', $cuenta->estado);
        $this->assertSame('100.00', (string) $cuenta->total_pagado);
        $this->assertSame(2, $cuenta->pagos()->count());
    }

    // --- Idempotencia: token por intento de cobro ---

    public function test_token_repetido_no_duplica_el_pago(): void
    {
        $user = User::factory()->create();
        $cuenta = $this->cuenta();
        $this->detalle($cuenta, '1', '100.00');
        $cuenta->load('detalles');
        $cuenta->recalcularTotales();

        // Primer intento: crea el pago y devuelve true
        $creado1 = $cuenta->registrarPago(100.00, 'efectivo', null, $user->id, 'tok-abc');
        // Reintento con el mismo token (doble-click / red): replay, no crea nada
        $creado2 = $cuenta->registrarPago(100.00, 'efectivo', null, $user->id, 'tok-abc');

        $this->assertTrue($creado1);
        $this->assertFalse($creado2);
        $this->assertSame(1, $cuenta->pagos()->count());
        $cuenta->refresh();
        // El total pagado no se infla por el reintento
        $this->assertSame('100.00', (string) $cuenta->total_pagado);
        $this->assertEqualsWithDelta(0.0, $cuenta->saldo_pendiente, 0.001);
    }

    public function test_tokens_distintos_registran_pagos_distintos(): void
    {
        $user = User::factory()->create();
        $cuenta = $this->cuenta();
        $this->detalle($cuenta, '1', '100.00');
        $cuenta->load('detalles');
        $cuenta->recalcularTotales();

        $cuenta->registrarPago(60.00, 'efectivo', null, $user->id, 'tok-1');
        $cuenta->registrarPago(40.00, 'qr', null, $user->id, 'tok-2');
        $cuenta->refresh();

        $this->assertSame(2, $cuenta->pagos()->count());
        $this->assertSame('100.00', (string) $cuenta->total_pagado);
        $this->assertSame('pagado', $cuenta->estado);
    }

    // --- Seguro: cobertura ---

    public function test_seguro_porcentaje_calcula_cobertura_y_copago(): void
    {
        $seguro = Seguro::create([
            'formulario'    => 'FORM-TEST',
            'nombre_empresa'       => 'ASEG',
            'tipo'                 => 'privado',
            'estado'               => 'activo',
            'tipo_cobertura'       => 'porcentaje',
            'cobertura_porcentaje' => '80.00',
            'copago_porcentaje'    => '20.00',
        ]);

        $c = $seguro->calcularCobertura(150.00);

        $this->assertSame('120.00', $c['monto_cubierto']);
        $this->assertSame('30.00', $c['monto_paciente']);
    }

    public function test_seguro_tope_monto_limita_cobertura(): void
    {
        $seguro = Seguro::create([
            'formulario'    => 'FORM-TEST',
            'nombre_empresa' => 'ASEG',
            'tipo'           => 'privado',
            'estado'         => 'activo',
            'tipo_cobertura' => 'tope_monto',
            'tope_monto'     => '100.00',
        ]);

        $c = $seguro->calcularCobertura(150.00);
        $this->assertSame('100.00', $c['monto_cubierto']);
        $this->assertSame('50.00', $c['monto_paciente']);

        // Si el total es menor al tope, cubre el total
        $c2 = $seguro->calcularCobertura(70.00);
        $this->assertSame('70.00', $c2['monto_cubierto']);
        $this->assertSame('0.00', $c2['monto_paciente']);
    }

    public function test_seguro_solo_consulta_cubre_todo(): void
    {
        $seguro = Seguro::create([
            'formulario'    => 'FORM-TEST',
            'nombre_empresa' => 'ASEG',
            'tipo'           => 'publico',
            'estado'         => 'activo',
            'tipo_cobertura' => 'solo_consulta',
        ]);

        $c = $seguro->calcularCobertura(80.00);
        $this->assertSame('80.00', $c['monto_cubierto']);
        $this->assertSame('0.00', $c['monto_paciente']);
    }

    public function test_cuenta_con_seguro_autorizado_descuenta_cobertura_del_saldo(): void
    {
        $seguro = Seguro::create([
            'formulario'    => 'FORM-TEST',
            'nombre_empresa'       => 'ASEG',
            'tipo'                 => 'privado',
            'estado'               => 'activo',
            'tipo_cobertura'       => 'porcentaje',
            'cobertura_porcentaje' => '70.00',
            'copago_porcentaje'    => '30.00',
        ]);

        $cuenta = $this->cuenta([
            'seguro_id'     => $seguro->id,
            'seguro_estado' => 'autorizado',
        ]);
        $this->detalle($cuenta, '1', '100.00');
        $cuenta->load(['detalles', 'seguro']);
        $cuenta->recalcularTotales();
        $cuenta->refresh();

        // Cobertura 70 -> el paciente debe 30
        $this->assertSame('70.00', (string) $cuenta->seguro_monto_cobertura);
        $this->assertSame('30.00', (string) $cuenta->seguro_monto_paciente);
        $this->assertEqualsWithDelta(30.00, $cuenta->saldo_pendiente, 0.001);
        // Con cobertura > 0 y sin pago, queda parcial
        $this->assertSame('parcial', $cuenta->estado);
    }

    public function test_seguro_que_cubre_100_deja_cuenta_pagada(): void
    {
        $seguro = Seguro::create([
            'formulario'    => 'FORM-TEST',
            'nombre_empresa' => 'ASEG',
            'tipo'           => 'publico',
            'estado'         => 'activo',
            'tipo_cobertura' => 'solo_consulta',
        ]);

        $cuenta = $this->cuenta([
            'seguro_id'     => $seguro->id,
            'seguro_estado' => 'autorizado',
        ]);
        $this->detalle($cuenta, '1', '100.00');
        $cuenta->load(['detalles', 'seguro']);
        $cuenta->recalcularTotales();
        $cuenta->refresh();

        $this->assertSame('pagado', $cuenta->estado);
        $this->assertEqualsWithDelta(0.0, $cuenta->saldo_pendiente, 0.001);
    }

    // --- Money helper: precisión de punto flotante ---

    public function test_money_evita_error_de_punto_flotante(): void
    {
        $this->assertSame('0.30', Money::add(0.1, 0.2));
        $this->assertSame('0.00', Money::sub(0.3, 0.3));
        $this->assertSame('0.00', Money::div(5, 0)); // división por cero segura
        $this->assertSame('8.33', Money::mul('2.5', '3.33')); // half-up
    }
}
