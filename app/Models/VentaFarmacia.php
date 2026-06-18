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
        'cliente',
        'con_credito_fiscal',
        'factura_razon_social',
        'factura_tipo_documento',
        'factura_numero_documento',
        'factura_complemento',
        'total',
        'metodo_pago',
        'requiere_receta',
        'fecha_venta',
        'estado',
        'observaciones',
        'caja_diaria_id'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'requiere_receta' => 'boolean',
        'con_credito_fiscal' => 'boolean',
        'factura_tipo_documento' => 'integer',
        'fecha_venta' => 'datetime',
        'metodo_pago' => 'string',
        'estado' => 'string',
    ];

    public $timestamps = false;

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
