<?php

namespace App\Services\Backup;

use App\Models\Backup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;
use ZipArchive;

/**
 * Restaura la base de datos (y archivos subidos) desde un .zip de respaldo.
 *
 * Regla de oro de seguridad: SIEMPRE genera un respaldo `pre_restauracion` del
 * estado actual antes de tocar nada. Si la restauración falla o se subió un
 * backup equivocado, ese snapshot permite volver atrás.
 */
class RestoreService
{
    public function __construct(
        private readonly BackupService $backupService,
        private readonly SqlRunner $runner,
    ) {
    }

    /**
     * Restaura desde el archivo .zip ubicado en $rutaZip.
     *
     * @return array{pre_backup: Backup, sentencias: int, archivos: int}
     */
    public function restaurar(string $rutaZip, ?int $userId = null): array
    {
        $zip = new ZipArchive();
        if ($zip->open($rutaZip) !== true) {
            throw new RuntimeException('El archivo no es un .zip válido.');
        }

        // 1) Validar que sea un respaldo nuestro antes de hacer nada destructivo.
        $this->validarManifest($zip);

        // 2) BLINDAJE: respaldo de seguridad del estado actual.
        $preBackup = $this->backupService->crear('pre_restauracion', $userId, true);

        $tmpDir = storage_path('app/private/backups/restore-'.now()->format('Ymd_His'));
        File::makeDirectory($tmpDir, 0775, true);

        try {
            // 3) Restaurar la base de datos.
            $rutaSql = $tmpDir.DIRECTORY_SEPARATOR.'database.sql';
            $this->extraerEntrada($zip, 'database.sql', $rutaSql);
            $sentencias = $this->runner->ejecutarArchivo($rutaSql, DB::connection()->getPdo());

            // 4) Restaurar archivos subidos (si el respaldo los incluye).
            $archivos = $this->restaurarArchivos($zip);

            $zip->close();

            Log::info('Restauración completada', [
                'pre_backup_id' => $preBackup->id,
                'sentencias' => $sentencias,
                'archivos' => $archivos,
                'user_id' => $userId,
            ]);

            return ['pre_backup' => $preBackup, 'sentencias' => $sentencias, 'archivos' => $archivos];
        } catch (Throwable $e) {
            $zip->close();
            Log::error('Fallo en restauración', [
                'pre_backup_id' => $preBackup->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        } finally {
            File::deleteDirectory($tmpDir);
        }
    }

    /** Verifica el manifiesto y la presencia del volcado SQL. */
    private function validarManifest(ZipArchive $zip): void
    {
        $manifestRaw = $zip->getFromName('manifest.json');
        if ($manifestRaw === false) {
            throw new RuntimeException('El zip no contiene manifest.json: no parece un respaldo del sistema.');
        }

        $manifest = json_decode($manifestRaw, true);
        if (! is_array($manifest) || ($manifest['formato'] ?? null) !== 'cemsa-backup-v1') {
            throw new RuntimeException('Formato de respaldo no reconocido o incompatible.');
        }

        if ($zip->locateName('database.sql') === false) {
            throw new RuntimeException('El respaldo no contiene database.sql.');
        }
    }

    /** Extrae una entrada del zip a una ruta destino, en streaming. */
    private function extraerEntrada(ZipArchive $zip, string $entrada, string $destino): void
    {
        $origen = $zip->getStream($entrada);
        if ($origen === false) {
            throw new RuntimeException("No se pudo leer '{$entrada}' del respaldo.");
        }

        $salida = fopen($destino, 'w');
        if ($salida === false) {
            fclose($origen);
            throw new RuntimeException("No se pudo escribir el archivo temporal: {$destino}");
        }

        stream_copy_to_stream($origen, $salida);
        fclose($origen);
        fclose($salida);
    }

    /**
     * Restaura las entradas files/* del zip a storage/app/public.
     *
     * @return int Cantidad de archivos restaurados.
     */
    private function restaurarArchivos(ZipArchive $zip): int
    {
        $base = storage_path('app/public');
        $restaurados = 0;

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $nombre = $zip->getNameIndex($i);
            if ($nombre === false || ! str_starts_with($nombre, 'files/') || str_ends_with($nombre, '/')) {
                continue;
            }

            // Anti zip-slip: normalizar y descartar rutas que escapen del destino.
            $relativo = ltrim(substr($nombre, strlen('files/')), '/');
            $relativo = str_replace('\\', '/', $relativo);
            if ($relativo === '' || str_contains($relativo, '../')) {
                continue;
            }

            $destino = $base.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relativo);
            File::ensureDirectoryExists(dirname($destino));
            $this->extraerEntrada($zip, $nombre, $destino);
            $restaurados++;
        }

        return $restaurados;
    }
}
