<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Triage extends Model
{
    use HasFactory;

    protected $table = 'triages';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'color',
        'descripcion',
        'prioridad',
        'id_usuario',
    ];

    protected $casts = [
        'id' => 'string',
        'id_usuario' => 'integer',
    ];

    public function pacientes()
    {
        return $this->hasMany(Paciente::class, 'id_triage', 'id');
    }

    public function emergencias()
    {
        return $this->hasMany(Emergencia::class, 'id_triage', 'id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }
}
