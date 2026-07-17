<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Emergency extends Model
{
    protected $fillable = [
        'paciente_id',
        'user_id',
        'code',
        'status',
        'tipo_ingreso',
        'destino_inicial',
        'symptoms',
        'initial_assessment',
        'vital_signs',
        'treatment',
        'observations',
        'destination',
        'flujo_historial',
        'ubicacion_actual',
        'nro_cirugia',
        'nro_hospitalizacion',
        'nro_uti',
        'cost',
        'paid',
        'deuda',
        'total_pagado',
        'detalle_costos',
        'es_parto',
        'estado_parto',
        'admission_date',
        'discharge_date',
        'equipos_medicos',
        'episodio_id',
    ];

    protected $casts = [
        'admission_date' => 'datetime',
        'discharge_date' => 'datetime',
        'cost' => 'decimal:2',
        'paid' => 'boolean',
        'es_parto' => 'boolean',
        'deuda' => 'decimal:2',
        'total_pagado' => 'decimal:2',
        'flujo_historial' => 'array',
        'detalle_costos' => 'array',
        'equipos_medicos' => 'array',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function episodio(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Episodio::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cuentaCobro()
    {
        return $this->hasOne(\App\Models\CuentaCobro::class, 'referencia_id')
            ->where('referencia_type', self::class);
    }

    public function getSaldoPendienteRealAttribute(): ?float
    {
        return $this->cuentaCobro?->saldo_pendiente;
    }

    public function getEstadoPagoRealAttribute(): ?string
    {
        return $this->cuentaCobro?->estado;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'recibido' => 'yellow',
            'en_evaluacion' => 'blue',
            'estabilizado' => 'green',
            'uti' => 'red',
            'cirugia' => 'purple',
            'alta' => 'gray',
            'fallecido' => 'black',
            default => 'gray',
        };
    }

    /**
     * Calcula el siguiente código de emergencia: EMG-Ymd-NNNN (p. ej. EMG-20260610-5001).
     * Lleva la fecha, pero el correlativo final es global e incremental: arranca en
     * 5001 y NO se reinicia al cambiar de día. Solo considera los códigos con este
     * formato (EMG-fecha-numero), de modo que registros de otro esquema no inflen el
     * contador. Solo cálculo; para persistir usar {@see crearConCodigo()}, que además
     * resuelve colisiones concurrentes.
     */
    public static function generateCode(): string
    {
        $last = static::where('code', 'REGEXP', '^EMG-[0-9]{8}-[0-9]+$')
            ->max(\DB::raw("CAST(SUBSTRING_INDEX(code, '-', -1) AS UNSIGNED)")) ?? 0;
        $siguiente = max((int) $last, 5000) + 1;

        return 'EMG-' . now()->format('Ymd') . '-' . str_pad($siguiente, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Crea una emergencia asignando un `code` único. Si dos procesos concurrentes
     * calculan el mismo correlativo, el unique index hace fallar al segundo y aquí
     * se reintenta con el siguiente número, en vez de un 500. Es la única vía que
     * se debe usar para crear emergencias.
     *
     * @param array $attributes Datos de la emergencia. No incluir `code`.
     */
    public static function crearConCodigo(array $attributes): self
    {
        for ($intento = 0; $intento < 5; $intento++) {
            $attributes['code'] = static::generateCode();

            try {
                return static::create($attributes);
            } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                // Colisión por concurrencia: reintentar con el siguiente correlativo.
            }
        }

        throw new \RuntimeException('No se pudo generar un código de emergencia único tras varios intentos.');
    }

    public function getTipoIngresoLabelAttribute(): string
    {
        return match($this->tipo_ingreso) {
            'soat' => 'SOAT (Accidente)',
            'parto' => 'Parto',
            'general' => 'Emergencia General',
            default => 'No especificado',
        };
    }

    public function getUbicacionColorAttribute(): string
    {
        return match($this->ubicacion_actual) {
            'emergencia' => 'red',
            'cirugia' => 'purple',
            'uti' => 'orange',
            'hospitalizacion' => 'blue',
            'observacion' => 'yellow',
            'alta' => 'green',
            default => 'gray',
        };
    }

    public function getUbicacionLabelAttribute(): string
    {
        return match($this->ubicacion_actual) {
            'emergencia' => 'Emergencia',
            'cirugia' => 'Quirófano',
            'uti' => 'UTI',
            'hospitalizacion' => 'Hospitalización',
            'observacion' => 'Observación',
            'alta' => 'Dado de Alta',
            default => 'Desconocido',
        };
    }

    public function registrarMovimiento(string $desde, string $hasta, ?string $notas = null): void
    {
        $historial = $this->flujo_historial ?? [];
        $historial[] = [
            'fecha' => now()->toDateTimeString(),
            'desde' => $desde,
            'hasta' => $hasta,
            'usuario_id' => auth()->id(),
            'notas' => $notas,
        ];

        $this->update([
            'flujo_historial' => $historial,
            'ubicacion_actual' => $hasta,
        ]);
    }
}
