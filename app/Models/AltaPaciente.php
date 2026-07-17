<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AltaPaciente extends Model
{
    use HasFactory;

    protected $table = 'alta_pacientes';

    protected $fillable = [
        'paciente_id',
        'dado_de_alta_por',
        'motivo_alta',
        'observaciones',
        'fecha_alta',
    ];

    protected $casts = [
        'fecha_alta' => 'datetime',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'dado_de_alta_por');
    }

    public function getMotivoLabelAttribute(): string
    {
        return match($this->motivo_alta) {
            'alta_medica'   => 'Alta Médica',
            'voluntaria'    => 'Alta Voluntaria',
            'fallecimiento' => 'Fallecimiento',
            'traslado'      => 'Traslado',
            default         => ucfirst($this->motivo_alta),
        };
    }
}
