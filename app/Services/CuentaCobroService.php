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
                'saldo_pendiente' => $precio,
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
     */
    public static function crearCuentaEmergencia(
        int $pacienteCi,
        string $emergencyId,
        array $servicios = [],
        bool $esPostPago = true
    ): CuentaCobro {
        return DB::transaction(function () use ($pacienteCi, $emergencyId, $servicios, $esPostPago) {
            $total = 0;

            // Crear cuenta
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
                // Tarifa base de emergencia
                $tarifaEmergencia = Tarifa::where('codigo', 'EMG-BASE')
                    ->where('activo', true)
                    ->first();

                $precioBase = $tarifaEmergencia?->precio_particular ?? 200.00;
                
                $cuenta->detalles()->create([
                    'tipo_item' => 'servicio',
                    'tarifa_id' => $tarifaEmergencia?->id,
                    'descripcion' => $tarifaEmergencia?->descripcion ?? 'Atención de Emergencia',
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
                $cuenta->detalles()->create([
                    'tipo_item' => 'medicamento',
                    'descripcion' => $med['descripcion'],
                    'cantidad' => $med['cantidad'],
                    'precio_unitario' => $med['precio_unitario'],
                    'subtotal' => $med['cantidad'] * $med['precio_unitario'],
                    'origen_type' => \App\Models\Medicamentos::class,
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
     * Obtener o crear cuenta para un paciente en emergencia
     * Usado durante el flujo de emergencia para ir acumulando cargos
     */
    public static function obtenerOCrearCuentaEmergencia(
        int $pacienteCi,
        string $emergencyId
    ): CuentaCobro {
        // Buscar cuenta existente no pagada
        $cuenta = CuentaCobro::where('paciente_ci', $pacienteCi)
            ->where('referencia_id', $emergencyId)
            ->where('referencia_type', \App\Models\Emergency::class)
            ->whereIn('estado', ['pendiente', 'parcial'])
            ->first();

        if ($cuenta) {
            return $cuenta;
        }

        // Crear nueva cuenta
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
}
