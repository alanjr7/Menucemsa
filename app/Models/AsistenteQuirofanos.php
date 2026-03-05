<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsistenteQuirofanos extends Model
{
    use HasFactory;

    protected $table = 'asistente_quirofanos';
    
    protected $primaryKey = ['id', 'nro_quirofano'];
    
    public $incrementing = false;
    
    protected $keyType = 'string';
    
    protected $fillable = [
        'id',
        'nro_quirofano',
        'descripcion',
    ];

    public function quirofano()
    {
        return $this->belongsTo(Quirofano::class, 'nro_quirofano', 'nro');
    }

    public function medicos()
    {
        return $this->hasMany(Medico::class, 'id_asistente', 'id');
    }
}
