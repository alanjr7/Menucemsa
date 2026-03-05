<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hospitalizacion extends Model
{
    use HasFactory;

    protected $table = 'hospitalizaciones';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'ci_paciente',
        'fecha_ingreso',
        'hora_ingreso',
        'fecha_alta',
        'hora_alta',
        'motivo',
        'diagnostico',
        'tratamiento',
        'ci_medico',
        'estado',
        'cama',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'fecha_alta' => 'date',
        'ci_paciente' => 'integer',
        'ci_medico' => 'integer',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'ci_paciente', 'ci');
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'ci_medico', 'ci');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($hospitalizacion) {
            if (empty($hospitalizacion->id)) {
                $hospitalizacion->id = 'HOSP-' . date('Y') . '-' . str_pad(Hospitalizacion::count() + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }
}
