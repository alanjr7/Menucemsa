<?php

namespace App\Services\Habitacion;

use App\Models\Cama;
use App\Models\Habitacion;
use App\Models\Hospitalizacion;
use App\Models\CuentaCobro;
use App\Models\CuentaCobroDetalle;

class AsignacionCamaService
{
    public function asignar(
        Habitacion $habitacion,
        int $camaId,
        string $hospitalizacionId
    ): array {
        $cama = Cama::findOrFail($camaId);

        if (!$cama->estaDisponible()) {
            return ['success' => false, 'error' => 'La cama no está disponible.'];
        }

        $hospitalizacion = Hospitalizacion::with('paciente')->findOrFail($hospitalizacionId);
        $cuentaCobro = $this->obtenerOCrearCuenta($hospitalizacion);
        $detalle = $this->crearDetalleEstadia($cuentaCobro, $hospitalizacion, $cama, $habitacion);

        $this->actualizarHospitalizacion($hospitalizacion, $habitacion, $cama, $detalle->id);
        $this->marcarCamaOcupada($cama);
        $this->actualizarEstadoHabitacion($habitacion);

        return [
            'success' => true,
            'message' => 'Paciente asignado a la cama exitosamente.',
        ];
    }

    private function obtenerOCrearCuenta(Hospitalizacion $hospitalizacion): CuentaCobro
    {
        $criteria = $hospitalizacion->ci_paciente
            ? ['paciente_ci' => $hospitalizacion->ci_paciente, 'estado' => 'pendiente']
            : ['referencia_id' => $hospitalizacion->id, 'referencia_type' => Hospitalizacion::class, 'estado' => 'pendiente'];

        $paciente = $hospitalizacion->paciente;
        $tieneSeguroAplicable = $paciente && $paciente->seguro && $paciente->seguro->estado === 'activo'
            && in_array($paciente->seguro->tipo_cobertura, ['porcentaje', 'tope_monto'], true);

        return CuentaCobro::firstOrCreate(
            $criteria,
            [
                'paciente_ci' => $hospitalizacion->ci_paciente,
                'tipo_atencion' => 'internacion',
                'referencia_id' => $hospitalizacion->id,
                'referencia_type' => Hospitalizacion::class,
                'total_calculado' => 0,
                'total_pagado' => 0,
                'seguro_id' => $tieneSeguroAplicable ? $paciente->seguro->id : null,
                'seguro_estado' => $tieneSeguroAplicable ? 'pendiente_autorizacion' : null,
            ]
        );
    }

    private function crearDetalleEstadia(
        CuentaCobro $cuenta,
        Hospitalizacion $hospitalizacion,
        Cama $cama,
        Habitacion $habitacion
    ): CuentaCobroDetalle {
        return CuentaCobroDetalle::create([
            'cuenta_cobro_id' => $cuenta->id,
            'tipo_item' => 'estadia',
            'descripcion' => "Estancia Internación - Habitación {$habitacion->id}, Cama {$cama->nro} (En progreso)",
            'cantidad' => 1,
            'precio_unitario' => $cama->precio_por_dia,
            'subtotal' => $cama->precio_por_dia,
            'origen_type' => Hospitalizacion::class,
            'origen_id' => $hospitalizacion->id,
        ]);
    }

    private function actualizarHospitalizacion(
        Hospitalizacion $hospitalizacion,
        Habitacion $habitacion,
        Cama $cama,
        int $detalleId
    ): void {
        $hospitalizacion->update([
            'habitacion_id' => $habitacion->id,
            'cama_id' => $cama->id,
            'precio_cama_dia' => $cama->precio_por_dia,
            'cuenta_cobro_detalle_id' => $detalleId,
        ]);
    }

    private function marcarCamaOcupada(Cama $cama): void
    {
        $cama->update(['disponibilidad' => 'ocupada']);
    }

    private function actualizarEstadoHabitacion(Habitacion $habitacion): void
    {
        if ($habitacion->estado === 'disponible') {
            $habitacion->update(['estado' => 'ocupada']);
        }
    }
}
