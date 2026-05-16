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
        'paciente_id',
        'fecha',
        'detalle',
        'observaciones',
        'alergias',
        'user_medico_id',
        'episodio_id',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function userMedico()
    {
        return $this->belongsTo(User::class, 'user_medico_id', 'id');
    }

    public function episodio()
    {
        return $this->belongsTo(\App\Models\Episodio::class);
    }
}
