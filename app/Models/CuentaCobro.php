<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Support\Money;
use App\Support\TipoDocumento;

class CuentaCobro extends Model
{
    use HasFactory;

    protected $table = 'cuenta_cobros';
    
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'paciente_id',
        'tipo_atencion',
        'referencia_id',
        'referencia_type',
        'estado',
        'total_calculado',
        'total_pagado',
        'es_emergencia',
        'es_post_pago',
        'episodio_numero',
        'episodio_id',
        'ci_nit_facturacion',
        'razon_social',
        'factura_tipo_documento',
        'factura_complemento',
        'con_credito_fiscal',
        'caja_session_id',
        'user_caja_id',
        'observaciones',
        'seguro_estado',
        'seguro_id',
        'seguro_autorizado_por',
        'seguro_fecha_autorizacion',
        'seguro_observaciones',
        'seguro_nro_autorizacion',
        'seguro_monto_cobertura',
        'seguro_monto_paciente',
    ];

    protected $casts = [
        'total_calculado' => 'decimal:2',
        'total_pagado' => 'decimal:2',
        'es_emergencia' => 'boolean',
        'es_post_pago' => 'boolean',
        'con_credito_fiscal' => 'boolean',
        'factura_tipo_documento' => 'integer',
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
        $cobertura = $this->seguro_estado === 'autorizado' ? $this->seguro_monto_cobertura : 0;
        $saldo = Money::sub(Money::sub($this->total_calculado, $cobertura), $this->total_pagado);
        return (float) Money::clampZero($saldo);
    }

    /** Etiqueta SIN del tipo de documento del receptor (CI/NIT/...) o '—'. */
    public function getTipoDocumentoLabelAttribute(): string
    {
        return TipoDocumento::labelFor($this->factura_tipo_documento);
    }

    /**
     * Datos fiscales del receptor normalizados (fuente única para comprobante/RCV/SFE).
     * Si no se pidió crédito fiscal se devuelve el caso "sin nombre" (S/N · NIT · 0),
     * sin necesidad de materializarlo en la BD. Mismo resultado que ventas_farmacia.
     */
    public function receptorFiscal(): array
    {
        if (! $this->con_credito_fiscal) {
            return [
                'con_credito_fiscal' => false,
                'razon_social' => TipoDocumento::SIN_NOMBRE_RAZON,
                'tipo_documento' => TipoDocumento::SIN_NOMBRE_TIPO->value,
                'tipo_documento_label' => TipoDocumento::SIN_NOMBRE_TIPO->label(),
                'numero_documento' => TipoDocumento::SIN_NOMBRE_DOC,
                'complemento' => null,
            ];
        }

        return [
            'con_credito_fiscal' => true,
            'razon_social' => $this->razon_social,
            'tipo_documento' => $this->factura_tipo_documento,
            'tipo_documento_label' => TipoDocumento::labelFor($this->factura_tipo_documento),
            'numero_documento' => $this->ci_nit_facturacion,
            'complemento' => $this->factura_complemento,
        ];
    }

    // Relaciones
    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
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
        return $this->belongsTo(User::class, 'user_caja_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(CuentaCobroDetalle::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(PagoCuenta::class);
    }

    /** Devoluciones (notas de crédito) emitidas sobre pagos de esta cuenta. */
    public function devoluciones(): HasMany
    {
        return $this->hasMany(Devolucion::class);
    }

    public function seguro(): BelongsTo
    {
        return $this->belongsTo(Seguro::class);
    }

    public function seguroAutorizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seguro_autorizado_por');
    }

    /** Ventas devengadas a la aseguradora por la porción cubierta de esta cuenta. */
    public function seguroCobros(): HasMany
    {
        return $this->hasMany(SeguroCobro::class);
    }

    public function episodio(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Episodio::class);
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

    /**
     * Retorna los detalles agrupados por área para mostrar en el recibo.
     * Formato: ['emergencia' => [detalles...], 'quirofano' => [...], ...]
     */
    public function detallesPorArea(): array
    {
        $this->loadMissing('detalles');
        $areaLabels = [
            'emergencia'       => 'Emergencia',
            'quirofano'        => 'Quirófano / Cirugía',
            'internacion'      => 'Internación',
            'uti'              => 'UTI (Terapia Intensiva)',
            'farmacia'         => 'Farmacia',
            'consulta_externa' => 'Consulta Externa',
            null               => 'General',
        ];

        $grupos = [];
        foreach ($this->detalles as $detalle) {
            // Los cargos deshabilitados (anulados por corrección) no se facturan
            if ($detalle->deshabilitado_en !== null) {
                continue;
            }
            $area = $detalle->area_origen ?? null;
            $label = $areaLabels[$area] ?? ucfirst((string)$area);
            if (!isset($grupos[$label])) {
                $grupos[$label] = ['items' => [], 'subtotal' => 0];
            }
            $grupos[$label]['items'][] = $detalle;
            $grupos[$label]['subtotal'] = (float) Money::add($grupos[$label]['subtotal'], $detalle->subtotal);
        }

        return $grupos;
    }

    // Calcular totales
    public function recalcularTotales(): void
    {
        // Sólo los cargos activos (no deshabilitados) suman al total facturable
        $this->total_calculado = $this->detalles
            ->whereNull('deshabilitado_en')
            ->reduce(fn ($acc, $d) => Money::add($acc, $d->subtotal), '0');

        // Autorización ABIERTA por episodio: mientras la venta a la aseguradora siga
        // PENDIENTE de liquidación, la cobertura sigue al total en vivo (cubre todos los
        // cargos nuevos según la regla %/tope) y se sincroniza la venta devengada. Una vez
        // LIQUIDADA (cobrada a la aseguradora) la cobertura queda congelada: cargos
        // posteriores los paga el paciente / requieren nueva autorización. El guard
        // `seguro_monto_cobertura === null` cubre el caso de autorización sin venta creada.
        if ($this->seguro_estado === 'autorizado' && $this->seguro) {
            $cobroAbierto = $this->seguroCobros()->where('estado', 'pendiente')->first();

            if ($cobroAbierto || $this->seguro_monto_cobertura === null) {
                $calculo = $this->seguro->calcularCobertura((float) $this->total_calculado, $this->topeDisponibleSeguro($this->seguro));
                $this->seguro_monto_cobertura = $calculo['monto_cubierto'];
                $this->seguro_monto_paciente = $calculo['monto_paciente'];

                if ($cobroAbierto) {
                    $cobroAbierto->actualizarMonto($calculo['monto_cubierto']);
                }
            }
        }

        $cobertura = $this->seguro_estado === 'autorizado' ? $this->seguro_monto_cobertura : 0;
        $saldoPendiente = Money::sub(Money::sub($this->total_calculado, $cobertura), $this->total_pagado);

        if (Money::cmp($saldoPendiente, 0) <= 0) {
            $this->estado = 'pagado';
        } elseif (Money::cmp($this->total_pagado, 0) > 0 || Money::cmp($cobertura, 0) > 0) {
            $this->estado = 'parcial';
        } else {
            $this->estado = 'pendiente';
        }
        
        $this->save();
    }

    /**
     * Autoriza el seguro sobre esta cuenta y materializa la venta devengada a la
     * aseguradora. Fuente única de autorización (la usan el cobro automático en caja
     * y la pre-autorización manual de admin), para que toda autorización deje:
     *   - el snapshot congelado en la cuenta (seguro_monto_cobertura / _paciente),
     *   - la cuenta por cobrar a la aseguradora ({@see SeguroCobro}) con débito fiscal.
     *
     * Autorización ABIERTA por episodio: la cobertura se calcula sobre el TOTAL del
     * episodio y queda abierta (la venta se sincroniza con el total en vivo) hasta que se
     * liquide a la aseguradora. Re-autorizar por cargo no es necesario en este modelo.
     *
     * @param  float  $montoBase  Compatibilidad: ya no determina la cobertura (se usa el
     *                            total del episodio). Se conserva por la firma existente.
     * @return array{cubierto: float, paciente: float}
     */
    public function autorizarSeguro(Seguro $seguro, float $montoBase = 0, array $meta = []): array
    {
        $calculo = $seguro->calcularCobertura((float) $this->total_calculado, $this->topeDisponibleSeguro($seguro));

        $this->forceFill([
            'seguro_id'                 => $seguro->id,
            'seguro_estado'             => 'autorizado',
            'seguro_autorizado_por'     => $meta['autorizado_por'] ?? auth()->id(),
            'seguro_fecha_autorizacion' => now(),
            'seguro_observaciones'      => $meta['observaciones'] ?? $this->seguro_observaciones,
            'seguro_nro_autorizacion'   => $meta['nro_autorizacion'] ?? $this->seguro_nro_autorizacion,
            'seguro_monto_cobertura'    => $calculo['monto_cubierto'],
            'seguro_monto_paciente'     => $calculo['monto_paciente'],
        ])->save();

        SeguroCobro::registrarPara($this, $seguro, $calculo['monto_cubierto'], $meta['autorizado_por'] ?? auth()->id());

        // recalcular sincroniza cobertura + venta con el total en vivo (modelo abierto).
        $this->recalcularTotales();

        return [
            'cubierto' => (float) $this->seguro_monto_cobertura,
            'paciente' => (float) $this->seguro_monto_paciente,
        ];
    }

    /**
     * Saldo del tope (seguro tipo tope_monto) que el paciente aún no consumió este período.
     * Devuelve null cuando no aplica (otro tipo de cobertura, o cuenta sin paciente): en ese
     * caso el cálculo usa el tope completo. Hace que el tope sea un límite AGREGADO por
     * paciente/gestión y no un límite que se reinicia en cada cuenta.
     */
    private function topeDisponibleSeguro(Seguro $seguro): ?float
    {
        if ($seguro->tipo_cobertura !== 'tope_monto' || ! $this->paciente_id) {
            return null;
        }

        // Excluye la cobertura de ESTA cuenta: al recomputarla en el modelo abierto no debe
        // descontarse de su propio tope disponible (se contaría dos veces).
        $consumido = SeguroCobro::consumoPaciente($this->paciente_id, $seguro->id, (int) now()->year, $this->id);

        return max(0.0, (float) Money::sub($seguro->tope_monto, $consumido));
    }

    // Registrar un pago.
    // Devuelve true si creó el pago, false si fue un replay idempotente (token ya usado).
    public function registrarPago(float $monto, string $metodoPago, ?string $referencia = null, ?int $usuarioId = null, ?string $idempotencyKey = null): bool
    {
        // Idempotencia: si ya existe un pago con este token, es un reintento.
        // No se vuelve a crear ni a re-aplicar totales. El unique index en BD es el
        // respaldo final; este chequeo evita la excepción cuando se tiene el lock de fila.
        if ($idempotencyKey && PagoCuenta::where('idempotency_key', $idempotencyKey)->exists()) {
            return false;
        }

        // Normalizar metodo de pago y validar valores permitidos
        $metodo = strtolower(trim((string) $metodoPago));
        $permitidos = ['efectivo', 'transferencia', 'tarjeta', 'qr'];
        if (!in_array($metodo, $permitidos, true)) {
            // Si viene un valor inesperado, registrar como 'efectivo' por compatibilidad
            $metodo = 'efectivo';
        }

        $pago = $this->pagos()->create([
            'monto' => $monto,
            'metodo_pago' => $metodo,
            'referencia' => $referencia,
            'user_id' => $usuarioId ?? auth()->id(),
            'caja_session_id' => $this->caja_session_id,
            'idempotency_key' => $idempotencyKey,
        ]);

        $this->total_pagado = Money::add($this->total_pagado, $monto);
        $this->recalcularTotales();

        // Cuando este pago deja la cuenta totalmente saldada, los cargos del ciclo
        // pendiente quedan "liquidados" y atados a este pago. Así un cargo agregado
        // o restaurado después se cobra como saldo nuevo y el recibo no re-lista lo
        // ya pagado. En pagos parciales no se liquida nada (todo sigue pendiente).
        if ($this->estado === 'pagado') {
            $this->detalles()
                ->whereNull('liquidado_en')
                ->update([
                    'liquidado_en'      => now(),
                    'liquidado_pago_id' => $pago->id,
                ]);
        }

        return true;
    }

    // Generar número de cuenta correlativo
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cuenta) {
            if (empty($cuenta->id)) {
                $cuenta->id = static::generarNumero();
            }
        });
    }

    /**
     * Siguiente número de cuenta: CTA-AAAA-NNNNNN (p. ej. CTA-2026-000123).
     *
     * El prefijo CTA- (CuentA) identifica la CUENTA por cobrar / episodio del
     * paciente; no confundir con el recibo de pago (PAGO-) ni con otros prefijos
     * del sistema (Receta REC-, Retención RET-, Proforma PRF-).
     *
     * Correlativo incremental por gestión fiscal (año): reinicia en 000001 cada
     * 1° de enero. Un correlativo limpio y sin saltos es lo que exige el control
     * interno (poder auditar comprobantes faltantes). Solo considera los ids con
     * este formato para no inflar el contador con registros de otro esquema.
     *
     * El bucle reasigna si el candidato ya existe (creación seguida en el mismo
     * proceso); ante una colisión concurrente real, el índice único del PK
     * protege la integridad.
     */
    public static function generarNumero(): string
    {
        $prefijo = 'CTA-' . now()->format('Y') . '-';

        do {
            $ultimo = static::where('id', 'REGEXP', '^CTA-[0-9]{4}-[0-9]{6}$')
                ->where('id', 'like', $prefijo . '%')
                ->max(\DB::raw("CAST(SUBSTRING_INDEX(id, '-', -1) AS UNSIGNED)")) ?? 0;

            $numero = $prefijo . str_pad((int) $ultimo + 1, 6, '0', STR_PAD_LEFT);
        } while (static::whereKey($numero)->exists());

        return $numero;
    }
}
