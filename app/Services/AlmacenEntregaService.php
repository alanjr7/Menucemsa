<?php

namespace App\Services;

use App\Models\AlmacenEntregaPaciente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AlmacenEntregaService
{
    public static function registrarEntrega(
        int $pacienteId,
        int $catalogoId,
        int $cantidad,
        string $origen,
        ?int $referenciaId = null,
        ?string $observaciones = null
    ): AlmacenEntregaPaciente {
        $entrega = AlmacenEntregaPaciente::create([
            'paciente_id'   => $pacienteId,
            'catalogo_id'   => $catalogoId,
            'cantidad'      => $cantidad,
            'entregado_por' => Auth::id(),
            'origen'        => $origen,
            'referencia_id' => $referenciaId,
            'observaciones' => $observaciones,
            'fecha_entrega' => now(),
        ]);

        Log::info('Medicamento entregado a paciente', [
            'entrega_id'  => $entrega->id,
            'paciente_id' => $pacienteId,
            'catalogo_id' => $catalogoId,
            'cantidad'    => $cantidad,
            'origen'      => $origen,
            'referencia_id' => $referenciaId,
            'entregado_por' => Auth::id(),
        ]);

        return $entrega;
    }

    public static function registrarEntregaMasiva(
        int $pacienteId,
        array $medicamentos,
        string $origen,
        ?int $referenciaId = null,
        ?string $observaciones = null
    ): array {
        $entregas = [];

        DB::transaction(function () use ($pacienteId, $medicamentos, $origen, $referenciaId, $observaciones, &$entregas) {
            foreach ($medicamentos as $med) {
                $entregas[] = self::registrarEntrega(
                    $pacienteId,
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
