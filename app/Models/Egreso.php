<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Egreso extends Model
{
    use HasFactory;

    protected $table = 'egresos';

    protected $fillable = [
        'fecha',
        'categoria',
        'descripcion',
        'monto',
        'metodo_pago',
        'proveedor',
        'comprobante_nro',
        'con_credito_fiscal',
        'nit_proveedor',
        'nro_factura',
        'codigo_autorizacion',
        'importe_iva',
        'observaciones',
        'user_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
        'con_credito_fiscal' => 'boolean',
        'importe_iva' => 'decimal:2',
    ];

    public const CATEGORIAS = [
        'sueldos' => 'Sueldos',
        'honorarios' => 'Honorarios médicos',
        'alquiler' => 'Alquiler',
        'servicios_basicos' => 'Servicios básicos',
        'insumos_medicos' => 'Insumos médicos',
        'mantenimiento' => 'Mantenimiento',
        'limpieza' => 'Limpieza',
        'equipamiento' => 'Equipamiento',
        'impuestos' => 'Impuestos',
        'otros' => 'Otros',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeEntreFechas($query, $inicio, $fin)
    {
        return $query->whereBetween('fecha', [$inicio, $fin]);
    }

    public function getCategoriaLabelAttribute(): string
    {
        return self::CATEGORIAS[$this->categoria] ?? ucfirst($this->categoria);
    }

    public function getMetodoPagoLabelAttribute(): string
    {
        return match ($this->metodo_pago) {
            'efectivo' => 'Efectivo',
            'transferencia' => 'Transferencia',
            'cheque' => 'Cheque',
            'tarjeta' => 'Tarjeta',
            'qr' => 'QR',
            default => ucfirst($this->metodo_pago),
        };
    }
}
