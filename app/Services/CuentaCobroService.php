<?php

namespace App\Services;

use App\Models\CuentaCobro;
use App\Models\CuentaCobroDetalle;
use App\Models\Tarifa;
use Illuminate\Support\Facades\DB;

/**
 * Servicio para gestionar la creación de cuentas por cobrar desde otros módulos
 * Este servicio permite integrar el flujo de caja con: Recepción, Emergencias, Hospitalización, etc.
 */
class CuentaCobroService
{
    // =========================================================================
    // CUENTA MAESTRA — Métodos principales (usar estos en todo el sistema)
    // =========================================================================

    /**
     * Obtener o crear la Cuenta Maestra única activa del paciente.
     *
     * Regla: UN paciente = UNA cuenta activa a la vez.
     * - Si existe cuenta pendiente/parcial → la retorna (todos los nuevos
     *   cargos se agregan como detalles a esa misma cuenta).
     * - Si NO existe → crea una nueva con episodio_numero incrementado.
     *
     * @param int|string $pacienteCi   CI del paciente
     * @param string     $areaOrigen   'emergencia'|'quirofano'|'internacion'|'uti'|...
     * @param int|null   $seguroId     ID del seguro (solo aplica al crear cuenta nueva)
     */
    public static function obtenerOCrearCuentaMaestra(
        int|string $pacienteCi,
        string $areaOrigen = 'general',
        ?int $seguroId = null
    ): CuentaCobro {
        return DB::transaction(function () use ($pacienteCi, $areaOrigen, $seguroId) {
            // Buscar cuenta activa existente (pendiente o pago parcial)
            $cuenta = CuentaCobro::where('paciente_ci', (string)$pacienteCi)
                ->whereIn('estado', ['pendiente', 'parcial'])
                ->where('es_post_pago', true)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($cuenta) {
                \Log::info('[CuentaMaestra] Cuenta existente reutilizada', [
                    'paciente_ci'     => $pacienteCi,
                    'cuenta_cobro_id' => $cuenta->id,
                    'area_origen'     => $areaOrigen,
                    'episodio'        => $cuenta->episodio_numero,
                ]);
                return $cuenta;
            }

            // Calcular número de episodio (nuevo ingreso del paciente)
            $ultimoEpisodio = CuentaCobro::where('paciente_ci', (string)$pacienteCi)
                ->max('episodio_numero') ?? 0;

            $tieneSeguro = false;
            if ($seguroId) {
                $seguro = \App\Models\Seguro::find($seguroId);
                $tieneSeguro = $seguro && $seguro->estado === 'activo'
                    && in_array($seguro->tipo_cobertura, ['porcentaje', 'tope_monto'], true)
                    && strtolower($seguro->nombre_empresa) !== 'particular';
            }

            $cuenta = CuentaCobro::create([
                'paciente_ci'      => (string)$pacienteCi,
                'tipo_atencion'    => 'multiple',
                'estado'           => 'pendiente',
                'total_calculado'  => 0,
                'total_pagado'     => 0,
                'es_emergencia'    => false,
                'es_post_pago'     => true,
                'episodio_numero'  => $ultimoEpisodio + 1,
                'seguro_id'        => $tieneSeguro ? $seguroId : null,
                'seguro_estado'    => $tieneSeguro ? 'pendiente_autorizacion' : null,
                'observaciones'    => 'Cuenta maestra - Episodio ' . ($ultimoEpisodio + 1),
            ]);

            \Log::info('[CuentaMaestra] Nueva cuenta creada', [
                'paciente_ci'     => $pacienteCi,
                'cuenta_cobro_id' => $cuenta->id,
                'area_origen'     => $areaOrigen,
                'episodio'        => $cuenta->episodio_numero,
            ]);

            return $cuenta;
        });
    }

    /**
     * Agregar un cargo a la cuenta maestra con deduplicación automática.
     *
     * Si ya existe un detalle con el mismo origen_type + origen_id + tipo_item
     * en la cuenta, NO lo duplica y retorna null.
     *
     * @param string      $cuentaCobroId  ID de la cuenta destino
     * @param string      $tipoItem       'medicamento'|'procedimiento'|'estadia'|...
     * @param string      $descripcion    Descripción visible en el recibo
     * @param float       $precioUnitario Precio por unidad
     * @param float       $cantidad       Cantidad
     * @param string      $areaOrigen     'emergencia'|'quirofano'|'internacion'|'uti'|...
     * @param string|null $origenType     Clase del modelo origen (ej: Emergency::class)
     * @param string|null $origenId       ID del registro origen
     * @param int|null    $tarifaId       ID de tarifa (opcional)
     */
    public static function agregarCargoConDeduplicacion(
        string  $cuentaCobroId,
        string  $tipoItem,
        string  $descripcion,
        float   $precioUnitario,
        float   $cantidad = 1,
        string  $areaOrigen = 'general',
        ?string $origenType = null,
        ?string $origenId   = null,
        ?int    $tarifaId   = null
    ): ?CuentaCobroDetalle {
        return DB::transaction(function () use (
            $cuentaCobroId, $tipoItem, $descripcion, $precioUnitario,
            $cantidad, $areaOrigen, $origenType, $origenId, $tarifaId
        ) {
            // Verificar duplicado por origen
            if ($origenType && $origenId) {
                $existe = CuentaCobroDetalle::where('cuenta_cobro_id', $cuentaCobroId)
                    ->where('origen_type', $origenType)
                    ->where('origen_id',   (string)$origenId)
                    ->where('tipo_item',   $tipoItem)
                    ->exists();

                if ($existe) {
                    \Log::info('[CuentaMaestra] Cargo omitido (duplicado detectado)', [
                        'cuenta_cobro_id' => $cuentaCobroId,
                        'tipo_item'       => $tipoItem,
                        'origen_type'     => $origenType,
                        'origen_id'       => $origenId,
                    ]);
                    return null;
                }
            }

            $cuenta = CuentaCobro::findOrFail($cuentaCobroId);

            if ($cuenta->estado === 'pagado') {
                throw new \Exception('No se pueden agregar cargos a una cuenta ya pagada completamente.');
            }

            $subtotal = round($precioUnitario * $cantidad, 2);

            $detalle = CuentaCobroDetalle::create([
                'cuenta_cobro_id' => $cuentaCobroId,
                'tipo_item'       => $tipoItem,
                'tarifa_id'       => $tarifaId,
                'descripcion'     => $descripcion,
                'cantidad'        => $cantidad,
                'precio_unitario' => $precioUnitario,
                'subtotal'        => $subtotal,
                'origen_type'     => $origenType,
                'origen_id'       => $origenId ? (string)$origenId : null,
                'area_origen'     => $areaOrigen,
            ]);

            // Recalcular total de la cuenta
            $cuenta->total_calculado = CuentaCobroDetalle::where('cuenta_cobro_id', $cuentaCobroId)
                ->sum('subtotal');
            $cuenta->save();

            return $detalle;
        });
    }


    /**
     * Crear una cuenta por cobrar para una consulta externa (flujo normal: pre-pago)
     * 
     * Flujo: Recepción → Caja cobra → Sistema habilita atención médica
     */
    public static function crearCuentaConsultaExterna(
        int $pacienteCi,
        string $consultaNro,
        int $especialidadCodigo,
        ?float $montoPersonalizado = null
    ): CuentaCobro {
        return DB::transaction(function () use ($pacienteCi, $consultaNro, $especialidadCodigo, $montoPersonalizado) {
            // Buscar tarifa según especialidad
            $tarifa = Tarifa::where('codigo', 'CONS-' . $especialidadCodigo)
                ->where('activo', true)
                ->first();

            $precio = $montoPersonalizado ?? ($tarifa ? $tarifa->precio_particular : 150.00);
            $descripcion = $tarifa ? $tarifa->descripcion : 'Consulta Externa';

            // Crear cuenta
            $cuenta = CuentaCobro::create([
                'paciente_ci' => $pacienteCi,
                'tipo_atencion' => 'consulta_externa',
                'referencia_id' => $consultaNro,
                'referencia_type' => \App\Models\Consulta::class,
                'estado' => 'pendiente',
                'total_calculado' => $precio,
                'es_emergencia' => false,
                'es_post_pago' => false,
            ]);

            // Crear detalle
            $cuenta->detalles()->create([
                'tipo_item' => 'servicio',
                'tarifa_id' => $tarifa?->id,
                'descripcion' => $descripcion,
                'cantidad' => 1,
                'precio_unitario' => $precio,
                'subtotal' => $precio,
                'origen_type' => \App\Models\Consulta::class,
                'origen_id' => $consultaNro,
            ]);

            return $cuenta;
        });
    }

    /**
     * Crear una cuenta por cobrar para una emergencia (flujo emergencia: post-pago)
     * 
     * Flujo: Recepción → Emergencia atiende → Caja cobra después
     * 
     * NOTA: Si el paciente ya tiene una cuenta post-pago activa, se unifican los detalles
     * en la cuenta existente para evitar duplicados.
     */
    public static function crearCuentaEmergencia(
        string $pacienteCi,
        string $emergencyId,
        array $servicios = [],
        bool $esPostPago = true,
        ?int $seguroId = null
    ): CuentaCobro {
        return DB::transaction(function () use ($pacienteCi, $emergencyId, $servicios, $esPostPago, $seguroId) {
            $total = 0;

            $tieneSeguroAplicable = false;
            if ($seguroId) {
                $seguro = \App\Models\Seguro::find($seguroId);
                $tieneSeguroAplicable = $seguro && $seguro->estado === 'activo'
                    && in_array($seguro->tipo_cobertura, ['porcentaje', 'tope_monto'], true)
                    && strtolower($seguro->nombre_empresa) !== 'particular';
            }

            // Buscar cuenta existente no pagada del paciente (Master Account)
            $cuentaExistente = self::obtenerCuentaPostPagoActiva((string)$pacienteCi);

            if ($cuentaExistente) {
                // Unificar en cuenta existente: agregar detalles de emergencia
                if (!empty($servicios)) {
                    foreach ($servicios as $servicio) {
                        $precio = $servicio['precio'] ?? 0;
                        $cuentaExistente->detalles()->create([
                            'tipo_item' => $servicio['tipo'] ?? 'servicio',
                            'tarifa_id' => $servicio['tarifa_id'] ?? null,
                            'descripcion' => $servicio['descripcion'] ?? 'Servicio de Emergencia',
                            'cantidad' => $servicio['cantidad'] ?? 1,
                            'precio_unitario' => $precio,
                            'subtotal' => $precio * ($servicio['cantidad'] ?? 1),
                            'origen_type' => $servicio['origen_type'] ?? \App\Models\Emergency::class,
                            'origen_id' => $servicio['origen_id'] ?? $emergencyId,
                        ]);
                        $total += $precio * ($servicio['cantidad'] ?? 1);
                    }
                } else {
                    // Precio de emergencia (con fallback al sistema anterior)
                    $precioBase = self::obtenerPrecioEmergencia();

                    $cuentaExistente->detalles()->create([
                        'tipo_item' => 'servicio',
                        'descripcion' => 'Atención de Emergencia',
                        'cantidad' => 1,
                        'precio_unitario' => $precioBase,
                        'subtotal' => $precioBase,
                        'origen_type' => \App\Models\Emergency::class,
                        'origen_id' => $emergencyId,
                    ]);
                    $total = $precioBase;
                }

                // Actualizar totales y tipo de atención (más genérico)
                $cuentaExistente->update([
                    'tipo_atencion' => 'multiple',
                    'total_calculado' => $cuentaExistente->total_calculado + $total,
                ]);

                \Log::info('Cuenta unificada: Emergencia agregada a cuenta existente', [
                    'paciente_ci' => $pacienteCi,
                    'cuenta_id' => $cuentaExistente->id,
                    'emergency_id' => $emergencyId,
                    'monto_agregado' => $total,
                ]);

                return $cuentaExistente;
            }

            // Crear cuenta nueva si no existe
            $cuenta = CuentaCobro::create([
                'paciente_ci' => $pacienteCi,
                'tipo_atencion' => 'emergencia',
                'referencia_id' => $emergencyId,
                'referencia_type' => \App\Models\Emergency::class,
                'estado' => 'pendiente',
                'total_calculado' => 0,
                'total_pagado' => 0,
                'es_emergencia' => true,
                'es_post_pago' => $esPostPago,
                'seguro_id' => $tieneSeguroAplicable ? $seguroId : null,
                'seguro_estado' => $tieneSeguroAplicable ? 'pendiente_autorizacion' : null,
            ]);

            // Si hay servicios predefinidos, agregarlos
            if (!empty($servicios)) {
                foreach ($servicios as $servicio) {
                    $precio = $servicio['precio'] ?? 0;
                    $cuenta->detalles()->create([
                        'tipo_item' => $servicio['tipo'] ?? 'servicio',
                        'tarifa_id' => $servicio['tarifa_id'] ?? null,
                        'descripcion' => $servicio['descripcion'] ?? 'Servicio de Emergencia',
                        'cantidad' => $servicio['cantidad'] ?? 1,
                        'precio_unitario' => $precio,
                        'subtotal' => $precio * ($servicio['cantidad'] ?? 1),
                        'origen_type' => $servicio['origen_type'] ?? null,
                        'origen_id' => $servicio['origen_id'] ?? null,
                    ]);
                    $total += $precio * ($servicio['cantidad'] ?? 1);
                }
            } else {
                // Precio de emergencia (con fallback al sistema anterior)
                $precioBase = self::obtenerPrecioEmergencia();

                $cuenta->detalles()->create([
                    'tipo_item' => 'servicio',
                    'descripcion' => 'Atención de Emergencia',
                    'cantidad' => 1,
                    'precio_unitario' => $precioBase,
                    'subtotal' => $precioBase,
                    'origen_type' => \App\Models\Emergency::class,
                    'origen_id' => $emergencyId,
                ]);
                $total = $precioBase;
            }

            $cuenta->update([
                'total_calculado' => $total,
            ]);

            return $cuenta;
        });
    }

    /**
     * Crear una cuenta por cobrar para una internación (flujo internación: post-pago)
     *
     * Flujo: Recepción → Internación atiende → Caja cobra después
     *
     * NOTA: Si el paciente ya tiene una cuenta post-pago activa, se unifican los detalles
     * en la cuenta existente para evitar duplicados.
     */
    public static function crearCuentaInternacion(
        string $pacienteCi,
        string $hospitalizacionId,
        array $servicios = [],
        bool $esPostPago = true,
        ?int $seguroId = null
    ): CuentaCobro {
        return DB::transaction(function () use ($pacienteCi, $hospitalizacionId, $servicios, $esPostPago, $seguroId) {
            $total = 0;

            $tieneSeguroAplicable = false;
            if ($seguroId) {
                $seguro = \App\Models\Seguro::find($seguroId);
                $tieneSeguroAplicable = $seguro && $seguro->estado === 'activo'
                    && in_array($seguro->tipo_cobertura, ['porcentaje', 'tope_monto'], true)
                    && strtolower($seguro->nombre_empresa) !== 'particular';
            }

            // Buscar cuenta existente no pagada (Master Account)
            $cuentaExistente = self::obtenerCuentaPostPagoActiva((string)$pacienteCi);

            if ($cuentaExistente) {
                // Unificar en cuenta existente: agregar detalles de internación
                if (!empty($servicios)) {
                    foreach ($servicios as $servicio) {
                        $precio = $servicio['precio'] ?? 0;
                        $cuentaExistente->detalles()->create([
                            'tipo_item' => $servicio['tipo'] ?? 'servicio',
                            'tarifa_id' => $servicio['tarifa_id'] ?? null,
                            'descripcion' => $servicio['descripcion'] ?? 'Servicio de Internación',
                            'cantidad' => $servicio['cantidad'] ?? 1,
                            'precio_unitario' => $precio,
                            'subtotal' => $precio * ($servicio['cantidad'] ?? 1),
                            'origen_type' => $servicio['origen_type'] ?? \App\Models\Hospitalizacion::class,
                            'origen_id' => $servicio['origen_id'] ?? $hospitalizacionId,
                        ]);
                        $total += $precio * ($servicio['cantidad'] ?? 1);
                    }
                } else {
                    // Precio de internación (con fallback al sistema anterior)
                    $precioBase = self::obtenerPrecioInternacion();

                    $cuentaExistente->detalles()->create([
                        'tipo_item' => 'servicio',
                        'descripcion' => 'Admisión de Internación',
                        'cantidad' => 1,
                        'precio_unitario' => $precioBase,
                        'subtotal' => $precioBase,
                        'origen_type' => \App\Models\Hospitalizacion::class,
                        'origen_id' => $hospitalizacionId,
                    ]);
                    $total = $precioBase;
                }

                // Actualizar totales y tipo de atención (más genérico)
                $cuentaExistente->update([
                    'tipo_atencion' => 'multiple',
                    'total_calculado' => $cuentaExistente->total_calculado + $total,
                ]);

                \Log::info('Cuenta unificada: Internación agregada a cuenta existente', [
                    'paciente_ci' => $pacienteCi,
                    'cuenta_id' => $cuentaExistente->id,
                    'hospitalizacion_id' => $hospitalizacionId,
                    'monto_agregado' => $total,
                ]);

                return $cuentaExistente;
            }

            // Crear cuenta nueva si no existe
            $cuenta = CuentaCobro::create([
                'paciente_ci' => $pacienteCi,
                'tipo_atencion' => 'internacion',
                'referencia_id' => $hospitalizacionId,
                'referencia_type' => \App\Models\Hospitalizacion::class,
                'estado' => 'pendiente',
                'total_calculado' => 0,
                'total_pagado' => 0,
                'es_emergencia' => false,
                'es_post_pago' => $esPostPago,
                'seguro_id' => $tieneSeguroAplicable ? $seguroId : null,
                'seguro_estado' => $tieneSeguroAplicable ? 'pendiente_autorizacion' : null,
            ]);

            // Si hay servicios predefinidos, agregarlos
            if (!empty($servicios)) {
                foreach ($servicios as $servicio) {
                    $precio = $servicio['precio'] ?? 0;
                    $cuenta->detalles()->create([
                        'tipo_item' => $servicio['tipo'] ?? 'servicio',
                        'tarifa_id' => $servicio['tarifa_id'] ?? null,
                        'descripcion' => $servicio['descripcion'] ?? 'Servicio de Internación',
                        'cantidad' => $servicio['cantidad'] ?? 1,
                        'precio_unitario' => $precio,
                        'subtotal' => $precio * ($servicio['cantidad'] ?? 1),
                        'origen_type' => $servicio['origen_type'] ?? null,
                        'origen_id' => $servicio['origen_id'] ?? null,
                    ]);
                    $total += $precio * ($servicio['cantidad'] ?? 1);
                }
            } else {
                // Precio de internación (con fallback al sistema anterior)
                $precioBase = self::obtenerPrecioInternacion();

                $cuenta->detalles()->create([
                    'tipo_item' => 'servicio',
                    'descripcion' => 'Admisión de Internación',
                    'cantidad' => 1,
                    'precio_unitario' => $precioBase,
                    'subtotal' => $precioBase,
                    'origen_type' => \App\Models\Hospitalizacion::class,
                    'origen_id' => $hospitalizacionId,
                ]);
                $total = $precioBase;
            }

            $cuenta->update([
                'total_calculado' => $total,
            ]);

            return $cuenta;
        });
    }

    /**
     * Agregar un item/cargo adicional a una cuenta existente
     * Usado para agregar servicios, medicamentos, procedimientos durante la atención
     */
    public static function agregarCargo(
        string $cuentaCobroId,
        string $tipoItem,
        string $descripcion,
        float $precioUnitario,
        float $cantidad = 1,
        ?int $tarifaId = null,
        ?string $origenType = null,
        ?string $origenId = null
    ): CuentaCobroDetalle {
        return DB::transaction(function () use ($cuentaCobroId, $tipoItem, $descripcion, $precioUnitario, $cantidad, $tarifaId, $origenType, $origenId) {
            $cuenta = CuentaCobro::findOrFail($cuentaCobroId);

            // No permitir agregar cargos si ya está pagada completamente
            if ($cuenta->estado === 'pagado') {
                throw new \Exception('No se pueden agregar cargos a una cuenta ya pagada');
            }

            $subtotal = $precioUnitario * $cantidad;

            $detalle = $cuenta->detalles()->create([
                'tipo_item' => $tipoItem,
                'tarifa_id' => $tarifaId,
                'descripcion' => $descripcion,
                'cantidad' => $cantidad,
                'precio_unitario' => $precioUnitario,
                'subtotal' => $subtotal,
                'origen_type' => $origenType,
                'origen_id' => $origenId,
            ]);

            // Recalcular totales
            $cuenta->recalcularTotales();

            return $detalle;
        });
    }

    /**
     * Agregar medicamentos desde farmacia a una cuenta
     */
    public static function agregarMedicamentosDesdeFarmacia(
        string $cuentaCobroId,
        array $medicamentos // Array de ['medicamento_id', 'descripcion', 'cantidad', 'precio_unitario']
    ): void {
        DB::transaction(function () use ($cuentaCobroId, $medicamentos) {
            $cuenta = CuentaCobro::findOrFail($cuentaCobroId);

            foreach ($medicamentos as $med) {
                // Descontar stock
                $item = \App\Models\AlmacenMedicamento::find($med['medicamento_id']);
                if ($item) {
                    if ($item->cantidad < $med['cantidad']) {
                        throw new \Exception("Stock insuficiente para: {$item->nombre}");
                    }
                    $item->decrement('cantidad', $med['cantidad']);
                }

                $cuenta->detalles()->create([
                    'tipo_item' => 'medicamento',
                    'descripcion' => $med['descripcion'],
                    'cantidad' => $med['cantidad'],
                    'precio_unitario' => $med['precio_unitario'],
                    'subtotal' => $med['cantidad'] * $med['precio_unitario'],
                    'origen_type' => \App\Models\AlmacenMedicamento::class,
                    'origen_id' => $med['medicamento_id'],
                ]);
            }

            $cuenta->recalcularTotales();
        });
    }

    /**
     * Agregar cargo por estadía/hospitalización
     */
    public static function agregarCargoEstadia(
        string $cuentaCobroId,
        string $hospitalizacionId,
        int $dias,
        float $precioPorDia,
        ?string $tipoHabitacion = null
    ): CuentaCobroDetalle {
        $descripcion = 'Estadía' . ($tipoHabitacion ? ' - ' . $tipoHabitacion : '') . " ({$dias} días)";
        
        return self::agregarCargo(
            $cuentaCobroId,
            'estadia',
            $descripcion,
            $precioPorDia,
            $dias,
            null,
            \App\Models\Hospitalizacion::class,
            $hospitalizacionId
        );
    }

    /**
     * Agregar cargo por procedimiento quirúrgico
     */
    public static function agregarCargoCirugia(
        string $cuentaCobroId,
        string $cirugiaId,
        string $nombreCirugia,
        float $precio,
        ?int $tarifaId = null
    ): CuentaCobroDetalle {
        return self::agregarCargo(
            $cuentaCobroId,
            'procedimiento',
            'Cirugía: ' . $nombreCirugia,
            $precio,
            1,
            $tarifaId,
            \App\Models\Cirugia::class,
            $cirugiaId
        );
    }

    /**
     * Agregar cargo por laboratorio o imagenología
     */
    public static function agregarCargoLaboratorio(
        string $cuentaCobroId,
        string $tipo, // 'laboratorio' o 'imagenologia'
        string $nombreExamen,
        float $precio,
        ?int $tarifaId = null
    ): CuentaCobroDetalle {
        return self::agregarCargo(
            $cuentaCobroId,
            $tipo,
            ucfirst($tipo) . ': ' . $nombreExamen,
            $precio,
            1,
            $tarifaId
        );
    }

    /**
     * Obtener cuenta activa post-pago (Emergencia o Internación) del paciente
     */
    public static function obtenerCuentaPostPagoActiva(string $pacienteCi): ?CuentaCobro
    {
        return CuentaCobro::where('paciente_ci', $pacienteCi)
            ->whereIn('estado', ['pendiente', 'parcial'])
            ->where('es_post_pago', true)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Obtener o crear cuenta para un paciente en emergencia
     * Usado durante el flujo de emergencia para ir acumulando cargos
     */
    public static function obtenerOCrearCuentaEmergencia(
        int $pacienteCi,
        string $emergencyId
    ): CuentaCobro {
        // Buscar cuenta existente no pagada (Master Account)
        $cuenta = self::obtenerCuentaPostPagoActiva((string)$pacienteCi);

        if ($cuenta) {
            // Si ya tiene una cuenta abierta, la reutilizamos para la emergencia
            // Podríamos actualizar la referencia si quisiéramos, pero por ahora solo la devolvemos
            return $cuenta;
        }

        // Crear nueva cuenta si no hay ninguna abierta
        return self::crearCuentaEmergencia($pacienteCi, $emergencyId, [], true);
    }

    /**
     * Verificar si un paciente puede recibir atención médica
     * Según las reglas: debe estar pagado (excepto emergencias)
     */
    public static function puedeRecibirAtencion(int $pacienteCi, string $tipoAtencion): bool
    {
        // Las emergencias siempre pueden recibir atención (post-pago)
        if ($tipoAtencion === 'emergencia') {
            return true;
        }

        // Para otros tipos, verificar que tenga cuenta pagada
        $cuentaPagada = CuentaCobro::where('paciente_ci', $pacienteCi)
            ->where('tipo_atencion', $tipoAtencion)
            ->where('estado', 'pagado')
            ->whereDate('created_at', today())
            ->exists();

        return $cuentaPagada;
    }

    /**
     * Marcar una emergencia como pagada (después del cobro en caja)
     */
    public static function marcarEmergenciaComoPagada(string $emergencyId): void
    {
        $emergency = \App\Models\Emergency::find($emergencyId);
        if ($emergency) {
            $emergency->update(['paid' => true]);
        }
    }

    /**
     * Obtener o crear cuenta para internación sin agregar tarifa base adicional
     * Usado durante la evaluación para agregar equipos médicos y medicamentos
     */
    public static function obtenerOCrearCuentaInternacion(
        string $pacienteCi,
        string $hospitalizacionId,
        ?int $seguroId = null
    ): CuentaCobro {
        return DB::transaction(function () use ($pacienteCi, $hospitalizacionId, $seguroId) {
            // Buscar cuenta existente no pagada (Master Account)
            $cuenta = self::obtenerCuentaPostPagoActiva($pacienteCi);

            if ($cuenta) {
                // Actualizar la referencia a la internación actual
                $cuenta->update([
                    'tipo_atencion' => 'hospitalizacion',
                    'referencia_id' => $hospitalizacionId,
                    'referencia_type' => \App\Models\Hospitalizacion::class,
                ]);
                return $cuenta;
            }

            // Crear cuenta nueva si no existe (con tarifa base)
            return self::crearCuentaInternacion($pacienteCi, $hospitalizacionId, [], true, $seguroId);
        });
    }

    /**
     * Obtener resumen de cuenta para mostrar en caja
     */
    public static function obtenerResumenCuenta(string $cuentaId): array
    {
        $cuenta = CuentaCobro::with(['paciente', 'detalles', 'pagos'])->findOrFail($cuentaId);

        return [
            'id' => $cuenta->id,
            'paciente' => [
                'ci' => $cuenta->paciente_ci,
                'nombre' => $cuenta->paciente->nombre ?? 'N/A',
            ],
            'tipo_atencion' => $cuenta->tipo_atencion_label,
            'estado' => $cuenta->estado,
            'estado_label' => $cuenta->estado_label,
            'total_calculado' => $cuenta->total_calculado,
            'total_pagado' => $cuenta->total_pagado,
            'saldo_pendiente' => $cuenta->saldo_pendiente,
            'detalles' => $cuenta->detalles->map(function ($d) {
                return [
                    'tipo' => $d->tipo_item_label,
                    'descripcion' => $d->descripcion,
                    'cantidad' => $d->cantidad,
                    'precio' => $d->precio_unitario,
                    'subtotal' => $d->subtotal,
                ];
            }),
            'pagos' => $cuenta->pagos->map(function ($p) {
                return [
                    'monto' => $p->monto,
                    'metodo' => $p->metodo_pago_label,
                    'fecha' => $p->created_at->format('d/m/Y H:i'),
                ];
            }),
        ];
    }

    /**
     * Obtener precio de emergencia con fallback al sistema anterior (tarifas)
     */
    private static function obtenerPrecioEmergencia(): float
    {
        $precioNuevo = \App\Models\IngresoPrecio::getPrecio('emergencia');

        if ($precioNuevo !== null) {
            return (float) $precioNuevo;
        }

        $tarifaEmergencia = Tarifa::where('codigo', 'EMG-BASE')
            ->where('activo', true)
            ->first();

        return $tarifaEmergencia?->precio_particular ?? 200.00;
    }

    /**
     * Obtener precio de internación con fallback al sistema anterior (tarifas)
     */
    private static function obtenerPrecioInternacion(): float
    {
        $precioNuevo = \App\Models\IngresoPrecio::getPrecio('internacion');

        if ($precioNuevo !== null) {
            return (float) $precioNuevo;
        }

        $tarifaInternacion = Tarifa::where('codigo', 'HOSP-ADM')
            ->where('activo', true)
            ->first();

        return $tarifaInternacion?->precio_particular ?? 150.00;
    }
}
