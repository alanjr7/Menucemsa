<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CajaFarmacia extends Model
{
    use HasFactory;

    protected $table = 'CAJA_FARMACIA';
    protected $primaryKey = 'CODIGO';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'CODIGO',
        'DETALLE',
        'TOTAL',
        'ID_CAJA',
        'FECHA'
    ];

    public $timestamps = false;

    protected $casts = [
        'FECHA' => 'datetime',
        'TOTAL' => 'decimal:2',
    ];

    public function caja()
    {
        return $this->belongsTo(Caja::class, 'ID_CAJA', 'ID');
    }
}
