<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleMedicamentos extends Model
{
    use HasFactory;

    protected $table = 'DETALLE_MEDICAMENTOS';
    protected $primaryKey = ['ID_FARMACIA', 'CODIGO_MEDICAMENTOS'];
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ID_FARMACIA',
        'CODIGO_MEDICAMENTOS',
        'LABORATORIO',
        'FECHA_VENCIMIENTO',
        'TIPO',
        'REQUERIMIENTO'
    ];

    protected $hidden = [
        'farmacia',
        'medicamento'
    ];

    public $timestamps = false;

    public function farmacia()
    {
        return $this->belongsTo(Farmacia::class, 'ID_FARMACIA', 'ID');
    }

    public function medicamento()
    {
        return $this->belongsTo(Medicamentos::class, 'CODIGO_MEDICAMENTOS', 'CODIGO');
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
