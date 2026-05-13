<?php

namespace App\Services;

use App\Models\Episodio;

class EpisodioService
{
    /**
     * Abre un episodio para el paciente.
     * Si ya tiene uno abierto, lo devuelve sin crear uno nuevo.
     */
    public static function abrirEpisodio(int|string $pacienteCi, string $tipoIngreso, int $userId): Episodio
    {
        $abierto = Episodio::where('paciente_ci', $pacienteCi)
            ->where('estado', 'abierto')
            ->first();

        if ($abierto) {
            return $abierto;
        }

        $ultimo = Episodio::where('paciente_ci', $pacienteCi)->max('numero') ?? 0;

        return Episodio::create([
            'paciente_ci'    => $pacienteCi,
            'numero'         => $ultimo + 1,
            'fecha_apertura' => now(),
            'estado'         => 'abierto',
            'tipo_ingreso'   => $tipoIngreso,
            'created_by'     => $userId,
        ]);
    }

    /**
     * Cierra el episodio abierto del paciente (si existe).
     */
    public static function cerrarEpisodioDelPaciente(int|string $pacienteCi, int $userId, string $motivo = 'alta_medica'): void
    {
        Episodio::where('paciente_ci', $pacienteCi)
            ->where('estado', 'abierto')
            ->update([
                'estado'       => 'cerrado',
                'fecha_cierre' => now(),
                'motivo_cierre' => $motivo,
                'closed_by'    => $userId,
            ]);
    }

    /**
     * Devuelve el episodio abierto del paciente o null.
     */
    public static function getEpisodioAbierto(int|string $pacienteCi): ?Episodio
    {
        return Episodio::where('paciente_ci', $pacienteCi)
            ->where('estado', 'abierto')
            ->first();
    }
}
