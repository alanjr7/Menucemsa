<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVentaFarmacia extends Model
{
    use HasFactory;

    protected $table = 'detalle_ventas_farmacia';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'codigo_venta',
        'codigo_producto',
        'tipo_producto',
        'nombre_producto',
        'cantidad',
        'precio_unitario',
        'subtotal'
    ];

    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public $timestamps = false;

    public function venta()
    {
        return $this->belongsTo(VentaFarmacia::class, 'codigo_venta', 'codigo_venta');
    }
}
