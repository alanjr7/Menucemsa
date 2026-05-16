<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Hospitalizacion;
use Illuminate\Support\Facades\DB;

class CerrarHospitalizacionesDuplicadas extends Command
{
    protected $signature   = 'hospitalizaciones:cerrar-duplicadas {--dry-run : Solo mostrar lo que haría sin modificar}';
    protected $description = 'Cierra registros de Hospitalizacion duplicados activos (sin fecha_alta) para el mismo paciente';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $this->info($dryRun ? '[DRY-RUN] Modo simulación' : 'Cerrando hospitalizaciones duplicadas...');

        // Pacientes con más de 1 hospitalización sin fecha_alta
        $duplicados = DB::select("
            SELECT paciente_id, COUNT(*) as cnt
            FROM hospitalizaciones
            WHERE fecha_alta IS NULL
              AND paciente_id IS NOT NULL
            GROUP BY paciente_id
            HAVING cnt > 1
        ");

        if (empty($duplicados)) {
            $this->info('✅ No se encontraron hospitalizaciones duplicadas activas.');
            return 0;
        }

        $this->warn(count($duplicados) . ' paciente(s) con hospitalizaciones duplicadas.');
        $cerradas = 0;

        foreach ($duplicados as $dup) {
            $hospitalizaciones = Hospitalizacion::where('paciente_id', $dup->paciente_id)
                ->whereNull('fecha_alta')
                ->orderBy('created_at', 'desc') // La más reciente es la activa
                ->get();

            $activa    = $hospitalizaciones->first();
            $anteriores = $hospitalizaciones->slice(1);

            $this->line(sprintf(
                '  Paciente CI %s → Activa: %s | A cerrar: %d',
                $dup->paciente_id,
                $activa->id,
                $anteriores->count()
            ));

            if ($dryRun) {
                foreach ($anteriores as $h) {
                    $this->line('    [DRY-RUN] Cerraría: ' . $h->id . ' (ingreso: ' . $h->fecha_ingreso . ')');
                }
                continue;
            }

            foreach ($anteriores as $hosp) {
                $hosp->update([
                    'fecha_alta'  => now(),
                    'diagnostico' => ($hosp->diagnostico ?? '') . ' | CERRADA AUTOMÁTICAMENTE (duplicado): activa es ' . $activa->id,
                ]);
                $cerradas++;
            }
        }

        $this->info("✅ Proceso completo: {$cerradas} hospitalización(es) cerrada(s).");
        return 0;
    }
}
