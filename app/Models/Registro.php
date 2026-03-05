<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registro extends Model
{
    use HasFactory;

    protected $table = 'registros';
    protected $primaryKey = 'codigo';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'codigo',
        'fecha',
        'hora',
        'motivo',
        'id_usuario',
    ];

    protected $casts = [
        'codigo' => 'string',
        'fecha' => 'date',
        'id_usuario' => 'integer',
    ];

    public function pacientes()
    {
        return $this->hasMany(Paciente::class, 'codigo_registro', 'codigo');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }
}
