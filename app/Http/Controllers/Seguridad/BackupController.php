<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use App\Models\Backup;
use App\Models\BackupSetting;
use App\Services\Backup\BackupService;
use App\Services\Backup\RestoreService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class BackupController extends Controller
{
    public function index()
    {
        $config = BackupSetting::actual();
        $backups = Backup::with('autor')->orderByDesc('created_at')->limit(50)->get();

        // Comando exacto de cron a pegar en cPanel para los respaldos automáticos.
        $cron = '* * * * * php '.base_path('artisan').' schedule:run >> /dev/null 2>&1';

        return view('seguridad.backup.index', compact('config', 'backups', 'cron'));
    }

    /** Genera un respaldo manual en el momento. */
    public function crear(Request $request, BackupService $service)
    {
        $request->validate([
            'incluir_archivos' => 'nullable|boolean',
        ]);

        $this->sinLimites();

        try {
            $backup = $service->crear('manual', Auth::id(), $request->boolean('incluir_archivos', true));

            return redirect()->route('seguridad.backup.index')
                ->with('success', "Respaldo generado correctamente ({$backup->tamano_legible}). Ya podés descargarlo.");
        } catch (Throwable $e) {
            Log::error('Error al crear respaldo manual', ['error' => $e->getMessage()]);

            return redirect()->route('seguridad.backup.index')
                ->with('error', 'No se pudo generar el respaldo: '.$e->getMessage());
        }
    }

    /** Descarga el archivo .zip de un respaldo. */
    public function descargar(Backup $backup)
    {
        if (! $backup->existeArchivo()) {
            return redirect()->route('seguridad.backup.index')
                ->with('error', 'El archivo de este respaldo ya no existe en el servidor.');
        }

        return Storage::disk($backup->disk)->download($backup->path, $backup->filename);
    }

    /** Elimina un respaldo (archivo + registro). */
    public function eliminar(Backup $backup, BackupService $service)
    {
        $service->eliminar($backup);

        return redirect()->route('seguridad.backup.index')
            ->with('success', 'Respaldo eliminado correctamente.');
    }

    /** Guarda la configuración de respaldos automáticos. */
    public function guardarConfiguracion(Request $request)
    {
        $validated = $request->validate([
            'automatico_activo' => 'nullable|boolean',
            'frecuencia' => 'required|in:diario,cada_2_dias,semanal,quincenal,mensual',
            'hora' => 'required|date_format:H:i',
            'retencion' => 'required|integer|min:1|max:365',
            'incluir_archivos' => 'nullable|boolean',
        ]);

        BackupSetting::actual()->update([
            'automatico_activo' => $request->boolean('automatico_activo'),
            'frecuencia' => $validated['frecuencia'],
            'hora' => $validated['hora'],
            'retencion' => $validated['retencion'],
            'incluir_archivos' => $request->boolean('incluir_archivos'),
        ]);

        return redirect()->route('seguridad.backup.index')
            ->with('success', 'Configuración de respaldos automáticos guardada.');
    }

    /**
     * Restaura la base de datos desde un respaldo subido.
     *
     * Exige confirmación explícita (segunda barrera, además del doble alert del
     * front). El RestoreService genera un respaldo de seguridad antes de tocar
     * la base, por si se sube un archivo equivocado.
     */
    public function restaurar(Request $request, RestoreService $service)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:zip|max:2097152', // hasta ~2 GB
            'confirmacion' => 'required|in:RESTAURAR',
        ], [
            'confirmacion.in' => 'Debe escribir RESTAURAR para confirmar la operación.',
            'archivo.mimes' => 'El archivo debe ser un .zip de respaldo válido.',
        ]);

        $this->sinLimites();

        // Guardamos el subido en una ruta temporal controlada.
        $rutaTemp = $request->file('archivo')->storeAs(
            'backups/restauraciones',
            'subido-'.now()->format('Ymd_His').'.zip',
            'local'
        );
        $rutaAbs = Storage::disk('local')->path($rutaTemp);

        try {
            $resultado = $service->restaurar($rutaAbs, Auth::id());

            return redirect()->route('seguridad.backup.index')->with('success', sprintf(
                'Restauración completada: %d sentencias y %d archivo(s). Se guardó un respaldo de seguridad previo (%s) por si necesitás revertir.',
                $resultado['sentencias'],
                $resultado['archivos'],
                $resultado['pre_backup']->filename,
            ));
        } catch (Throwable $e) {
            Log::error('Error en restauración', ['error' => $e->getMessage()]);

            return redirect()->route('seguridad.backup.index')->with('error',
                'La restauración falló: '.$e->getMessage().
                ' Se generó un respaldo de seguridad previo; el sistema no quedó a medias salvo indicación contraria.'
            );
        } finally {
            Storage::disk('local')->delete($rutaTemp);
        }
    }

    /** Levanta límites de tiempo/memoria para operaciones pesadas (best-effort). */
    private function sinLimites(): void
    {
        @set_time_limit(0);
        @ini_set('memory_limit', '512M');
    }
}
