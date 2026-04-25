<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Emergency;

class Cirugia extends Model
{
    use HasFactory;

    protected $table = 'cirugias';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'codigo',
        'fecha',
        'hora',
        'tipo',
        'descripcion',
        'emergencia_id',
        'quirofano_id',
        'equipos_medicos',
    ];

    protected $casts = [
        'fecha' => 'date',
        'emergencia_id' => 'integer',
        'quirofano_id' => 'integer',
        'equipos_medicos' => 'array',
    ];

    public function quirofano()
    {
        return $this->belongsTo(Quirofano::class, 'quirofano_id');
    }

    public function emergencia()
    {
        return $this->belongsTo(Emergency::class, 'emergencia_id');
    }
}
