<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentaFarmacia extends Model
{
    use HasFactory;

    protected $table = 'VENTAS_FARMACIA';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'CODIGO_VENTA',
        'ID_FARMACIA',
        'CLIENTE',
        'TOTAL',
        'METODO_PAGO',
        'REQUIERE_RECETA',
        'FECHA_VENTA',
        'ESTADO',
        'OBSERVACIONES',
        'caja_diaria_id'
    ];

    protected $casts = [
        'TOTAL' => 'decimal:2',
        'REQUIERE_RECETA' => 'boolean',
        'FECHA_VENTA' => 'datetime',
    ];

    public function detalles()
    {
        return $this->hasMany(DetalleVentaFarmacia::class, 'CODIGO_VENTA', 'CODIGO_VENTA');
    }

    public function farmacia()
    {
        return $this->belongsTo(Farmacia::class, 'ID_FARMACIA', 'ID');
    }

    public function cajaDiaria()
    {
        return $this->belongsTo(CajaDiaria::class);
    }

    public static function generarCodigoVenta()
    {
        $ultimo = self::orderBy('id', 'desc')->first();
        $numero = $ultimo ? $ultimo->id + 1 : 1;
        return 'VTA-' . str_pad($numero, 6, '0', STR_PAD_LEFT);
    }
}
