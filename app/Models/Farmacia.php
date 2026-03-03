<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Farmacia extends Model
{
    use HasFactory;

    protected $table = 'FARMACIA';
    protected $primaryKey = 'ID';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ID',
        'DETALLE'
    ];

    public $timestamps = false;

    public function detalleMedicamentos()
    {
        return $this->hasMany(DetalleMedicamentos::class, 'ID_FARMACIA', 'ID');
    }

    public function detalleInsumos()
    {
        return $this->hasMany(DetalleInsumos::class, 'ID_FARMACIA', 'ID');
    }

    public function detalleRecetas()
    {
        return $this->hasMany(DetalleReceta::class, 'ID_FARMACIA', 'ID');
    }

    public function inventarios()
    {
        return $this->hasMany(Inventario::class, 'ID_FARMACIA', 'ID');
    }
}
