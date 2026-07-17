<?php

namespace App\Models;

use App\Support\Money;
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
        'aplica_retencion',
        'retencion_tipo',
        'retencion_iue',
        'retencion_it',
        'observaciones',
        'user_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
        'con_credito_fiscal' => 'boolean',
        'importe_iva' => 'decimal:2',
        'aplica_retencion' => 'boolean',
        'retencion_iue' => 'decimal:2',
        'retencion_it' => 'decimal:2',
        'anulado_at' => 'datetime',
    ];

    /**
     * Tasas de retención cuando se paga sin factura a una persona natural.
     * Servicios (honorarios médicos): IUE 12,5% + IT 3% = 15,5%.
     * Bienes: IUE 5% + IT 3% = 8%.
     */
    public const RETENCION_TASAS = [
        'servicios' => ['iue' => '0.125', 'it' => '0.03'],
        'bienes' => ['iue' => '0.05', 'it' => '0.03'],
    ];

    public const RETENCION_TIPOS = [
        'servicios' => 'Servicios (IUE 12,5% + IT 3% = 15,5%)',
        'bienes' => 'Bienes (IUE 5% + IT 3% = 8%)',
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

    public function anuladoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'anulado_por');
    }

    public function scopeEntreFechas($query, $inicio, $fin)
    {
        return $query->whereBetween('fecha', [$inicio, $fin]);
    }

    /** Egresos no anulados — los únicos que cuentan para el flujo de caja. */
    public function scopeVigentes($query)
    {
        return $query->whereNull('anulado_at');
    }

    public function getAnuladoAttribute(): bool
    {
        return $this->anulado_at !== null;
    }

    /** Anula el egreso (reversible + auditado). No se borra: queda visible para auditoría. */
    public function anular(int $userId, string $motivo): void
    {
        $this->anulado_at = now();
        $this->anulado_por = $userId;
        $this->motivo_anulacion = $motivo;
        $this->save();
    }

    /** Revierte una anulación: el egreso vuelve a contar para el flujo de caja. */
    public function revertirAnulacion(): void
    {
        $this->anulado_at = null;
        $this->anulado_por = null;
        $this->motivo_anulacion = null;
        $this->save();
    }

    /** Calcula la retención IUE/IT sobre un monto bruto según el tipo (servicios/bienes). */
    public static function calcularRetenciones(string $monto, string $tipo): array
    {
        $tasas = self::RETENCION_TASAS[$tipo] ?? self::RETENCION_TASAS['servicios'];

        return [
            'iue' => Money::mul($monto, $tasas['iue']),
            'it' => Money::mul($monto, $tasas['it']),
        ];
    }

    /** Total retenido = IUE + IT. */
    public function getRetencionTotalAttribute(): string
    {
        return Money::add($this->retencion_iue, $this->retencion_it);
    }

    /** Neto efectivamente pagado al beneficiario = monto bruto - retenciones. */
    public function getNetoPagadoAttribute(): string
    {
        return Money::sub($this->monto, $this->retencion_total);
    }

    public function getRetencionTipoLabelAttribute(): string
    {
        return self::RETENCION_TIPOS[$this->retencion_tipo] ?? '—';
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
