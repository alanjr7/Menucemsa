<?php

namespace Tests\Feature\Caja;

use App\Models\CierreContable;
use App\Models\CuentaCobro;
use App\Models\CuentaCobroDetalle;
use App\Models\Devolucion;
use App\Models\PagoCuenta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Devoluciones / Notas de Crédito (contra-ingreso): el pago original es
 * inmutable; la NC resta de los ingresos, revierte débito fiscal IVA y
 * reabre el saldo de la cuenta (des-liquida los cargos del pago devuelto).
 */
class DevolucionTest extends TestCase
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

    private function gerente(): User
    {
        return User::where('email', 'gerente@ClinicaSantaCruz.com')->firstOrFail();
    }

    /** Cuenta con un cargo pagado por completo; devuelve [cuenta, pago]. */
    private function cuentaPagada(string $monto = '200.00'): array
    {
        $cuenta = CuentaCobro::create([
            'tipo_atencion' => 'consulta_externa',
            'estado'        => 'pendiente',
        ]);
        $cuenta->detalles()->create([
            'tipo_item'       => 'servicio',
            'descripcion'     => 'Servicio de prueba',
            'cantidad'        => '1',
            'precio_unitario' => $monto,
        ]);
        $cuenta->load('detalles');
        $cuenta->recalcularTotales();
        $cuenta->registrarPago((float) $monto, 'efectivo', null, $this->admin()->id);
        $cuenta->refresh();

        return [$cuenta, $cuenta->pagos()->first()];
    }

    private function datos(array $extra = []): array
    {
        return array_merge([
            'monto' => '50.00',
            'metodo_devolucion' => 'efectivo',
            'motivo' => 'Cobro en exceso',
            'user_id' => $this->admin()->id,
        ], $extra);
    }

    // --- Núcleo de dominio ---
    // La devolución reversa la VENTA completa: resta el dinero Y anula cargos
    // por el monto devuelto. La cuenta queda saldada (no reaparece en caja).

    public function test_devolucion_parcial_anula_cargo_equivalente_y_revierte_debito_fiscal(): void
    {
        [$cuenta, $pago] = $this->cuentaPagada('200.00');

        $nc = Devolucion::registrarPara($pago, $this->datos(['monto' => '50.00']));

        $this->assertNotNull($nc);
        $this->assertMatchesRegularExpression('/^NC-\d{4}-\d{6}$/', $nc->id);
        // Débito fiscal IVA revertido = 13% por dentro del monto devuelto
        $this->assertSame('50.00', (string) $nc->base_imponible);
        $this->assertSame('6.50', (string) $nc->debito_fiscal);

        // El pago original NO se toca; la cuenta neta el cargo y queda saldada
        $this->assertSame('200.00', (string) $pago->fresh()->monto);
        $cuenta->refresh();
        $this->assertSame('150.00', (string) $cuenta->total_pagado);
        $this->assertSame('150.00', (string) $cuenta->total_calculado); // cargo reducido en 50
        $this->assertSame('pagado', $cuenta->estado);
        $this->assertEqualsWithDelta(0.0, $cuenta->saldo_pendiente, 0.001);
        $this->assertSame('0.00', $nc->residuoSinAnular);

        // La línea quedó en 0.75 unidades (0.25 × 200 = 50 anulado) y trazada a la NC
        $detalle = $cuenta->detalles()->first();
        $this->assertSame('0.75', (string) $detalle->cantidad);
        $this->assertDatabaseHas('cuenta_cobro_detalle_eliminados', [
            'cuenta_cobro_id' => $cuenta->id,
            'devolucion_id' => $nc->id,
            'subtotal' => '50.00',
        ]);
    }

    public function test_devolucion_total_anula_todos_los_cargos_y_salda_la_cuenta(): void
    {
        [$cuenta, $pago] = $this->cuentaPagada('200.00');
        $this->assertNotNull($cuenta->detalles()->first()->liquidado_en);

        $nc = Devolucion::registrarPara($pago, $this->datos(['monto' => '200.00']));

        $cuenta->refresh();
        $this->assertSame('0.00', (string) $cuenta->total_pagado);
        $this->assertSame('0.00', (string) $cuenta->total_calculado);
        // Saldada en 0: NO reaparece en la lista de cobro de caja-operativa
        $this->assertSame('pagado', $cuenta->estado);
        $this->assertEqualsWithDelta(0.0, $cuenta->saldo_pendiente, 0.001);
        $this->assertSame('0.00', $nc->residuoSinAnular);

        // El cargo quedó anulado por completo (deshabilitado, oculto por el scope)
        $this->assertNull($cuenta->detalles()->first());
        $detalle = $cuenta->detalles()->conDeshabilitados()->first();
        $this->assertNotNull($detalle->deshabilitado_en);
        $this->assertSame('0.00', (string) $detalle->cantidad);
    }

    public function test_no_se_puede_devolver_mas_que_el_pago(): void
    {
        [, $pago] = $this->cuentaPagada('100.00');

        $this->expectException(\RuntimeException::class);
        Devolucion::registrarPara($pago, $this->datos(['monto' => '100.01']));
    }

    public function test_tope_considera_devoluciones_previas(): void
    {
        [$cuenta, $pago] = $this->cuentaPagada('100.00');

        Devolucion::registrarPara($pago, $this->datos(['monto' => '70.00']));
        $this->assertSame('30.00', Devolucion::montoDisponible($pago));

        // Queda Bs 30 disponibles: devolver 40 debe fallar sin efectos colaterales
        try {
            Devolucion::registrarPara($pago, $this->datos(['monto' => '40.00']));
            $this->fail('Debió rechazar la devolución que excede el disponible');
        } catch (\RuntimeException) {
            // esperado
        }

        $cuenta->refresh();
        $this->assertSame('30.00', (string) $cuenta->total_pagado);
        $this->assertSame(1, Devolucion::count());

        // Devolver exactamente el disponible sí procede
        Devolucion::registrarPara($pago, $this->datos(['monto' => '30.00']));
        $this->assertSame('0.00', Devolucion::montoDisponible($pago));
    }

    public function test_token_repetido_no_duplica_la_devolucion(): void
    {
        [$cuenta, $pago] = $this->cuentaPagada('100.00');

        $nc1 = Devolucion::registrarPara($pago, $this->datos(['monto' => '40.00', 'idempotency_key' => 'tok-dev-1']));
        $nc2 = Devolucion::registrarPara($pago, $this->datos(['monto' => '40.00', 'idempotency_key' => 'tok-dev-1']));

        $this->assertNotNull($nc1);
        $this->assertNull($nc2); // replay: no crea ni re-aplica
        $this->assertSame(1, Devolucion::count());
        $this->assertSame('60.00', (string) $cuenta->fresh()->total_pagado);
    }

    public function test_correlativo_nc_incrementa_sin_saltos(): void
    {
        [, $pago] = $this->cuentaPagada('100.00');

        $a = Devolucion::registrarPara($pago, $this->datos(['monto' => '10.00']));
        $b = Devolucion::registrarPara($pago, $this->datos(['monto' => '10.00']));

        $na = (int) substr($a->id, -6);
        $nb = (int) substr($b->id, -6);
        $this->assertSame($na + 1, $nb);
    }

    public function test_anular_nc_restaura_pago_y_cargos(): void
    {
        [$cuenta, $pago] = $this->cuentaPagada('200.00');
        $nc = Devolucion::registrarPara($pago, $this->datos(['monto' => '200.00']));
        $this->assertSame('0.00', (string) $cuenta->fresh()->total_calculado);

        $nc->anular($this->admin()->id, 'Devolución registrada por error');

        // Simetría exacta: vuelven el dinero Y los cargos que la NC anuló
        $cuenta->refresh();
        $this->assertSame('200.00', (string) $cuenta->total_pagado);
        $this->assertSame('200.00', (string) $cuenta->total_calculado);
        $this->assertSame('pagado', $cuenta->estado);

        $detalle = $cuenta->detalles()->first();
        $this->assertNotNull($detalle); // reactivado (ya no está deshabilitado)
        $this->assertSame('1.00', (string) $detalle->cantidad);
        $this->assertNotNull($detalle->liquidado_en);

        // La NC anulada no cuenta como devuelta; su evento quedó revertido
        $this->assertSame('200.00', Devolucion::montoDisponible($pago));
        $this->assertSame(0, Devolucion::vigentes()->count());
        $this->assertSame(1, Devolucion::count()); // pero el registro persiste (auditoría)
        $this->assertSame(0, \App\Models\CuentaCobroDetalleEliminado::where('devolucion_id', $nc->id)->vigentes()->count());
    }

    public function test_revertir_anulacion_vuelve_a_aplicar_devolucion_y_anulacion_de_cargos(): void
    {
        [$cuenta, $pago] = $this->cuentaPagada('200.00');
        $nc = Devolucion::registrarPara($pago, $this->datos(['monto' => '80.00']));
        $nc->anular($this->admin()->id, 'error');
        $this->assertSame('200.00', (string) $cuenta->fresh()->total_pagado);
        $this->assertSame('200.00', (string) $cuenta->fresh()->total_calculado);

        $nc->revertirAnulacion();

        $cuenta->refresh();
        $this->assertSame('120.00', (string) $cuenta->total_pagado);
        $this->assertSame('120.00', (string) $cuenta->total_calculado); // cargos re-anulados
        $this->assertSame('pagado', $cuenta->estado);
        $this->assertSame('120.00', Devolucion::montoDisponible($pago));
    }

    // --- Caso "error de cobro" (anula_cargos = false): el cargo se mantiene ---

    public function test_error_de_cobro_mantiene_el_cargo_y_reabre_el_saldo(): void
    {
        // Se cobró mal el monto: se devuelve el dinero pero el servicio SÍ se va a
        // cumplir → el cargo queda intacto y la cuenta vuelve a cobro.
        [$cuenta, $pago] = $this->cuentaPagada('200.00');

        $nc = Devolucion::registrarPara($pago, $this->datos([
            'monto' => '200.00',
            'anula_cargos' => false,
            'motivo' => 'Monto mal ingresado',
        ]));

        $cuenta->refresh();
        $this->assertSame('0.00', (string) $cuenta->total_pagado);
        $this->assertSame('200.00', (string) $cuenta->total_calculado); // cargo intacto
        $this->assertSame('pendiente', $cuenta->estado); // vuelve a la lista de caja
        $this->assertEqualsWithDelta(200.00, $cuenta->saldo_pendiente, 0.001);
        $this->assertSame('1.00', (string) $cuenta->detalles()->first()->cantidad);
        // Sin anulaciones de cargos ni residuo
        $this->assertSame(0, \App\Models\CuentaCobroDetalleEliminado::where('devolucion_id', $nc->id)->count());
        $this->assertSame('0.00', $nc->residuoSinAnular);
        // El cargo quedó des-liquidado: el re-cobro lo vuelve a liquidar normal
        $this->assertNull($cuenta->detalles()->first()->liquidado_en);

        // Re-cobro correcto: el ciclo se cierra como cualquier pago
        $cuenta->registrarPago(200.00, 'efectivo', null, $this->admin()->id);
        $cuenta->refresh();
        $this->assertSame('pagado', $cuenta->estado);
        $this->assertSame('200.00', (string) $cuenta->total_pagado);
    }

    public function test_anular_nc_de_error_de_cobro_restituye_solo_el_dinero(): void
    {
        [$cuenta, $pago] = $this->cuentaPagada('200.00');
        $nc = Devolucion::registrarPara($pago, $this->datos([
            'monto' => '200.00',
            'anula_cargos' => false,
        ]));
        $this->assertSame('pendiente', $cuenta->fresh()->estado);

        $nc->anular($this->admin()->id, 'devolución registrada por error');

        $cuenta->refresh();
        $this->assertSame('200.00', (string) $cuenta->total_pagado);
        $this->assertSame('200.00', (string) $cuenta->total_calculado);
        $this->assertSame('pagado', $cuenta->estado);
        $this->assertNotNull($cuenta->detalles()->first()->liquidado_en);
    }

    public function test_endpoint_acepta_el_caso_error_de_cobro(): void
    {
        [$cuenta, $pago] = $this->cuentaPagada('150.00');

        $res = $this->actingAs($this->admin())
            ->postJson(route('caja.gestion.devoluciones.store', $pago->id), [
                'monto' => '150.00',
                'metodo_devolucion' => 'efectivo',
                'motivo' => 'monto equivocado',
                'anula_cargos' => false,
            ]);

        $res->assertOk()->assertJson(['success' => true]);
        $cuenta->refresh();
        $this->assertSame('150.00', (string) $cuenta->total_calculado);
        $this->assertSame('pendiente', $cuenta->estado);
        $this->assertFalse(Devolucion::first()->anula_cargos);
    }

    public function test_devolucion_de_sobrepago_no_toca_cargos(): void
    {
        // Sobrepago: se cobró 120 por una cuenta de 100. Devolver los 20 de más
        // NO es reversa de venta (esa plata nunca tuvo cargo detrás): los cargos
        // quedan intactos y la cuenta sigue saldada.
        $user = $this->admin();
        $cuenta = CuentaCobro::create(['tipo_atencion' => 'consulta_externa', 'estado' => 'pendiente']);
        $cuenta->detalles()->create([
            'tipo_item' => 'servicio', 'descripcion' => 'Servicio',
            'cantidad' => '1', 'precio_unitario' => '100.00',
        ]);
        $cuenta->load('detalles');
        $cuenta->recalcularTotales();
        $cuenta->registrarPago(120.00, 'efectivo', null, $user->id);
        $pago = $cuenta->pagos()->first();

        $nc = Devolucion::registrarPara($pago, $this->datos(['monto' => '20.00']));

        $cuenta->refresh();
        $this->assertSame('100.00', (string) $cuenta->total_pagado);
        $this->assertSame('100.00', (string) $cuenta->total_calculado); // cargo intacto
        $this->assertSame('pagado', $cuenta->estado);
        $this->assertSame('1.00', (string) $cuenta->detalles()->first()->cantidad);
        $this->assertSame(0, \App\Models\CuentaCobroDetalleEliminado::where('devolucion_id', $nc->id)->count());
    }

    public function test_residuo_por_precio_que_no_divide_queda_como_saldo_y_se_reporta(): void
    {
        // Línea de 3 × 70 = 210. Devolver 100: la fracción máxima anulable a 2
        // decimales es 1.42 × 70 = 99.40; los 0.60 restantes no se pueden partir
        // y quedan como saldo pendiente reportado en residuoSinAnular.
        $user = $this->admin();
        $cuenta = CuentaCobro::create(['tipo_atencion' => 'consulta_externa', 'estado' => 'pendiente']);
        $cuenta->detalles()->create([
            'tipo_item' => 'servicio', 'descripcion' => 'Sesión',
            'cantidad' => '3', 'precio_unitario' => '70.00',
        ]);
        $cuenta->load('detalles');
        $cuenta->recalcularTotales();
        $cuenta->registrarPago(210.00, 'efectivo', null, $user->id);
        $pago = $cuenta->pagos()->first();

        $nc = Devolucion::registrarPara($pago, $this->datos(['monto' => '100.00']));

        $this->assertSame('0.60', $nc->residuoSinAnular);
        $cuenta->refresh();
        $this->assertSame('110.00', (string) $cuenta->total_pagado);
        $this->assertSame('110.60', (string) $cuenta->total_calculado); // 210 − 99.40
        $this->assertSame('parcial', $cuenta->estado);
        $this->assertEqualsWithDelta(0.60, $cuenta->saldo_pendiente, 0.001);
    }

    // --- Endpoints HTTP (caja-gestion, admin|administrador) ---

    public function test_endpoint_registra_devolucion(): void
    {
        [$cuenta, $pago] = $this->cuentaPagada('150.00');

        $res = $this->actingAs($this->admin())
            ->postJson(route('caja.gestion.devoluciones.store', $pago->id), [
                'monto' => '150,00', // coma decimal (locale es) se sanea
                'metodo_devolucion' => 'efectivo',
                'motivo' => 'Servicio no realizado',
                'idempotency_key' => 'ui-tok-1',
            ]);

        $res->assertOk()->assertJson(['success' => true]);
        $this->assertSame(1, Devolucion::count());
        $this->assertSame('0.00', (string) $cuenta->fresh()->total_pagado);
    }

    public function test_endpoint_rechaza_monto_que_excede_el_disponible(): void
    {
        [, $pago] = $this->cuentaPagada('100.00');

        $res = $this->actingAs($this->admin())
            ->postJson(route('caja.gestion.devoluciones.store', $pago->id), [
                'monto' => '120.00',
                'metodo_devolucion' => 'efectivo',
                'motivo' => 'excede',
            ]);

        $res->assertStatus(422);
        $this->assertSame(0, Devolucion::count());
    }

    public function test_endpoint_exige_motivo(): void
    {
        [, $pago] = $this->cuentaPagada('100.00');

        $res = $this->actingAs($this->admin())
            ->postJson(route('caja.gestion.devoluciones.store', $pago->id), [
                'monto' => '50.00',
                'metodo_devolucion' => 'efectivo',
            ]);

        $res->assertStatus(422);
        $this->assertSame(0, Devolucion::count());
    }

    public function test_endpoint_bloquea_devolucion_en_periodo_cerrado(): void
    {
        [, $pago] = $this->cuentaPagada('100.00');
        CierreContable::create([
            'anio' => (int) now()->year,
            'mes' => (int) now()->month,
            'cerrado_at' => now(),
            'cerrado_por' => $this->admin()->id,
        ]);

        $res = $this->actingAs($this->admin())
            ->postJson(route('caja.gestion.devoluciones.store', $pago->id), [
                'monto' => '50.00',
                'metodo_devolucion' => 'efectivo',
                'motivo' => 'tarde',
            ]);

        $res->assertStatus(422);
        $this->assertSame(0, Devolucion::count());
    }

    public function test_endpoint_anular_y_revertir_nc(): void
    {
        [$cuenta, $pago] = $this->cuentaPagada('100.00');
        $nc = Devolucion::registrarPara($pago, $this->datos(['monto' => '100.00']));

        $res = $this->actingAs($this->admin())
            ->postJson(route('caja.gestion.devoluciones.anular', $nc->id), ['motivo' => 'error de registro']);
        $res->assertOk()->assertJson(['success' => true]);
        $this->assertSame('pagado', $cuenta->fresh()->estado);

        $res = $this->actingAs($this->admin())
            ->postJson(route('caja.gestion.devoluciones.revertir', $nc->id));
        $res->assertOk()->assertJson(['success' => true]);
        // Re-aplicada: dinero devuelto Y cargos anulados otra vez → cuenta en 0
        $cuenta->refresh();
        $this->assertSame('pagado', $cuenta->estado);
        $this->assertSame('0.00', (string) $cuenta->total_calculado);
        $this->assertSame('0.00', (string) $cuenta->total_pagado);
    }

    public function test_rol_sin_permiso_no_accede(): void
    {
        [, $pago] = $this->cuentaPagada('100.00');

        $res = $this->actingAs($this->gerente())
            ->postJson(route('caja.gestion.devoluciones.store', $pago->id), [
                'monto' => '50.00',
                'metodo_devolucion' => 'efectivo',
                'motivo' => 'sin permiso',
            ]);

        $res->assertStatus(403);
        $this->assertSame(0, Devolucion::count());
    }

    public function test_comprobante_reimpreso_muestra_la_devolucion_total(): void
    {
        // Reimprimir el recibo de un pago devuelto NO puede decir "MONTO PAGADO
        // Bs 200" a secas: lleva sello PAGO DEVUELTO + desglose devuelto/neto.
        [$cuenta, $pago] = $this->cuentaPagada('200.00');
        $nc = Devolucion::registrarPara($pago, $this->datos(['monto' => '200.00']));

        $res = $this->actingAs($this->admin())
            ->get(route('caja.operativa.comprobante', $cuenta->id).'?pago='.$pago->id);

        $res->assertOk();
        $res->assertSee('PAGO DEVUELTO');
        $res->assertSee($nc->id);
        $res->assertSee('DEVUELTO Bs');
        $res->assertSee('PAGADO NETO Bs');
        $res->assertSee('Devolución '.$nc->id); // fila en Forma de Pago
    }

    public function test_comprobante_con_devolucion_parcial_desglosa_sin_sello(): void
    {
        [$cuenta, $pago] = $this->cuentaPagada('200.00');
        Devolucion::registrarPara($pago, $this->datos(['monto' => '50.00']));

        $res = $this->actingAs($this->admin())
            ->get(route('caja.operativa.comprobante', $cuenta->id).'?pago='.$pago->id);

        $res->assertOk();
        $res->assertDontSee('PAGO DEVUELTO'); // el sello es solo para devolución total
        $res->assertSee('DEVUELTO Bs');
        $res->assertSee('PAGADO NETO Bs');
    }

    public function test_comprobante_sin_devolucion_no_muestra_desglose(): void
    {
        [$cuenta, $pago] = $this->cuentaPagada('200.00');

        $res = $this->actingAs($this->admin())
            ->get(route('caja.operativa.comprobante', $cuenta->id).'?pago='.$pago->id);

        $res->assertOk();
        $res->assertDontSee('PAGO DEVUELTO');
        $res->assertDontSee('DEVUELTO Bs');
        $res->assertDontSee('PAGADO NETO Bs');
    }

    // --- Página propia del módulo (menú Contabilidad → Devoluciones / N. Crédito) ---

    public function test_pagina_devoluciones_renderiza_para_admin(): void
    {
        $res = $this->actingAs($this->admin())
            ->get(route('caja.gestion.devoluciones.index'));

        $res->assertOk();
        $res->assertSee('Devoluciones / Notas de Crédito');
        $res->assertSee('Buscar el pago a devolver');
        $res->assertSee('Devoluciones emitidas');
    }

    public function test_pagina_devoluciones_bloqueada_para_gerente(): void
    {
        $this->actingAs($this->gerente())
            ->get(route('caja.gestion.devoluciones.index'))
            ->assertStatus(403);
    }

    public function test_listado_de_nc_filtra_y_expone_kpis(): void
    {
        [, $pago] = $this->cuentaPagada('200.00');
        Devolucion::registrarPara($pago, $this->datos(['monto' => '80.00']));
        $anulada = Devolucion::registrarPara($pago, $this->datos(['monto' => '20.00']));
        $anulada->anular($this->admin()->id, 'error');

        // Sin filtro: las 2 NC; KPI vigente solo suma la no anulada
        $res = $this->actingAs($this->admin())
            ->getJson(route('caja.gestion.devoluciones.listar'));
        $res->assertOk()
            ->assertJsonPath('stats.total_vigente', '80.00')
            ->assertJsonPath('stats.cantidad', 2)
            ->assertJsonPath('stats.anuladas', 1)
            ->assertJsonCount(2, 'devoluciones.data');

        // Filtro por estado
        $res = $this->actingAs($this->admin())
            ->getJson(route('caja.gestion.devoluciones.listar', ['estado' => 'anuladas']));
        $res->assertOk()->assertJsonCount(1, 'devoluciones.data');
        $this->assertTrue($res->json('devoluciones.data.0.anulado'));

        // Búsqueda por nº de recibo
        $res = $this->actingAs($this->admin())
            ->getJson(route('caja.gestion.devoluciones.listar', ['q' => $pago->id]));
        $res->assertOk()->assertJsonCount(2, 'devoluciones.data');

        // Búsqueda sin coincidencias
        $res = $this->actingAs($this->admin())
            ->getJson(route('caja.gestion.devoluciones.listar', ['q' => 'NC-9999-999999']));
        $res->assertOk()->assertJsonCount(0, 'devoluciones.data');
    }

    public function test_estado_devoluciones_de_un_pago(): void
    {
        [, $pago] = $this->cuentaPagada('100.00');
        Devolucion::registrarPara($pago, $this->datos(['monto' => '30.00']));

        $res = $this->actingAs($this->admin())
            ->getJson(route('caja.gestion.devoluciones.por-pago', $pago->id));

        $res->assertOk()
            ->assertJsonPath('pago.monto_devuelto', '30.00')
            ->assertJsonPath('pago.monto_disponible', '70.00')
            ->assertJsonCount(1, 'devoluciones');
    }
}
