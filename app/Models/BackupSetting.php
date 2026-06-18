<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Configuración singleton del módulo de backups automáticos.
 */
class BackupSetting extends Model
{
    protected $guarded = [];

    protected $casts = [
        'automatico_activo' => 'boolean',
        'incluir_archivos' => 'boolean',
        'retencion' => 'integer',
        'ultima_ejecucion_at' => 'datetime',
        'hora' => 'datetime:H:i',
    ];

    /** Frecuencia (clave) => cantidad de días entre respaldos. */
    public const INTERVALOS_DIAS = [
        'diario' => 1,
        'cada_2_dias' => 2,
        'semanal' => 7,
        'quincenal' => 15,
        'mensual' => 30,
    ];

    /** Devuelve (o crea) la única fila de configuración. */
    public static function actual(): self
    {
        return static::query()->firstOrCreate([], [
            'automatico_activo' => false,
            'frecuencia' => 'diario',
            'hora' => '02:00:00',
            'retencion' => 7,
            'incluir_archivos' => true,
        ]);
    }

    /** Días que deben transcurrir entre respaldos automáticos. */
    public function intervaloDias(): int
    {
        return self::INTERVALOS_DIAS[$this->frecuencia] ?? 1;
    }

    /**
     * ¿Corresponde ejecutar un respaldo automático ahora?
     * Vencido si está activo y pasó el intervalo desde la última ejecución,
     * respetando la hora preferida de corrida.
     */
    public function debeEjecutarse(?Carbon $ahora = null): bool
    {
        $ahora = $ahora ?? now();

        if (! $this->automatico_activo) {
            return false;
        }

        // Aún no llegó la hora preferida de hoy.
        $horaProgramada = Carbon::parse($this->hora->format('H:i'), $ahora->getTimezone())
            ->setDate($ahora->year, $ahora->month, $ahora->day);
        if ($ahora->lt($horaProgramada)) {
            return false;
        }

        if (! $this->ultima_ejecucion_at) {
            return true;
        }

        return $this->ultima_ejecucion_at->copy()
            ->addDays($this->intervaloDias())
            ->lte($ahora);
    }

    /** Etiqueta legible de la frecuencia. */
    public function getFrecuenciaLegibleAttribute(): string
    {
        return match ($this->frecuencia) {
            'cada_2_dias' => 'Cada 2 días',
            'semanal' => 'Semanal',
            'quincenal' => 'Quincenal (cada 15 días)',
            'mensual' => 'Mensual',
            default => 'Diario',
        };
    }
}
