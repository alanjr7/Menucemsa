<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    use HasFactory;

    protected $table = 'cajas';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'fecha',
        'total_dia',
        'tipo',
        'nro_factura',
        'id_farmacia',
        'metodo_pago',
        'referencia',
        'estado',
    ];

    protected $casts = [
        'id' => 'string',
        'fecha' => 'datetime',
        'total_dia' => 'float',
        'nro_factura' => 'integer',
    ];

    /**
     * @deprecated Tabla 'facturas' no existe. Usar CuentaCobro para cobros.
     */
    public function factura()
    {
        return $this;
    }

    /**
     * @deprecated Usar VentaFarmacia con farmacia_id.
     */
    public function farmacia()
    {
        return $this;
    }

    public function consultas()
    {
        return $this->hasMany(Consulta::class, 'caja_id', 'id');
    }

    public function consulta()
    {
        return $this->hasOne(Consulta::class, 'caja_id', 'id');
    }

    // Métodos para simular estado (ya que no existe en la BD)
    public function getEstadoAttribute()
    {
        // Si tiene factura, está pagado, si no, pendiente
        return $this->nro_factura ? 'pagado' : 'pendiente';
    }

    public function getMontoPagadoAttribute()
    {
        return $this->total_dia;
    }

    public function marcarComoPagado()
    {
        // Asignar número de factura para marcar como pagado
        $this->nro_factura = $this->generarNumeroFactura();
        $this->save();
    }

    public function getEstadoColorAttribute()
    {
        return $this->estado === 'pagado' ? 'green' : 'yellow';
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($caja) {
            if (empty($caja->id)) {
                $caja->id = 'CAJA-' . date('YmdHis') . '-' . str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
            }
            if (empty($caja->fecha)) {
                $caja->fecha = now();
            }
        });
    }

    private function generarNumeroFactura()
    {
        // Obtener el último número de factura y generar el siguiente
        $ultimaFactura = Caja::whereNotNull('nro_factura')->max('nro_factura') ?? 0;
        return $ultimaFactura + 1;
    }
}
