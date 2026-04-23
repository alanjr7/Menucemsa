<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    use HasFactory;

    protected $table = 'especialidades';
    protected $primaryKey = 'codigo';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'estado',
    ];

    protected $casts = [
        'codigo' => 'string',
    ];

    public function getRouteKeyName(): string
    {
        return 'codigo';
    }

    public function medicos()
    {
        return $this->hasMany(Medico::class, 'codigo_especialidad', 'codigo');
    }

    public function consultas()
    {
        return $this->hasMany(Consulta::class, 'codigo_especialidad', 'codigo');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($especialidad) {
            if (empty($especialidad->codigo)) {
                $codigo = strtoupper(substr($especialidad->nombre, 0, 3));
                $especialidad->codigo = $codigo . '-' . str_pad(Especialidad::count() + 1, 3, '0', STR_PAD_LEFT);
            }
        });
    }
}
