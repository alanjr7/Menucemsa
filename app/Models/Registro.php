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
        'user_id',
    ];

    protected $casts = [
        'codigo' => 'string',
        'fecha' => 'date',
    ];

    public function pacientes()
    {
        return $this->hasMany(Paciente::class, 'registro_codigo', 'codigo');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
