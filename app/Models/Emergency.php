<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Emergency extends Model
{
    protected $fillable = [
        'patient_id',
        'user_id',
        'code',
        'status',
        'tipo_ingreso',
        'destino_inicial',
        'is_temp_id',
        'temp_id',
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
    ];

    protected $casts = [
        'admission_date' => 'datetime',
        'discharge_date' => 'datetime',
        'cost' => 'decimal:2',
        'paid' => 'boolean',
        'is_temp_id' => 'boolean',
        'es_parto' => 'boolean',
        'deuda' => 'decimal:2',
        'total_pagado' => 'decimal:2',
        'flujo_historial' => 'array',
        'detalle_costos' => 'array',
        'equipos_medicos' => 'array',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'patient_id', 'ci');
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

    public static function generateCode(): string
    {
        $date = now()->format('Ymd');
        $last = static::whereDate('created_at', today())
            ->max(\DB::raw("CAST(SUBSTRING_INDEX(code, '-', -1) AS UNSIGNED)")) ?? 0;
        return 'EMG-' . $date . '-' . str_pad($last + 1, 3, '0', STR_PAD_LEFT);
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
