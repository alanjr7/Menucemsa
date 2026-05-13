<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluacion extends Model
{
    protected $table = 'evaluaciones';

    protected $fillable = ['paciente_ci', 'temp_id', 'area', 'user_id', 'observaciones', 'signos_vitales', 'episodio_id'];

    protected $casts = ['signos_vitales' => 'array'];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_ci', 'ci');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(EvaluacionItem::class);
    }

    public function episodio()
    {
        return $this->belongsTo(\App\Models\Episodio::class);
    }
}
