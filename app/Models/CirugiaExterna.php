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
        'cirugia_id',
        'cirugia_nombre',
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

    public const CIRUGIAS = [
        // --- MENOR ---
        ['id' => 'extirpacion_lipoma', 'especialidad' => 'Cirugía General', 'nombre' => 'Extirpación de lipoma', 'tipo' => 'menor', 'duracionMin' => 45],
        ['id' => 'extirpacion_quiste_sebaceo', 'especialidad' => 'Cirugía General', 'nombre' => 'Extirpación de quiste sebáceo', 'tipo' => 'menor', 'duracionMin' => 30],
        ['id' => 'hemorroidectomia', 'especialidad' => 'Cirugía General', 'nombre' => 'Hemorroidectomía', 'tipo' => 'menor', 'duracionMin' => 60],
        ['id' => 'fistulectomia_anal', 'especialidad' => 'Cirugía General', 'nombre' => 'Fistulectomía anal', 'tipo' => 'menor', 'duracionMin' => 60],
        ['id' => 'fisurectomia', 'especialidad' => 'Cirugía General', 'nombre' => 'Fisurectomía', 'tipo' => 'menor', 'duracionMin' => 45],
        ['id' => 'legrado_uterino', 'especialidad' => 'Ginecología y Obstetricia', 'nombre' => 'Legrado uterino', 'tipo' => 'menor', 'duracionMin' => 30],
        ['id' => 'conizacion_cervical', 'especialidad' => 'Ginecología y Obstetricia', 'nombre' => 'Conización cervical', 'tipo' => 'menor', 'duracionMin' => 45],
        ['id' => 'biopsia_cuello_uterino', 'especialidad' => 'Ginecología y Obstetricia', 'nombre' => 'Biopsia de cuello uterino', 'tipo' => 'menor', 'duracionMin' => 30],
        ['id' => 'retiro_material_osteosintesis', 'especialidad' => 'Traumatología y Ortopedia', 'nombre' => 'Retiro de material de osteosíntesis', 'tipo' => 'menor', 'duracionMin' => 45],
        ['id' => 'liberacion_tunel_carpiano', 'especialidad' => 'Traumatología y Ortopedia', 'nombre' => 'Liberación túnel carpiano', 'tipo' => 'menor', 'duracionMin' => 45],
        ['id' => 'circuncision', 'especialidad' => 'Urología', 'nombre' => 'Circuncisión', 'tipo' => 'menor', 'duracionMin' => 30],
        ['id' => 'vasectomia', 'especialidad' => 'Urología', 'nombre' => 'Vasectomía', 'tipo' => 'menor', 'duracionMin' => 30],
        ['id' => 'colocacion_tubos_ventilacion', 'especialidad' => 'Otorrinolaringología', 'nombre' => 'Colocación tubos ventilación', 'tipo' => 'menor', 'duracionMin' => 30],
        ['id' => 'pterigion', 'especialidad' => 'Oftalmología', 'nombre' => 'Pterigión', 'tipo' => 'menor', 'duracionMin' => 30],
        ['id' => 'chalazion', 'especialidad' => 'Oftalmología', 'nombre' => 'Chalazión', 'tipo' => 'menor', 'duracionMin' => 20],
        ['id' => 'blefaroplastia', 'especialidad' => 'Cirugía Plástica', 'nombre' => 'Blefaroplastia', 'tipo' => 'menor', 'duracionMin' => 60],
        ['id' => 'otoplastia', 'especialidad' => 'Cirugía Plástica', 'nombre' => 'Otoplastia', 'tipo' => 'menor', 'duracionMin' => 90],
        ['id' => 'hernia_pediatrica', 'especialidad' => 'Cirugía Pediátrica', 'nombre' => 'Hernia umbilical pediátrica', 'tipo' => 'menor', 'duracionMin' => 45],

        // --- MEDIANA ---
        ['id' => 'apendicectomia', 'especialidad' => 'Cirugía General', 'nombre' => 'Apendicectomía', 'tipo' => 'mediana', 'duracionMin' => 90],
        ['id' => 'hernioplastia_inguinal', 'especialidad' => 'Cirugía General', 'nombre' => 'Hernioplastia inguinal', 'tipo' => 'mediana', 'duracionMin' => 90],
        ['id' => 'hernioplastia_umbilical', 'especialidad' => 'Cirugía General', 'nombre' => 'Hernioplastia umbilical', 'tipo' => 'mediana', 'duracionMin' => 60],
        ['id' => 'salpingectomia', 'especialidad' => 'Ginecología y Obstetricia', 'nombre' => 'Salpingectomía', 'tipo' => 'mediana', 'duracionMin' => 90],
        ['id' => 'artroscopia_rodilla', 'especialidad' => 'Traumatología y Ortopedia', 'nombre' => 'Artroscopia de rodilla', 'tipo' => 'mediana', 'duracionMin' => 90],
        ['id' => 'reduccion_osteosintesis_radio', 'especialidad' => 'Traumatología y Ortopedia', 'nombre' => 'Reducción y osteosíntesis de radio', 'tipo' => 'mediana', 'duracionMin' => 120],
        ['id' => 'ureteroscopia', 'especialidad' => 'Urología', 'nombre' => 'Ureteroscopia', 'tipo' => 'mediana', 'duracionMin' => 90],
        ['id' => 'litotricia_endoscopica', 'especialidad' => 'Urología', 'nombre' => 'Litotricia endoscópica', 'tipo' => 'mediana', 'duracionMin' => 120],
        ['id' => 'amigdalectomia', 'especialidad' => 'Otorrinolaringología', 'nombre' => 'Amigdalectomía', 'tipo' => 'mediana', 'duracionMin' => 60],
        ['id' => 'septoplastia', 'especialidad' => 'Otorrinolaringología', 'nombre' => 'Septoplastia', 'tipo' => 'mediana', 'duracionMin' => 90],
        ['id' => 'catarata', 'especialidad' => 'Oftalmología', 'nombre' => 'Catarata', 'tipo' => 'mediana', 'duracionMin' => 45],
        ['id' => 'safenectomia', 'especialidad' => 'Cirugía Vascular', 'nombre' => 'Safenectomía', 'tipo' => 'mediana', 'duracionMin' => 90],
        ['id' => 'fistula', 'especialidad' => 'Cirugía Vascular', 'nombre' => 'Fístula arteriovenosa', 'tipo' => 'mediana', 'duracionMin' => 120],
        ['id' => 'orquidopexia', 'especialidad' => 'Cirugía Pediátrica', 'nombre' => 'Orquidopexia', 'tipo' => 'mediana', 'duracionMin' => 60],

        // --- MAYOR ---
        ['id' => 'colecistectomia_laparoscopica', 'especialidad' => 'Cirugía General', 'nombre' => 'Colecistectomía laparoscópica', 'tipo' => 'mayor', 'duracionMin' => 120],
        ['id' => 'colectomia', 'especialidad' => 'Cirugía General', 'nombre' => 'Colectomía', 'tipo' => 'mayor', 'duracionMin' => 240],
        ['id' => 'gastrectomia', 'especialidad' => 'Cirugía General', 'nombre' => 'Gastrectomía', 'tipo' => 'mayor', 'duracionMin' => 240],
        ['id' => 'laparotomia_exploradora', 'especialidad' => 'Cirugía General', 'nombre' => 'Laparotomía exploradora', 'tipo' => 'mayor', 'duracionMin' => 180],
        ['id' => 'miomectomia', 'especialidad' => 'Ginecología y Obstetricia', 'nombre' => 'Miomectomía', 'tipo' => 'mayor', 'duracionMin' => 180],
        ['id' => 'histerectomia_abdominal', 'especialidad' => 'Ginecología y Obstetricia', 'nombre' => 'Histerectomía abdominal', 'tipo' => 'mayor', 'duracionMin' => 180],
        ['id' => 'cesarea', 'especialidad' => 'Ginecología y Obstetricia', 'nombre' => 'Cesárea', 'tipo' => 'mayor', 'duracionMin' => 90],
        ['id' => 'reconstruccion_lca', 'especialidad' => 'Traumatología y Ortopedia', 'nombre' => 'Reconstrucción LCA', 'tipo' => 'mayor', 'duracionMin' => 180],
        ['id' => 'protesis_total_cadera', 'especialidad' => 'Traumatología y Ortopedia', 'nombre' => 'Prótesis total de cadera', 'tipo' => 'mayor', 'duracionMin' => 240],
        ['id' => 'protesis_total_rodilla', 'especialidad' => 'Traumatología y Ortopedia', 'nombre' => 'Prótesis total de rodilla', 'tipo' => 'mayor', 'duracionMin' => 240],
        ['id' => 'nefrectomia', 'especialidad' => 'Urología', 'nombre' => 'Nefrectomía', 'tipo' => 'mayor', 'duracionMin' => 180],
        ['id' => 'prostatectomia_radical', 'especialidad' => 'Urología', 'nombre' => 'Prostatectomía radical', 'tipo' => 'mayor', 'duracionMin' => 240],
        ['id' => 'timpanoplastia', 'especialidad' => 'Otorrinolaringología', 'nombre' => 'Timpanoplastia', 'tipo' => 'mayor', 'duracionMin' => 180],
        ['id' => 'vitrectomia', 'especialidad' => 'Oftalmología', 'nombre' => 'Vitrectomía', 'tipo' => 'mayor', 'duracionMin' => 180],
        ['id' => 'mamoplastia', 'especialidad' => 'Cirugía Plástica', 'nombre' => 'Mamoplastia', 'tipo' => 'mayor', 'duracionMin' => 180],
        ['id' => 'abdominoplastia', 'especialidad' => 'Cirugía Plástica', 'nombre' => 'Abdominoplastia', 'tipo' => 'mayor', 'duracionMin' => 240],
        ['id' => 'liposuccion', 'especialidad' => 'Cirugía Plástica', 'nombre' => 'Liposucción', 'tipo' => 'mayor', 'duracionMin' => 180],
        ['id' => 'derivacion', 'especialidad' => 'Neurocirugía', 'nombre' => 'Derivación ventricular', 'tipo' => 'mayor', 'duracionMin' => 180],
        ['id' => 'craneotomia', 'especialidad' => 'Neurocirugía', 'nombre' => 'Craneotomía', 'tipo' => 'mayor', 'duracionMin' => 300],
        ['id' => 'artrodesis', 'especialidad' => 'Neurocirugía', 'nombre' => 'Artrodesis lumbar', 'tipo' => 'mayor', 'duracionMin' => 300],
        ['id' => 'bypass', 'especialidad' => 'Cirugía Vascular', 'nombre' => 'Bypass femoropoplíteo', 'tipo' => 'mayor', 'duracionMin' => 240],
        ['id' => 'atresia_intestinal', 'especialidad' => 'Cirugía Pediátrica', 'nombre' => 'Atresia intestinal', 'tipo' => 'mayor', 'duracionMin' => 240],
    ];

    public static function findSurgeryById(string $id): ?array
    {
        foreach (self::CIRUGIAS as $surg) {
            if ($surg['id'] === $id) {
                return $surg;
            }
        }
        return null;
    }

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
