<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentaFarmacia extends Model
{
    use HasFactory;

    protected $table = 'ventas_farmacia';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'codigo_venta',
        'farmacia_id',
        'cliente',
        'total',
        'metodo_pago',
        'requiere_receta',
        'fecha_venta',
        'estado',
        'observaciones',
        'caja_diaria_id'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'requiere_receta' => 'boolean',
        'fecha_venta' => 'datetime',
        'metodo_pago' => 'string',
        'estado' => 'string',
    ];

    public function detalles()
    {
        return $this->hasMany(DetalleVentaFarmacia::class, 'codigo_venta', 'codigo_venta');
    }

    public function farmacia()
    {
        return $this->belongsTo(Farmacia::class, 'farmacia_id');
    }

    public function cajaDiaria()
    {
        return $this->belongsTo(CajaDiaria::class);
    }
}
