<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * Proforma = cotización / presupuesto pre-admisión que se entrega al paciente.
 *
 * Documento informativo (estimado), NO un cargo real: no impacta CuentaCobro,
 * caja ni contabilidad. La identidad del receptor es texto libre.
 */
class Proforma extends Model
{
    protected $fillable = [
        'numero',
        'paciente_nombre',
        'paciente_documento',
        'paciente_telefono',
        'validez_dias',
        'descuento',
        'total',
        'observaciones',
        'user_id',
    ];

    protected $casts = [
        'validez_dias' => 'integer',
        'descuento'    => 'decimal:2',
        'total'        => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Proforma $proforma) {
            if (empty($proforma->numero)) {
                $proforma->numero = static::generarNumero();
            }
        });
    }

    /**
     * Siguiente número correlativo: PRF-AAAA-NNNNNN (p. ej. PRF-2026-000123).
     *
     * Reinicia cada gestión (año). Espejo de CuentaCobro::generarNumero: el
     * bucle reasigna ante colisiones del mismo proceso y el índice único del
     * campo protege la integridad ante concurrencia real.
     */
    public static function generarNumero(): string
    {
        $prefijo = 'PRF-' . now()->format('Y') . '-';

        do {
            $ultimo = static::where('numero', 'REGEXP', '^PRF-[0-9]{4}-[0-9]{6}$')
                ->where('numero', 'like', $prefijo . '%')
                ->max(DB::raw("CAST(SUBSTRING_INDEX(numero, '-', -1) AS UNSIGNED)")) ?? 0;

            $numero = $prefijo . str_pad((int) $ultimo + 1, 6, '0', STR_PAD_LEFT);
        } while (static::where('numero', $numero)->exists());

        return $numero;
    }

    // ── Relaciones ─────────────────────────────────────────────────────────
    public function items(): HasMany
    {
        return $this->hasMany(ProformaItem::class)->orderBy('orden');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Visibilidad ────────────────────────────────────────────────────────
    /**
     * admin y administrador ven TODAS; el resto solo las propias.
     */
    public function scopeVisiblesPara(Builder $query, User $user): Builder
    {
        if (self::esGestor($user)) {
            return $query;
        }

        return $query->where('user_id', $user->id);
    }

    public function puedeVerla(User $user): bool
    {
        return self::esGestor($user) || $this->user_id === $user->id;
    }

    /** Roles con visibilidad global sobre las proformas. */
    public static function esGestor(User $user): bool
    {
        return $user->isAdmin() || $user->role === 'administrador';
    }

    // ── Derivados ──────────────────────────────────────────────────────────
    public function getFechaVencimientoAttribute()
    {
        return $this->created_at?->copy()->addDays($this->validez_dias);
    }
}
