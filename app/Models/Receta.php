<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receta extends Model
{
    use HasFactory;

    protected $table = 'recetas';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'nro_consulta',
        'ci_medico',
        'ci_paciente',
        'fecha',
        'indicaciones',
        'estado',
    ];

    protected $casts = [
        'fecha' => 'date',
        'nro_consulta' => 'string',
        'ci_medico' => 'integer',
        'ci_paciente' => 'integer',
    ];

    public function consulta()
    {
        return $this->belongsTo(Consulta::class, 'nro_consulta', 'nro');
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'ci_medico', 'ci');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'ci_paciente', 'ci');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleReceta::class, 'id_receta', 'id');
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($receta) {
            if (empty($receta->id)) {
                $receta->id = 'REC-' . date('Y') . '-' . str_pad(Receta::count() + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }
}
