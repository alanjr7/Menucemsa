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

        // No se aplica una póliza vencida (o aún no vigente).
        if (!$cuenta->paciente->seguroVigente()) {
            return ['cubierto' => 0, 'paciente' => $cuenta->saldo_pendiente, 'aplicado' => false];
        }

        $tiposAplicables = ['porcentaje', 'tope_monto'];
        if (!in_array($seguro->tipo_cobertura, $tiposAplicables, true)) {
            return ['cubierto' => 0, 'paciente' => $cuenta->saldo_pendiente, 'aplicado' => false];
        }

        // Modelo abierto: la cobertura es sobre el TOTAL del episodio (no el saldo).
        $calculo = $seguro->calcularCobertura((float) $cuenta->total_calculado);

        if ((float) $calculo['monto_cubierto'] <= 0) {
            return ['cubierto' => 0, 'paciente' => $cuenta->saldo_pendiente, 'aplicado' => false];
        }

        // Autoriza (abierta por episodio) + crea la venta devengada a la aseguradora.
        $resultado = $cuenta->autorizarSeguro($seguro);

        return [
            'cubierto' => $resultado['cubierto'],
            'paciente' => $resultado['paciente'],
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

        if (!$cuenta->paciente->seguroVigente()) {
            return null;
        }

        $tiposAplicables = ['porcentaje', 'tope_monto'];
        if (!in_array($seguro->tipo_cobertura, $tiposAplicables, true)) {
            return null;
        }

        $calculo = $seguro->calcularCobertura((float) $cuenta->total_calculado);

        if ((float) $calculo['monto_cubierto'] <= 0) {
            return null;
        }

        return [
            'nombre' => $seguro->nombre_empresa,
            'monto_cubierto' => (float) $calculo['monto_cubierto'],
            'monto_paciente' => (float) $calculo['monto_paciente'],
            'ya_aplicado' => false,
        ];
    }
}
