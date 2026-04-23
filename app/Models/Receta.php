<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receta extends Model
{
    use HasFactory;

    protected $table = 'recetas';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nro',
        'fecha',
        'indicaciones',
        'user_medico_id',
        'consulta_id',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'consulta_id');
    }

    public function userMedico()
    {
        return $this->belongsTo(User::class, 'user_medico_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleReceta::class, 'receta_id');
    }

    public function paciente()
    {
        return $this->hasOneThrough(
            \App\Models\Paciente::class,
            \App\Models\Consulta::class,
            'id',
            'ci',
            'consulta_id',
            'ci_paciente'
        );
    }
}
