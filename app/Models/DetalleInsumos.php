<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleInsumos extends Model
{
    use HasFactory;

    protected $table = 'DETALLE_INSUMOS';
    protected $primaryKey = ['ID_FARMACIA', 'CODIGO_INSUMOS'];
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ID_FARMACIA',
        'CODIGO_INSUMOS',
        'LABORATORIO',
        'FECHA_VENCIMIENTO',
        'DESCRIPCION'
    ];

    protected $hidden = [
        'farmacia',
        'insumo'
    ];

    public $timestamps = false;

    public function farmacia()
    {
        return $this->belongsTo(Farmacia::class, 'ID_FARMACIA', 'ID');
    }

    public function insumo()
    {
        return $this->belongsTo(Insumos::class, 'CODIGO_INSUMOS', 'CODIGO');
    }

    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKeyName();
        if (!is_array($keys)) {
            return parent::setKeysForSaveQuery($query);
        }

        foreach ($keys as $keyName) {
            $query->where($keyName, '=', $this->getKeyForSaveQuery($keyName));
        }

        return $query;
    }

    protected function getKeyForSaveQuery($keyName = null)
    {
        if (is_null($keyName)) {
            $keyName = $this->getKeyName();
        }

        return $this->getAttribute($keyName);
    }
}
