<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentaFarmacia extends Model
{
    use HasFactory;

    protected $table = 'ventas_farmacia';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'codigo_venta',
        'farmacia_id',
        'usuario_id',
        'cliente_id',
        'paciente_id',
        'cliente',
        'con_credito_fiscal',
        'factura_razon_social',
        'factura_tipo_documento',
        'factura_numero_documento',
        'factura_complemento',
        'total',
        'base_imponible',
        'debito_fiscal',
        'metodo_pago',
        'requiere_receta',
        'fecha_venta',
        'estado',
        'observaciones',
        'anulado_at',
        'anulado_por',
        'motivo_anulacion',
        'caja_diaria_id'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'base_imponible' => 'decimal:2',
        'debito_fiscal' => 'decimal:2',
        'requiere_receta' => 'boolean',
        'con_credito_fiscal' => 'boolean',
        'factura_tipo_documento' => 'integer',
        'fecha_venta' => 'datetime',
        'anulado_at' => 'datetime',
        'metodo_pago' => 'string',
        'estado' => 'string',
    ];

    /** Ventas que cuentan como ingreso (excluye anuladas/pendientes). */
    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'COMPLETADA');
    }

    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($venta) {
            // Débito fiscal IVA (Libro de Ventas): el total ya incluye IVA 13% (por dentro).
            if (empty($venta->base_imponible) || (float) $venta->base_imponible === 0.0) {
                $venta->base_imponible = $venta->total;
                $venta->debito_fiscal = \App\Support\Impuestos::iva((string) $venta->total);
            }
        });
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVentaFarmacia::class, 'codigo_venta', 'codigo_venta');
    }

    public function farmacia()
    {
        return $this->belongsTo(Farmacia::class, 'farmacia_id');
    }

    public function cajaDiaria()
    {
        return $this->belongsTo(CajaDiaria::class);
    }

    public function usuario()
    {
        return $this->belongsTo(\App\Models\User::class, 'usuario_id');
    }

    public function clienteRegistrado()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function anuladoPor()
    {
        return $this->belongsTo(User::class, 'anulado_por');
    }

    // Etiqueta legible del tipo de documento de la factura (catálogo SIN)
    public function getFacturaTipoDocumentoLabelAttribute(): string
    {
        return \App\Support\TipoDocumento::labelFor($this->factura_tipo_documento);
    }

    public static function generarCodigoVenta()
    {
        $prefijo = 'VTF';
        $fecha = now()->format('Ymd');
        
        $last = self::whereDate('fecha_venta', today())
            ->max(\DB::raw("CAST(SUBSTRING_INDEX(codigo_venta, 'VTF" . $fecha . "', -1) AS UNSIGNED)")) ?? 0;
        
        $numero = $last + 1;
        $numeroFormateado = str_pad($numero, 4, '0', STR_PAD_LEFT);
        
        return $prefijo . $fecha . $numeroFormateado;
    }
}
