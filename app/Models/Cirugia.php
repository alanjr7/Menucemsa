<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cirugia extends Model
{
    use HasFactory;

    protected $table = 'cirugias';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'ci_paciente',
        'ci_medico',
        'codigo_especialidad',
        'fecha_programada',
        'hora_programada',
        'tipo_cirugia',
        'descripcion',
        'estado',
        'quirofano',
    ];

    protected $casts = [
        'fecha_programada' => 'date',
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

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'codigo_especialidad', 'codigo');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($cirugia) {
            if (empty($cirugia->id)) {
                $cirugia->id = 'CIR-' . date('Y') . '-' . str_pad(Cirugia::count() + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }
}
