<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;

    protected $table = 'INVENTARIO';
    protected $primaryKey = ['ID', 'ID_FARMACIA'];
    public $incrementing = false;

    protected $fillable = [
        'ID',
        'ID_FARMACIA',
        'TIPO_ITEM',
        'STOCK_MINIMO',
        'STOCK_DISPONIBLE',
        'REPOSICION',
        'FECHA_INGRESO'
    ];

    public $timestamps = false;

    public function farmacia()
    {
        return $this->belongsTo(Farmacia::class, 'ID_FARMACIA', 'ID');
    }

    public function medicamento()
    {
        return $this->belongsTo(Medicamentos::class, 'ID', 'CODIGO');
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
