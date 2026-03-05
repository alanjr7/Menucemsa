<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Emergencia extends Model
{
    use HasFactory;

    protected $table = 'emergencias';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'ci_paciente',
        'fecha',
        'hora',
        'motivo',
        'descripcion',
        'triage_nivel',
        'ci_medico',
        'estado',
    ];

    protected $casts = [
        'fecha' => 'date',
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

        static::creating(function ($emergencia) {
            if (empty($emergencia->id)) {
                $emergencia->id = 'EMER-' . date('Y') . '-' . str_pad(Emergencia::count() + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }
}
