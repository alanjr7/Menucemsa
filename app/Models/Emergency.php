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
        'symptoms',
        'initial_assessment',
        'vital_signs',
        'treatment',
        'observations',
        'destination',
        'cost',
        'paid',
        'admission_date',
        'discharge_date',
    ];

    protected $casts = [
        'admission_date' => 'datetime',
        'discharge_date' => 'datetime',
        'cost' => 'decimal:2',
        'paid' => 'boolean',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
        $last = static::whereDate('created_at', today())->count();
        return 'EMG-' . $date . '-' . str_pad($last + 1, 3, '0', STR_PAD_LEFT);
    }
}
