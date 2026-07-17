<?php

namespace App\Console\Commands;

use App\Models\BackupSetting;
use App\Services\Backup\BackupService;
use Illuminate\Console\Command;
use Throwable;

/**
 * Ejecuta el respaldo automático si corresponde según la configuración.
 *
 * Es self-throttling: el scheduler lo invoca seguido (cada hora), pero solo
 * genera un respaldo cuando venció el intervalo configurado. Así desacoplamos
 * la cadencia del cron de la frecuencia de negocio.
 */
class EjecutarBackupAutomatico extends Command
{
    protected $signature = 'backup:auto {--force : Forzar el respaldo ignorando la programación}';
    protected $description = 'Genera un respaldo automático de la base de datos si está programado y vencido';

    public function handle(BackupService $service): int
    {
        $config = BackupSetting::actual();

        if (! $this->option('force') && ! $config->debeEjecutarse()) {
            $this->line('No corresponde ejecutar respaldo automático en este momento.');

            return self::SUCCESS;
        }

        $this->info('Generando respaldo automático...');

        try {
            $backup = $service->crear('automatico', null, $config->incluir_archivos);
            $podados = $service->podar($config->retencion);

            $config->update(['ultima_ejecucion_at' => now()]);

            $this->info("✅ Respaldo creado: {$backup->filename} ({$backup->tamano_legible}).");
            if ($podados > 0) {
                $this->line("Se podaron {$podados} respaldo(s) antiguo(s) por retención.");
            }

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('❌ Falló el respaldo automático: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
