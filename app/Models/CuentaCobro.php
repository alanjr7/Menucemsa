<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CuentaCobro extends Model
{
    use HasFactory;

    protected $table = 'cuenta_cobros';
    
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'paciente_ci',
        'tipo_atencion',
        'referencia_id',
        'referencia_type',
        'estado',
        'total_calculado',
        'total_pagado',
        'es_emergencia',
        'es_post_pago',
        'ci_nit_facturacion',
        'razon_social',
        'caja_session_id',
        'usuario_caja_id',
        'observaciones',
        'seguro_estado',
        'seguro_id',
        'seguro_autorizado_por',
        'seguro_fecha_autorizacion',
        'seguro_observaciones',
        'seguro_monto_cobertura',
        'seguro_monto_paciente',
    ];

    protected $casts = [
        'total_calculado' => 'decimal:2',
        'total_pagado' => 'decimal:2',
        'es_emergencia' => 'boolean',
        'es_post_pago' => 'boolean',
        'seguro_fecha_autorizacion' => 'datetime',
        'seguro_monto_cobertura' => 'decimal:2',
        'seguro_monto_paciente' => 'decimal:2',
    ];

    protected $appends = [
        'estado_color',
        'estado_label',
        'tipo_atencion_label',
        'saldo_pendiente',
    ];

    /**
     * Calcular saldo pendiente dinámicamente
     */
    public function getSaldoPendienteAttribute(): float
    {
        $cobertura = $this->seguro_estado === 'autorizado' ? (float) $this->seguro_monto_cobertura : 0;
        return max(0, (float) $this->total_calculado - $cobertura - (float) $this->total_pagado);
    }

    // Relaciones
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_ci', 'ci');
    }

    public function referencia(): MorphTo
    {
        return $this->morphTo();
    }

    public function cajaSession(): BelongsTo
    {
        return $this->belongsTo(CajaSession::class);
    }

    public function usuarioCaja(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_caja_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(CuentaCobroDetalle::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(PagoCuenta::class);
    }

    public function seguro(): BelongsTo
    {
        return $this->belongsTo(Seguro::class);
    }

    public function seguroAutorizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seguro_autorizado_por');
    }

    // Scopes
    public function scopePendiente($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeVisiblesEnCaja($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('seguro_estado')
              ->orWhere('seguro_estado', 'rechazado');
        });
    }

    public function scopePendientesSeguro($query)
    {
        return $query->where('seguro_estado', 'pendiente_autorizacion');
    }

    public function scopeAutorizadosSeguro($query)
    {
        return $query->where('seguro_estado', 'autorizado');
    }

    public function scopePagado($query)
    {
        return $query->where('estado', 'pagado');
    }

    public function scopeParcial($query)
    {
        return $query->where('estado', 'parcial');
    }

    public function scopeDelDia($query, $fecha = null)
    {
        $fecha = $fecha ?? now()->toDateString();
        return $query->whereDate('created_at', $fecha);
    }

    public function scopeEmergencias($query)
    {
        return $query->where('es_emergencia', true);
    }

    public function scopePostPago($query)
    {
        return $query->where('es_post_pago', true);
    }

    // Métodos de estado
    public function estaPendiente(): bool
    {
        return $this->estado === 'pendiente';
    }

    public function estaPagado(): bool
    {
        return $this->estado === 'pagado';
    }

    public function esPagoParcial(): bool
    {
        return $this->estado === 'parcial';
    }

    public function getEstadoColorAttribute(): string
    {
        return match($this->estado) {
            'pagado' => 'green',
            'parcial' => 'yellow',
            'pendiente' => 'red',
            default => 'gray',
        };
    }

    public function getEstadoLabelAttribute(): string
    {
        return match($this->estado) {
            'pagado' => 'Pagado',
            'parcial' => 'Pago Parcial',
            'pendiente' => 'Pendiente',
            default => 'Desconocido',
        };
    }

    public function getTipoAtencionLabelAttribute(): string
    {
        return match($this->tipo_atencion) {
            'consulta_externa' => 'Consulta Externa',
            'emergencia' => 'Emergencia',
            'hospitalizacion' => 'Hospitalización',
            'cirugia' => 'Cirugía',
            'laboratorio' => 'Laboratorio',
            'imagenologia' => 'Imagenología',
            'farmacia' => 'Farmacia',
            default => ucfirst(str_replace('_', ' ', $this->tipo_atencion)),
        };
    }

    // Calcular totales
    public function recalcularTotales(): void
    {
        $this->total_calculado = $this->detalles->sum('subtotal');
        
        if ($this->seguro_estado === 'autorizado' && $this->seguro) {
            $calculo = $this->seguro->calcularCobertura((float)$this->total_calculado);
            $this->seguro_monto_cobertura = $calculo['monto_cubierto'];
            $this->seguro_monto_paciente = $calculo['monto_paciente'];
        }

        $cobertura = $this->seguro_estado === 'autorizado' ? (float) $this->seguro_monto_cobertura : 0;
        $saldoPendiente = (float) $this->total_calculado - $cobertura - (float) $this->total_pagado;
        
        if ($saldoPendiente <= 0) {
            $this->estado = 'pagado';
        } elseif ($this->total_pagado > 0 || $cobertura > 0) {
            $this->estado = 'parcial';
        } else {
            $this->estado = 'pendiente';
        }
        
        $this->save();
    }

    // Registrar un pago
    public function registrarPago(float $monto, string $metodoPago, ?string $referencia = null, ?int $usuarioId = null): void
    {
        $this->pagos()->create([
            'monto' => $monto,
            'metodo_pago' => $metodoPago,
            'referencia' => $referencia,
            'user_id' => $usuarioId ?? auth()->id(),
            'caja_session_id' => $this->caja_session_id,
        ]);

        $this->total_pagado += $monto;
        $this->recalcularTotales();
    }

    // Generar código único
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cuenta) {
            if (empty($cuenta->id)) {
                $cuenta->id = 'CC-' . date('YmdHis') . '-' . str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
            }
        });
    }
}
