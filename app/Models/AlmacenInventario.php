<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlmacenInventario extends Model
{
    protected $table = 'almacen_inventario';

    protected $fillable = [
        'codigo_activo',
        'nombre',
        'precio',
        'cantidad',
        'marca',
        'proveedor',
        'nro_factura',
        'numero_recibo',
    ];

    protected $casts = [
        'precio'   => 'decimal:2',
        'cantidad' => 'integer',
    ];
}
