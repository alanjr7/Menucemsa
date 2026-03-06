<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Consulta;
use App\Models\Cirugia;
use App\Models\Cita;
use Illuminate\Support\Facades\DB;

class UnificarEspecialidadesCommand extends Command
{
    protected $signature = 'especialidades:unificar
                            {--dry-run : Mostrar cambios sin aplicarlos}';

    protected $description = 'Unifica especialidades duplicadas (mismo nombre): deja un código por nombre y actualiza referencias en médicos, consultas, cirugías y citas.';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('Modo dry-run: no se aplicarán cambios.');
        }

        $especialidades = Especialidad::orderBy('nombre')->orderBy('codigo')->get();

        // Agrupar por nombre normalizado (trim + minúsculas) para unificar "Medicina General" y "Medicina general"
        $grupos = $especialidades->groupBy(function ($esp) {
            return strtolower(trim($esp->nombre));
        });

        $unificar = [];
        foreach ($grupos as $nombre => $filas) {
            if ($filas->count() <= 1) {
                continue;
            }
            $codigos = $filas->pluck('codigo')->values()->all();
            $mantener = $filas->first()->codigo;
            $eliminar = array_values(array_diff($codigos, [$mantener]));
            $unificar[] = [
                'nombre' => $filas->first()->nombre, // nombre real para mostrar
                'mantener' => $mantener,
                'eliminar' => $eliminar,
            ];
        }

        if (empty($unificar)) {
            $this->info('No hay especialidades duplicadas por nombre.');
            return self::SUCCESS;
        }

        $this->table(
            ['Nombre', 'Código a mantener', 'Códigos a unificar (y eliminar)'],
            array_map(fn ($u) => [
                $u['nombre'],
                $u['mantener'],
                implode(', ', $u['eliminar']),
            ], $unificar)
        );

        if (!$dryRun && !$this->confirm('¿Aplicar estos cambios en la base de datos?', true)) {
            return self::SUCCESS;
        }

        if ($dryRun) {
            $this->info('Dry-run: se actualizarían las tablas y se eliminarían los códigos listados.');
            return self::SUCCESS;
        }

        try {
            DB::beginTransaction();

            foreach ($unificar as $u) {
                $mantener = $u['mantener'];
                $eliminar = $u['eliminar'];

                foreach ($eliminar as $codigo) {
                    Medico::where('codigo_especialidad', $codigo)->update(['codigo_especialidad' => $mantener]);
                    Consulta::where('codigo_especialidad', $codigo)->update(['codigo_especialidad' => $mantener]);
                    Cirugia::where('codigo_especialidad', $codigo)->update(['codigo_especialidad' => $mantener]);
                    Cita::where('codigo_especialidad', $codigo)->update(['codigo_especialidad' => $mantener]);
                }

                Especialidad::whereIn('codigo', $eliminar)->delete();
            }

            DB::commit();
            $this->info('Especialidades unificadas correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Error: ' . $e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
