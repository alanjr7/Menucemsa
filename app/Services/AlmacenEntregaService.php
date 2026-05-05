<?php

namespace App\Services;

use App\Models\AlmacenEntregaPaciente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AlmacenEntregaService
{
    public static function registrarEntrega(
        int $pacienteCi,
        int $catalogoId,
        int $cantidad,
        string $origen,
        ?int $referenciaId = null,
        ?string $observaciones = null
    ): AlmacenEntregaPaciente {
        $entrega = AlmacenEntregaPaciente::create([
            'paciente_ci' => $pacienteCi,
            'catalogo_id' => $catalogoId,
            'cantidad' => $cantidad,
            'entregado_por' => Auth::id(),
            'origen' => $origen,
            'referencia_id' => $referenciaId,
            'observaciones' => $observaciones,
            'fecha_entrega' => now(),
        ]);

        Log::info('Medicamento entregado a paciente', [
            'entrega_id' => $entrega->id,
            'paciente_ci' => $pacienteCi,
            'catalogo_id' => $catalogoId,
            'cantidad' => $cantidad,
            'origen' => $origen,
            'referencia_id' => $referenciaId,
            'entregado_por' => Auth::id(),
        ]);

        return $entrega;
    }

    public static function registrarEntregaMasiva(
        int $pacienteCi,
        array $medicamentos,
        string $origen,
        ?int $referenciaId = null,
        ?string $observaciones = null
    ): array {
        $entregas = [];

        DB::transaction(function () use ($pacienteCi, $medicamentos, $origen, $referenciaId, $observaciones, &$entregas) {
            foreach ($medicamentos as $med) {
                $entregas[] = self::registrarEntrega(
                    $pacienteCi,
                    $med['id'],
                    $med['cantidad'],
                    $origen,
                    $referenciaId,
                    $observaciones
                );
            }
        });

        return $entregas;
    }
}
