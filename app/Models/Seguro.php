<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seguro extends Model
{
    use HasFactory;

    protected $table = 'seguros';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nombre_empresa',
        'tipo',
        'cobertura',
        'telefono',
        'formulario',
        'estado',
    ];

    protected $casts = [
        'telefono' => 'string',
    ];

    public function pacientes()
    {
        return $this->hasMany(Paciente::class, 'seguro_id');
    }
}
