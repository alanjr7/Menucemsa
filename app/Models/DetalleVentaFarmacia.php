<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVentaFarmacia extends Model
{
    use HasFactory;

    protected $table = 'DETALLE_VENTAS_FARMACIA';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'CODIGO_VENTA',
        'CODIGO_PRODUCTO',
        'TIPO_PRODUCTO',
        'NOMBRE_PRODUCTO',
        'CANTIDAD',
        'PRECIO_UNITARIO',
        'SUBTOTAL'
    ];

    protected $casts = [
        'CANTIDAD' => 'integer',
        'PRECIO_UNITARIO' => 'decimal:2',
        'SUBTOTAL' => 'decimal:2',
    ];

    public function venta()
    {
        return $this->belongsTo(VentaFarmacia::class, 'CODIGO_VENTA', 'CODIGO_VENTA');
    }
}
