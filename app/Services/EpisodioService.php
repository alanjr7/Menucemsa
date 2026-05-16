<?php

namespace App\Services;

use App\Models\Episodio;

class EpisodioService
{
    public static function abrirEpisodio(int $pacienteId, string $tipoIngreso, int $userId): Episodio
    {
        $abierto = Episodio::where('paciente_id', $pacienteId)
            ->where('estado', 'abierto')
            ->first();

        if ($abierto) {
            return $abierto;
        }

        $ultimo = Episodio::where('paciente_id', $pacienteId)->max('numero') ?? 0;

        return Episodio::create([
            'paciente_id'    => $pacienteId,
            'numero'         => $ultimo + 1,
            'fecha_apertura' => now(),
            'estado'         => 'abierto',
            'tipo_ingreso'   => $tipoIngreso,
            'created_by'     => $userId,
        ]);
    }

    public static function cerrarEpisodioDelPaciente(int $pacienteId, int $userId, string $motivo = 'alta_medica'): void
    {
        Episodio::where('paciente_id', $pacienteId)
            ->where('estado', 'abierto')
            ->update([
                'estado'        => 'cerrado',
                'fecha_cierre'  => now(),
                'motivo_cierre' => $motivo,
                'closed_by'     => $userId,
            ]);
    }

    public static function getEpisodioAbierto(int $pacienteId): ?Episodio
    {
        return Episodio::where('paciente_id', $pacienteId)
            ->where('estado', 'abierto')
            ->first();
    }
}
