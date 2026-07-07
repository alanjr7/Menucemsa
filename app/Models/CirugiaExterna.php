<?php

namespace App\Models;

use App\Support\Money;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Reserva de quirófano hecha por un cirujano externo desde la ruta pública.
 * Aggregate aislado del flujo clínico ({@see CitaQuirurgica}): entrada no
 * confiable, prepago con recibo, verificada luego por administración.
 */
class CirugiaExterna extends Model
{
    use HasFactory;

    protected $table = 'cirugias_externas';

    protected $fillable = [
        'codigo',
        'cirujano_nombre',
        'cirujano_telefono',
        'cirujano_email',
        'paciente_nombre',
        'tipo_cirugia_externa_id',
        'quirofano_id',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'precio_base',
        'descuento',
        'precio_final',
        'es_nocturno',
        'recibo_path',
        'estado',
        'motivo_rechazo',
        'verificado_por',
        'verificado_at',
    ];

    protected $casts = [
        'fecha' => 'date',
        'precio_base' => 'decimal:2',
        'descuento' => 'decimal:2',
        'precio_final' => 'decimal:2',
        'es_nocturno' => 'boolean',
        'verificado_at' => 'datetime',
    ];

    /** Descuento por horario nocturno (10%) y hora de corte (< 06:00). */
    public const DESCUENTO_NOCTURNO = '0.10';
    public const NOCTURNO_FIN_HORA = 6;

    /** Ruta ÚNICA del QR de pago (disco public). Se sobreescribe al subir uno nuevo. */
    public const QR_PAGO_PATH = 'cirugias-externas/qr-pago.png';

    // ---------------------------------------------------------------------
    // Relaciones
    // ---------------------------------------------------------------------
    public function tipo()
    {
        return $this->belongsTo(TipoCirugiaExterna::class, 'tipo_cirugia_externa_id');
    }

    public function quirofano()
    {
        return $this->belongsTo(Quirofano::class, 'quirofano_id');
    }

    public function verificador()
    {
        return $this->belongsTo(User::class, 'verificado_por');
    }

    // ---------------------------------------------------------------------
    // Código correlativo (mismo patrón que Emergency/Consulta::crearConCodigo)
    // ---------------------------------------------------------------------

    /**
     * CIREXT-Ymd-NNNN. Correlativo global incremental desde 5001, filtrado por
     * REGEXP para no contaminar el contador. Sólo cálculo; persistir con
     * {@see crearConCodigo()}.
     */
    public static function generarCodigo(): string
    {
        $last = static::where('codigo', 'REGEXP', '^CIREXT-[0-9]{8}-[0-9]+$')
            ->max(DB::raw("CAST(SUBSTRING_INDEX(codigo, '-', -1) AS UNSIGNED)")) ?? 0;
        $siguiente = max((int) $last, 5000) + 1;

        return 'CIREXT-' . now()->format('Ymd') . '-' . str_pad($siguiente, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Crea la reserva asignando un `codigo` único, reintentando ante colisión
     * concurrente (unique index) en vez de un 500. Única vía de creación.
     *
     * @param array $attributes Sin `codigo`.
     */
    public static function crearConCodigo(array $attributes): self
    {
        for ($intento = 0; $intento < 5; $intento++) {
            $attributes['codigo'] = static::generarCodigo();

            try {
                return static::create($attributes);
            } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                // Colisión por concurrencia: reintentar con el siguiente correlativo.
            }
        }

        throw new \RuntimeException('No se pudo generar un código único de cirugía externa tras varios intentos.');
    }

    // ---------------------------------------------------------------------
    // Reglas de negocio (fuente única, con BCMath/Money)
    // ---------------------------------------------------------------------

    /** ¿La hora de inicio cae en el rango nocturno [00:00, 06:00)? */
    public static function esHorarioNocturno(string $horaInicio): bool
    {
        $hora = (int) explode(':', $horaInicio)[0];

        return $hora >= 0 && $hora < self::NOCTURNO_FIN_HORA;
    }

    /**
     * Precio de la reserva con descuento nocturno del 10% si aplica.
     * Fuente ÚNICA de la fórmula (usada por el store público y la vista).
     *
     * @return array{base:string, descuento:string, final:string, nocturno:bool}
     */
    public static function calcularPrecio($precioBase, string $horaInicio): array
    {
        $base = Money::format($precioBase);
        $nocturno = self::esHorarioNocturno($horaInicio);
        $descuento = $nocturno ? Money::mul($base, self::DESCUENTO_NOCTURNO) : Money::format('0');
        $final = Money::sub($base, $descuento);

        return ['base' => $base, 'descuento' => $descuento, 'final' => $final, 'nocturno' => $nocturno];
    }

    /**
     * Hora de fin = inicio + duración (minutos). Devuelve "H:i".
     */
    public static function calcularHoraFin(string $horaInicio, int $duracionMinutos): string
    {
        return static::parseHora($horaInicio)->addMinutes($duracionMinutos)->format('H:i');
    }

    /**
     * ¿Se solapa esta reserva con otra ocupación del MISMO quirófano ese día?
     * Agenda COMPARTIDA: considera tanto cirugías internas ({@see CitaQuirurgica}
     * no canceladas) como otras reservas externas (no rechazadas). Fuente única
     * usada por el endpoint AJAX y por el guard de servidor del store.
     */
    public static function haySolape(int $quirofanoId, string $fecha, string $horaInicio, string $horaFin, ?int $ignorarId = null): bool
    {
        $ini = static::parseHora($horaInicio);
        $fin = static::parseHora($horaFin);

        // 1) Cirugías internas programadas/en curso/finalizadas (no canceladas).
        $internas = CitaQuirurgica::where('quirofano_id', $quirofanoId)
            ->whereDate('fecha', $fecha)
            ->where('estado', '!=', 'cancelada')
            ->get();

        foreach ($internas as $cita) {
            $cIni = static::parseHora($cita->hora_inicio_estimada);
            $cFin = $cIni->copy()->addMinutes($cita->duracion_estimada);
            if ($ini < $cFin && $fin > $cIni) {
                return true;
            }
        }

        // 2) Otras reservas externas (no rechazadas).
        $externas = static::where('quirofano_id', $quirofanoId)
            ->whereDate('fecha', $fecha)
            ->where('estado', '!=', 'rechazado')
            ->when($ignorarId, fn ($q) => $q->where('id', '!=', $ignorarId))
            ->get();

        foreach ($externas as $reserva) {
            $rIni = static::parseHora($reserva->hora_inicio);
            $rFin = static::parseHora($reserva->hora_fin);
            if ($ini < $rFin && $fin > $rIni) {
                return true;
            }
        }

        return false;
    }

    /** Normaliza "H:i" / "H:i:s" / Carbon a un Carbon del mismo día. */
    private static function parseHora($hora): Carbon
    {
        if ($hora instanceof Carbon) {
            return $hora->copy();
        }
        $parts = explode(':', (string) $hora);

        return Carbon::createFromTime((int) $parts[0], (int) ($parts[1] ?? 0));
    }

    // ---------------------------------------------------------------------
    // Accessors / helpers de presentación
    // ---------------------------------------------------------------------

    /**
     * URL para ver el recibo. Apunta a una ruta del controlador que lee el
     * archivo del disco local (NO a /storage vía symlink), por lo que funciona
     * en cualquier despliegue (Windows/VM/cPanel) y con URL relativa.
     */
    public function getReciboUrlAttribute(): ?string
    {
        return $this->recibo_path ? route('admin.cirugias-externas.recibo', $this) : null;
    }

    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    /**
     * URL del QR de pago con cache-busting (o null si no hay uno cargado).
     * Fuente única usada por la página pública y el panel admin.
     */
    public static function qrPagoUrl(): ?string
    {
        $disk = Storage::disk('public');
        if (! $disk->exists(self::QR_PAGO_PATH)) {
            return null;
        }

        return route('cirugias-externas.public.qr') . '?v=' . $disk->lastModified(self::QR_PAGO_PATH);
    }
}
