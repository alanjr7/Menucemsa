<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Paciente;
use App\Models\Registro;
use App\Models\User;

/**
 * Crea registros para pacientes que no tienen uno (seeder de reparación).
 * También abre episodios cerrados para que aparezcan en /patients.
 */
class FixRegistrosPacientesSeeder extends Seeder
{
    public function run(): void
    {
        $userId = User::first()?->id ?? 1;

        $pacientesSinRegistro = Paciente::whereNull('registro_codigo')->get();

        $this->command->info("Pacientes sin registro: {$pacientesSinRegistro->count()}");

        $arreglados = 0;
        $seq        = DB::table('registros')->count() + 1;

        foreach ($pacientesSinRegistro as $paciente) {
            // Código simple ASCII para evitar problemas de encoding con nombres acentuados
            $codigo = 'REG-SEED-' . str_pad($seq++, 6, '0', STR_PAD_LEFT);

            DB::transaction(function () use ($paciente, $codigo, $userId) {
                Registro::create([
                    'codigo'  => $codigo,
                    'fecha'   => $paciente->created_at?->format('Y-m-d') ?? now()->format('Y-m-d'),
                    'hora'    => $paciente->created_at?->format('H:i:s') ?? now()->format('H:i:s'),
                    'motivo'  => 'Registro inicial',
                    'user_id' => $userId,
                ]);

                $paciente->update(['registro_codigo' => $codigo]);

                // Abrir el episodio si está cerrado (para que aparezca en /patients)
                $paciente->episodios()
                    ->where('estado', 'cerrado')
                    ->latest()
                    ->first()
                    ?->update([
                        'estado'       => 'abierto',
                        'fecha_cierre' => null,
                        'closed_by'    => null,
                    ]);
            });

            $arreglados++;
        }

        $this->command->info("Registros creados: {$arreglados}");
    }
}
