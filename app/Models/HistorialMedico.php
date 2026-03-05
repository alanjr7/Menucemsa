<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialMedico extends Model
{
    use HasFactory;

    protected $table = 'historial_medico';
    protected $primaryKey = 'ci_paciente';
    public $incrementing = false;
    protected $keyType = 'integer';

    protected $fillable = [
        'ci_paciente',
        'alergias',
        'enfermedades_cronicas',
        'medicamentos_actuales',
        'antecedentes_familiares',
        'observaciones',
    ];

    protected $casts = [
        'ci_paciente' => 'integer',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'ci_paciente', 'ci');
    }
}
