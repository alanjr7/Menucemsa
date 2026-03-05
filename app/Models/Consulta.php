<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    use HasFactory;

    protected $table = 'consultas';
    protected $primaryKey = 'nro';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'nro',
        'fecha',
        'hora',
        'motivo',
        'observaciones',
        'codigo_especialidad',
        'ci_paciente',
        'ci_medico',
        'estado_pago',
        'id_caja',
        'estado',
    ];

    protected $casts = [
        'fecha' => 'date',
        'estado_pago' => 'boolean',
    ];

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'codigo_especialidad', 'codigo');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'ci_paciente', 'ci');
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'ci_medico', 'ci');
    }

    public function caja()
    {
        return $this->belongsTo(Caja::class, 'id_caja', 'id');
    }

    public function recetas()
    {
        return $this->hasMany(Receta::class, 'nro_consulta', 'nro');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($consulta) {
            if (empty($consulta->nro)) {
                $consulta->nro = 'CONS-' . date('Y') . '-' . str_pad(Consulta::count() + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }
}
