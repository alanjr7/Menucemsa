<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsistenteQuirofanos extends Model
{
    use HasFactory;

    protected $table = 'asistente_quirofanos';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'quirofano_id',
        'descripcion',
    ];

    public function quirofano()
    {
        return $this->belongsTo(Quirofano::class, 'quirofano_id');
    }

    public function medicos()
    {
        return $this->hasMany(Medico::class, 'asistente_id', 'id');
    }
}
