<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\VentaFarmacia;

class CajaDiaria extends Model
{
    use HasFactory;

    protected $table = 'caja_diarias';

    protected $fillable = [
        'fecha',
        'monto_inicial',
        'monto_final',
        'ventas_efectivo',
        'ventas_qr',
        'ventas_transferencia',
        'ventas_tarjeta',
        'total_ventas',
        'estado',
        'usuario_id',
        'observaciones',
        'hora_apertura',
        'hora_cierre'
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto_inicial' => 'decimal:2',
        'monto_final' => 'decimal:2',
        'ventas_efectivo' => 'decimal:2',
        'ventas_qr' => 'decimal:2',
        'ventas_transferencia' => 'decimal:2',
        'ventas_tarjeta' => 'decimal:2',
        'total_ventas' => 'decimal:2',
        'hora_apertura' => 'datetime',
        'hora_cierre' => 'datetime'
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id', 'id');
    }

    public function ventas()
    {
        return $this->hasMany(VentaFarmacia::class, 'caja_diaria_id');
    }

    // Scopes
    public function scopeAbierta($query)
    {
        return $query->where('estado', 'abierta');
    }

    public function scopeCerrada($query)
    {
        return $query->where('estado', 'cerrada');
    }

    public function scopeDelDia($query, $fecha)
    {
        return $query->where('fecha', $fecha);
    }

    // Métodos
    public function abrirCaja($montoInicial, $observaciones = null)
    {
        $this->monto_inicial = $montoInicial;
        $this->estado = 'abierta';
        $this->hora_apertura = now();
        $this->observaciones = $observaciones;
        $this->save();
    }

    public function cerrarCaja($observaciones = null)
    {
        // Calcular totales del día
        $this->calcularTotalesDia();
        
        $this->estado = 'cerrada';
        $this->hora_cierre = now();
        if ($observaciones) {
            $this->observaciones = $observaciones;
        }
        $this->save();
    }

    private function calcularTotalesDia()
    {
        $ventas = VentaFarmacia::whereDate('fecha_venta', $this->fecha)->get();

        $this->ventas_efectivo = $ventas->where('metodo_pago', 'efectivo')->sum('total');
        $this->ventas_qr = $ventas->where('metodo_pago', 'qr')->sum('total');
        $this->ventas_transferencia = $ventas->where('metodo_pago', 'transferencia')->sum('total');
        $this->ventas_tarjeta = $ventas->where('metodo_pago', 'tarjeta')->sum('total');
        $this->total_ventas = $ventas->sum('total');
        $this->monto_final = $this->monto_inicial + $this->total_ventas;
    }

    public static function getCajaAbiertaHoy()
    {
        return static::whereDate('fecha', now()->format('Y-m-d'))->abierta()->first();
    }

    public static function hayCajaAbiertaHoy()
    {
        return static::getCajaAbiertaHoy() !== null;
    }
}
