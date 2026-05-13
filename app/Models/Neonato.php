<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Neonato extends Model
{
    protected $fillable = [
        'nombre', 'sexo',
        'paciente_ci', 'temp_id', 'is_temp_id', 'code',
        'madre_ci', 'madre_nombre',
        'peso', 'talla', 'perimetro_cefalico',
        'apgar1', 'apgar5', 'tipo_parto', 'fecha_hora_nacimiento',
        'status', 'status_logs', 'observaciones',
        'admission_date', 'discharge_date',
        'user_id',
    ];

    protected $casts = [
        'fecha_hora_nacimiento' => 'datetime',
        'admission_date'        => 'datetime',
        'discharge_date'        => 'datetime',
        'is_temp_id'            => 'boolean',
        'status_logs'           => 'array',
    ];

    // -------------------------------------------------------------------------
    // Relaciones
    // -------------------------------------------------------------------------

    public function madre(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'madre_ci', 'ci');
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_ci', 'ci');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function evaluaciones(): HasMany
    {
        return $this->hasMany(Evaluacion::class, 'temp_id', 'temp_id');
    }

    public function camillaUsos(): HasMany
    {
        return $this->hasMany(CamillaUso::class, 'paciente_ci', 'temp_id');
    }

    // -------------------------------------------------------------------------
    // Generadores de códigos
    // -------------------------------------------------------------------------

    public static function generateCode(): string
    {
        $prefix = 'NEO-' . now()->format('Ymd');
        $last = static::where('code', 'like', $prefix . '-%')
            ->orderBy('code', 'desc')
            ->value('code');
        $seq = $last ? ((int) substr($last, -3)) + 1 : 1;

        return $prefix . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    public static function generateTempId(): string
    {
        $prefix = 'RN-' . now()->format('Ymd');
        $last = static::where('temp_id', 'like', $prefix . '-%')
            ->orderBy('temp_id', 'desc')
            ->value('temp_id');
        $seq = $last ? ((int) substr($last, -3)) + 1 : 1;

        return $prefix . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    public function getIdentificadorAttribute(): string
    {
        return $this->is_temp_id ? ($this->temp_id ?? '') : ($this->paciente_ci ?? '');
    }

    /** CI de facturación: usa madre_ci si existe, sino el temp_id propio */
    public function getBillingCiAttribute(): string
    {
        return $this->madre_ci ?? $this->temp_id ?? (string) $this->id;
    }

    public function getNombreDisplayAttribute(): string
    {
        return $this->nombre ?: 'Recién Nacido';
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'recibido'       => 'blue',
            'en_observacion' => 'yellow',
            'estable'        => 'green',
            'uti_neonatal'   => 'orange',
            'alta'           => 'gray',
            'fallecido'      => 'red',
            default          => 'gray',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'recibido'       => 'Recibido',
            'en_observacion' => 'En Observación',
            'estable'        => 'Estable',
            'uti_neonatal'   => 'UTI Neonatal',
            'alta'           => 'Alta',
            'fallecido'      => 'Fallecido',
            default          => 'Desconocido',
        };
    }

    public static function statuses(): array
    {
        return [
            'recibido'       => 'Recibido',
            'en_observacion' => 'En Observación',
            'estable'        => 'Estable',
            'uti_neonatal'   => 'UTI Neonatal',
            'alta'           => 'Alta',
            'fallecido'      => 'Fallecido',
        ];
    }
}
