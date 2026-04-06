<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialMedico extends Model
{
    use HasFactory;

    protected $table = 'historial_medicos';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'ci_paciente',
        'fecha',
        'detalle',
        'observaciones',
        'alergias',
        'user_medico_id',
    ];

    protected $casts = [
        'ci_paciente' => 'integer',
        'fecha' => 'date',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'ci_paciente', 'ci');
    }

    public function userMedico()
    {
        return $this->belongsTo(User::class, 'user_medico_id', 'id');
    }
}
