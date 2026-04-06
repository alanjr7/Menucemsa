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
        'user_id',
    ];

    protected $casts = [
        'id' => 'string',
    ];

    public function pacientes()
    {
        return $this->hasMany(Paciente::class, 'triage_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
