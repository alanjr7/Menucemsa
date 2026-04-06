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

    public function factura()
    {
        // Relación deshabilitada ya que la tabla facturas no existe
        return $this;
    }

    public function farmacia()
    {
        // Relación deshabilitada ya que la tabla farmacias tiene nombre diferente
        return $this;
    }

    public function consultas()
    {
        return $this->hasMany(Consulta::class, 'id_caja', 'id');
    }

    public function consulta()
    {
        return $this->hasOne(Consulta::class, 'id_caja', 'id');
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
                $caja->id = 'CAJA-' . date('YmdHis') . '-' . rand(100, 999);
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
