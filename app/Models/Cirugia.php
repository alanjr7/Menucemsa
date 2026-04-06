<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $casts = [
        'fecha' => 'date',
        'emergencia_id' => 'integer',
        'quirofano_id' => 'integer',
    ];

    public function quirofano()
    {
        return $this->belongsTo(Quirofano::class, 'quirofano_id');
    }
}
