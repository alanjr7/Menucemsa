<?php

namespace App\Services;

use App\Models\CuentaCobro;
use App\Models\Seguro;

class AplicarSeguroService
{
    /**
     * Aplica el seguro del paciente sobre el saldo pendiente de una cuenta.
     * Solo actua si la cuenta aun no tiene seguro aplicado y el paciente
     * posee un seguro con cobertura por porcentaje o tope de monto.
     *
     * @return array{cubierto: float, paciente: float, aplicado: bool}
     */
    public static function aplicarSiCorresponde(CuentaCobro $cuenta): array
    {
        if ($cuenta->seguro_estado !== null) {
            return ['cubierto' => 0, 'paciente' => $cuenta->saldo_pendiente, 'aplicado' => false];
        }

        $seguro = $cuenta->paciente?->seguro;

        if (!$seguro || $seguro->estado !== 'activo') {
            return ['cubierto' => 0, 'paciente' => $cuenta->saldo_pendiente, 'aplicado' => false];
        }

        $tiposAplicables = ['porcentaje', 'tope_monto'];
        if (!in_array($seguro->tipo_cobertura, $tiposAplicables, true)) {
            return ['cubierto' => 0, 'paciente' => $cuenta->saldo_pendiente, 'aplicado' => false];
        }

        $montoBase = max(0, (float) $cuenta->total_calculado - (float) $cuenta->total_pagado);
        $calculo = $seguro->calcularCobertura($montoBase);

        $montoCubierto = (float) $calculo['monto_cubierto'];
        $montoPaciente = (float) $calculo['monto_paciente'];

        $cuenta->update([
            'seguro_id' => $seguro->id,
            'seguro_estado' => 'autorizado',
            'seguro_fecha_autorizacion' => now(),
            'seguro_autorizado_por' => auth()->id(),
            'seguro_monto_cobertura' => $montoCubierto,
            'seguro_monto_paciente' => $montoPaciente,
        ]);
        $cuenta->recalcularTotales();

        return [
            'cubierto' => $montoCubierto,
            'paciente' => $montoPaciente,
            'aplicado' => true,
        ];
    }

    /**
     * Devuelve la proyeccion de cobertura sin persistir cambios.
     */
    public static function calcularProyeccion(CuentaCobro $cuenta): ?array
    {
        if ($cuenta->seguro_estado !== null) {
            return [
                'nombre' => $cuenta->seguro?->nombre_empresa,
                'monto_cubierto' => (float) $cuenta->seguro_monto_cobertura,
                'monto_paciente' => (float) $cuenta->seguro_monto_paciente,
                'ya_aplicado' => true,
            ];
        }

        $seguro = $cuenta->paciente?->seguro;

        if (!$seguro || $seguro->estado !== 'activo') {
            return null;
        }

        $tiposAplicables = ['porcentaje', 'tope_monto'];
        if (!in_array($seguro->tipo_cobertura, $tiposAplicables, true)) {
            return null;
        }

        $montoBase = max(0, (float) $cuenta->total_calculado - (float) $cuenta->total_pagado);
        $calculo = $seguro->calcularCobertura($montoBase);

        return [
            'nombre' => $seguro->nombre_empresa,
            'monto_cubierto' => (float) $calculo['monto_cubierto'],
            'monto_paciente' => (float) $calculo['monto_paciente'],
            'ya_aplicado' => false,
        ];
    }
}
