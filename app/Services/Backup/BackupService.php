<?php

namespace App\Services\Backup;

use App\Models\Backup;
use App\Models\BackupSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;
use ZipArchive;

/**
 * Genera respaldos completos (base de datos + archivos subidos) en un único .zip,
 * los registra en la tabla `backups` y aplica la política de retención.
 *
 * Todo en PHP puro: apto para hosting compartido sin mysqldump ni exec().
 */
class BackupService
{
    /** Disco lógico y subcarpeta donde viven los respaldos (privado, no público). */
    public const DISCO = 'local';
    private const SUBCARPETA = 'backups';

    public function __construct(private readonly SqlDumper $dumper)
    {
    }

    /** Ruta absoluta de la carpeta de respaldos (la crea si no existe). */
    public function carpeta(): string
    {
        $ruta = storage_path('app/private/'.self::SUBCARPETA);
        if (! File::isDirectory($ruta)) {
            File::makeDirectory($ruta, 0775, true);
        }

        return $ruta;
    }

    /**
     * Crea un respaldo y devuelve su registro.
     *
     * @param  string  $tipo  manual|automatico|pre_restauracion
     * @param  int|null  $userId  autor (null si lo crea el scheduler)
     * @param  bool|null  $incluirArchivos  si null, toma la configuración guardada
     */
    public function crear(string $tipo = 'manual', ?int $userId = null, ?bool $incluirArchivos = null): Backup
    {
        $incluirArchivos ??= BackupSetting::actual()->incluir_archivos;

        $marca = now()->format('Ymd_His');
        $filename = "backup-cemsa-{$tipo}-{$marca}.zip";
        $rutaZip = $this->carpeta().DIRECTORY_SEPARATOR.$filename;
        $rutaSql = $this->carpeta().DIRECTORY_SEPARATOR."db-{$marca}.sql";

        $backup = Backup::create([
            'filename' => $filename,
            'disk' => self::DISCO,
            'path' => self::SUBCARPETA.'/'.$filename,
            'size' => 0,
            'type' => $tipo,
            'incluye_archivos' => $incluirArchivos,
            'estado' => 'en_proceso',
            'created_by' => $userId,
        ]);

        try {
            // 1) Volcar la base de datos a un .sql temporal.
            $tablas = $this->dumper->dump($rutaSql);

            // 2) Empaquetar en el .zip.
            $zip = new ZipArchive();
            if ($zip->open($rutaZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new RuntimeException("No se pudo crear el archivo zip: {$rutaZip}");
            }

            $zip->addFile($rutaSql, 'database.sql');
            $zip->addFromString('manifest.json', $this->manifest($tablas, $incluirArchivos));

            if ($incluirArchivos) {
                $this->agregarArchivosSubidos($zip);
            }

            $zip->close();

            // 3) Limpiar el .sql temporal (ya está dentro del zip).
            File::delete($rutaSql);

            $backup->update([
                'size' => filesize($rutaZip) ?: 0,
                'estado' => 'completado',
            ]);
        } catch (Throwable $e) {
            File::delete($rutaSql);
            File::delete($rutaZip);
            $backup->update([
                'estado' => 'fallido',
                'error' => mb_substr($e->getMessage(), 0, 1000),
            ]);
            Log::error('Fallo al generar respaldo', ['backup_id' => $backup->id, 'error' => $e->getMessage()]);

            throw $e;
        }

        return $backup->refresh();
    }

    /**
     * Política de retención: conserva los $retener respaldos automáticos más
     * recientes y borra el resto (archivo + registro). No toca manuales ni
     * los previos a restauración.
     */
    public function podar(int $retener): int
    {
        $sobrantes = Backup::where('type', 'automatico')
            ->where('estado', 'completado')
            ->orderByDesc('created_at')
            ->skip(max($retener, 0))
            ->take(PHP_INT_MAX)
            ->get();

        $borrados = 0;
        foreach ($sobrantes as $backup) {
            if ($this->eliminar($backup)) {
                $borrados++;
            }
        }

        return $borrados;
    }

    /** Borra el archivo físico y el registro. */
    public function eliminar(Backup $backup): bool
    {
        $ruta = storage_path('app/private/'.$backup->path);
        File::delete($ruta);

        return (bool) $backup->delete();
    }

    private function manifest(int $tablas, bool $incluyeArchivos): string
    {
        return json_encode([
            'app' => config('app.name'),
            'formato' => 'cemsa-backup-v1',
            'base_datos' => DB::connection()->getDatabaseName(),
            'tablas' => $tablas,
            'incluye_archivos' => $incluyeArchivos,
            'php' => PHP_VERSION,
            'generado_en' => now()->toIso8601String(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /** Agrega recursivamente storage/app/public bajo el prefijo files/ en el zip. */
    private function agregarArchivosSubidos(ZipArchive $zip): void
    {
        $base = storage_path('app/public');
        if (! File::isDirectory($base)) {
            return;
        }

        foreach (File::allFiles($base) as $archivo) {
            $relativo = 'files/'.str_replace('\\', '/', $archivo->getRelativePathname());
            $zip->addFile($archivo->getPathname(), $relativo);
        }
    }
}
