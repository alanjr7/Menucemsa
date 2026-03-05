<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seguro extends Model
{
    use HasFactory;

    protected $table = 'seguros';
    protected $primaryKey = 'codigo';
    protected $keyType = 'integer';
    public $incrementing = false;

    protected $fillable = [
        'codigo',
        'nombre_empresa',
        'tipo',
        'cobertura',
        'telefono',
        'formulario',
        'estado',
    ];

    protected $casts = [
        'codigo' => 'integer',
        'telefono' => 'integer',
    ];

    public function pacientes()
    {
        return $this->hasMany(Paciente::class, 'codigo_seguro', 'codigo');
    }
}
