<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarioFarmacia extends Model
{
    use HasFactory;

    protected $table = 'inventario_farmacia';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'farmacia_id',
        'tipo_item',
        'codigo_item',
        'laboratorio',
        'fecha_vencimiento',
        'tipo',
        'requerimiento',
        'stock_minimo',
        'stock_disponible',
        'reposicion',
        'lote',
        'fecha_ingreso'
    ];

    public $timestamps = true;

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'fecha_ingreso' => 'date',
        'stock_minimo' => 'integer',
        'stock_disponible' => 'integer',
        'reposicion' => 'integer',
    ];

    public function farmacia()
    {
        return $this->belongsTo(Farmacia::class, 'farmacia_id', 'id');
    }

    public function medicamento()
    {
        return $this->belongsTo(Medicamentos::class, 'codigo_item', 'codigo');
    }
}
