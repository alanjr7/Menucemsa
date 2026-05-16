<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Neonato extends Model
{
    protected $fillable = [
        'nombre', 'sexo',
        'paciente_id', 'code',
        'madre_id', 'madre_nombre',
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
        'status_logs'           => 'array',
    ];

    // -------------------------------------------------------------------------
    // Relaciones
    // -------------------------------------------------------------------------

    public function madre(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'madre_id');
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function evaluaciones(): HasMany
    {
        return $this->hasMany(Evaluacion::class, 'paciente_id', 'paciente_id');
    }

    public function camillaUsos(): HasMany
    {
        return $this->hasMany(CamillaUso::class, 'paciente_id', 'paciente_id');
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

    // Genera el temp_code que va en pacientes.temp_code al crear un neonato sin CI
    public static function generateTempCode(): string
    {
        $prefix = 'RN-' . now()->format('Ymd');
        $last = Paciente::where('temp_code', 'like', $prefix . '-%')
            ->orderBy('temp_code', 'desc')
            ->value('temp_code');
        $seq = $last ? ((int) substr($last, -3)) + 1 : 1;

        return $prefix . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    public function getIdentificadorAttribute(): string
    {
        $paciente = $this->paciente;
        if (!$paciente) {
            return (string) $this->id;
        }
        return $paciente->is_temp
            ? ($paciente->temp_code ?? '')
            : ((string) $paciente->ci ?? '');
    }

    public function getBillingCiAttribute(): string
    {
        $madre = $this->madre;
        if ($madre) {
            return (string) ($madre->ci ?? $madre->temp_code ?? $madre->id);
        }
        $paciente = $this->paciente;
        if ($paciente) {
            return (string) ($paciente->temp_code ?? $paciente->ci ?? $paciente->id);
        }
        return (string) $this->id;
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
