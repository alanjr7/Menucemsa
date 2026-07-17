<?php

namespace App\Http\Controllers;

use App\Models\CirugiaExterna;
use App\Models\CitaQuirurgica;
use App\Models\Quirofano;
use App\Models\TipoCirugia;
use App\Models\TipoCirugiaExterna;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Registro PÚBLICO de cirugías externas (sin login). Un cirujano externo reserva
 * el uso de un quirófano, sube el recibo de pago y la reserva queda "pendiente"
 * para que administración la verifique. Toda entrada es no confiable: se valida
 * en servidor, el precio se recalcula aquí y nada toca el flujo clínico interno.
 */
class CirugiaExternaPublicaController extends Controller
{
    public function create(): View
    {
        $tipos = TipoCirugiaExterna::activos()->orderByDesc('precio')->get();
        $claves = $tipos->pluck('clave')->toArray();
        $quirofanos = Quirofano::where('estado', '!=', 'mantenimiento')
            ->orderBy('id')
            ->get()
            ->filter(function ($q) use ($claves) {
                if (empty($q->restriccion_externa)) {
                    return true;
                }
                return count(array_intersect($q->restriccion_externa, $claves)) > 0;
            })
            ->values();
        $qrUrl = CirugiaExterna::qrPagoUrl();

        return view('cirugias-externas.publico.registrar', compact('tipos', 'quirofanos', 'qrUrl'));
    }

    /** Sirve el QR de pago (público: la página de registro lo muestra). */
    public function qr()
    {
        $disk = Storage::disk('public');
        abort_unless($disk->exists(CirugiaExterna::QR_PAGO_PATH), 404);

        return $disk->response(CirugiaExterna::QR_PAGO_PATH, null, ['Content-Type' => 'image/png'])
            ->setPublic()->setMaxAge(3600);
    }

    /**
     * Ocupación de quirófanos (agenda COMPARTIDA internas + externas) para que el
     * calendario/picker del wizard público replique la disponibilidad real. No
     * expone datos clínicos: las cirugías internas salen como "Ocupado".
     */
    public function agenda(): JsonResponse
    {
        $desde = now()->startOfDay()->toDateString();
        $hasta = now()->addDays(180)->endOfDay()->toDateString();

        $durInternas = TipoCirugia::pluck('duracion_minutos', 'nombre'); // nombre => minutos
        $items = [];

        foreach (CitaQuirurgica::whereBetween('fecha', [$desde, $hasta])->where('estado', '!=', 'cancelada')
                    ->get(['fecha', 'hora_inicio_estimada', 'tipo_cirugia', 'quirofano_id']) as $c) {
            $items[] = [
                'fecha' => $c->fecha->format('Y-m-d'),
                'q' => (int) $c->quirofano_id,
                'ini' => $this->minutosDeHora($c->hora_inicio_estimada),
                'dur' => (int) ($durInternas[$c->tipo_cirugia] ?? 60),
                'label' => 'Ocupado',
                'tipo' => ucfirst((string) $c->tipo_cirugia),
                'categoria' => 'interna',
            ];
        }

        foreach (CirugiaExterna::with('tipo')->whereBetween('fecha', [$desde, $hasta])->where('estado', '!=', 'rechazado')->get() as $e) {
            $primer = trim((string) (explode(' ', trim($e->cirujano_nombre))[0] ?? ''));
            $ini = $this->minutosDeHora($e->hora_inicio);
            $fin = $this->minutosDeHora($e->hora_fin);
            $dur = $fin - $ini;
            $items[] = [
                'fecha' => Carbon::parse($e->fecha)->format('Y-m-d'),
                'q' => (int) $e->quirofano_id,
                'ini' => $ini,
                'dur' => $dur,
                'label' => 'Dr. ' . $primer,
                'tipo' => $e->cirugia_nombre ?: ($e->tipo->nombre ?? ''),
                'categoria' => $e->estado === 'pendiente' ? 'externa_pend' : 'externa_conf',
            ];
        }

        return response()->json($items);
    }

    /** Minutos desde medianoche de un "H:i"/"H:i:s"/Carbon. */
    private function minutosDeHora($hora): int
    {
        if ($hora instanceof Carbon) {
            return $hora->hour * 60 + $hora->minute;
        }
        $p = explode(':', (string) $hora);

        return ((int) $p[0]) * 60 + (int) ($p[1] ?? 0);
    }

    /**
     * Endpoint AJAX del wizard: dado quirófano + tipo + fecha + hora, responde si
     * el horario está libre (agenda compartida con cirugías internas y externas).
     */
    public function disponibilidad(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'quirofano_id' => 'required|exists:quirofanos,id',
            'tipo_cirugia_externa_id' => 'required|exists:tipos_cirugia_externa,id',
            'fecha' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'cirugia_id' => 'nullable|string',
        ]);

        $tipo = TipoCirugiaExterna::findOrFail($validated['tipo_cirugia_externa_id']);
        $quirofano = Quirofano::findOrFail($validated['quirofano_id']);

        $duracionMinutos = $tipo->duracion_minutos;
        if (!empty($validated['cirugia_id'])) {
            $surg = CirugiaExterna::findSurgeryById($validated['cirugia_id']);
            if ($surg) {
                $duracionMinutos = $surg['duracionMin'];
            }
        }
        $horaFin = CirugiaExterna::calcularHoraFin($validated['hora_inicio'], $duracionMinutos);

        $aceptaTipo = $quirofano->aceptaTipoExterno($tipo->clave);
        $solapa = $aceptaTipo && CirugiaExterna::haySolape(
            $quirofano->id,
            $validated['fecha'],
            $validated['hora_inicio'],
            $horaFin
        );

        $precio = CirugiaExterna::calcularPrecio($tipo->precio, $validated['hora_inicio']);

        return response()->json([
            'disponible' => $aceptaTipo && ! $solapa,
            'acepta_tipo' => $aceptaTipo,
            'hora_fin' => $horaFin,
            'precio' => $precio,
            'motivo' => ! $aceptaTipo
                ? 'Este quirófano no acepta ese tipo de cirugía.'
                : ($solapa ? 'Ese horario ya está ocupado. Elija otro.' : null),
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'cirujano_nombre' => 'required|string|max:255',
            'cirujano_telefono' => ['required', 'string', 'regex:/^\d{7,10}$/'],
            'cirujano_email' => 'required|email|max:255',
            'paciente_nombre' => 'required|string|max:255',
            'tipo_cirugia_externa_id' => 'required|exists:tipos_cirugia_externa,id',
            'cirugia_id' => 'nullable|string',
            'quirofano_id' => 'required|exists:quirofanos,id',
            'fecha' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required|date_format:H:i',
            // Fotos de celular pesan varios MB: aceptamos hasta 12 MB.
            'recibo' => 'required|image|max:12288',
        ], [
            'cirujano_telefono.regex' => 'El teléfono debe tener entre 7 y 10 dígitos.',
            'recibo.required' => 'Debe subir la foto del recibo para procesar el pago.',
            'recibo.image' => 'El recibo debe ser una imagen (JPG o PNG).',
            'recibo.max' => 'La imagen del recibo es muy grande (máx. 12 MB).',
            'fecha.after_or_equal' => 'La fecha no puede ser en el pasado.',
        ]);

        $tipo = TipoCirugiaExterna::findOrFail($validated['tipo_cirugia_externa_id']);
        $quirofano = Quirofano::findOrFail($validated['quirofano_id']);

        // Regla de restricción por quirófano (p. ej. Q3 sólo menor/parto).
        if (! $quirofano->aceptaTipoExterno($tipo->clave)) {
            throw ValidationException::withMessages([
                'quirofano_id' => 'El quirófano seleccionado no acepta este tipo de cirugía.',
            ]);
        }

        // Validate surgery_id if provided
        $cirugiaNombre = null;
        $duracionMinutos = $tipo->duracion_minutos;
        if (!empty($validated['cirugia_id'])) {
            $surg = CirugiaExterna::findSurgeryById($validated['cirugia_id']);
            if (!$surg || $surg['tipo'] !== $tipo->clave) {
                throw ValidationException::withMessages([
                    'cirugia_id' => 'La cirugía seleccionada no es válida para este tipo de cirugía.',
                ]);
            }
            $cirugiaNombre = $surg['nombre'];
            $duracionMinutos = $surg['duracionMin'];
        }

        // Precio recalculado en servidor (nunca confiar en el cliente).
        $horaFin = CirugiaExterna::calcularHoraFin($validated['hora_inicio'], $duracionMinutos);
        $precio = CirugiaExterna::calcularPrecio($tipo->precio, $validated['hora_inicio']);

        // Guard de solape en servidor (agenda compartida).
        if (CirugiaExterna::haySolape($quirofano->id, $validated['fecha'], $validated['hora_inicio'], $horaFin)) {
            throw ValidationException::withMessages([
                'hora_inicio' => 'Ese horario ya fue tomado. Vuelva atrás y elija otro.',
            ]);
        }

        $reciboPath = $request->file('recibo')->store('cirugias-externas', 'public');

        $cirugia = CirugiaExterna::crearConCodigo([
            'cirujano_nombre' => $validated['cirujano_nombre'],
            'cirujano_telefono' => $validated['cirujano_telefono'],
            'cirujano_email' => $validated['cirujano_email'],
            'paciente_nombre' => $validated['paciente_nombre'],
            'tipo_cirugia_externa_id' => $tipo->id,
            'cirugia_id' => $validated['cirugia_id'] ?? null,
            'cirugia_nombre' => $cirugiaNombre,
            'quirofano_id' => $quirofano->id,
            'fecha' => $validated['fecha'],
            'hora_inicio' => $validated['hora_inicio'],
            'hora_fin' => $horaFin,
            'precio_base' => $precio['base'],
            'descuento' => $precio['descuento'],
            'precio_final' => $precio['final'],
            'es_nocturno' => $precio['nocturno'],
            'recibo_path' => $reciboPath,
            'estado' => 'pendiente',
        ]);

        // Aviso a administración (campana). notifyAdmins cubre el rol admin;
        // el administrador también gestiona el panel.
        $mensaje = "Cirujano: {$cirugia->cirujano_nombre} · Paciente: {$cirugia->paciente_nombre} · "
            . ($cirugia->cirugia_nombre ?: $tipo->nombre) . " · {$quirofano->nombre} · {$cirugia->fecha->format('d/m/Y')} {$validated['hora_inicio']}";
        NotificationService::notifyAdmins('cirugia', 'Nueva cirugía externa', $mensaje, route('admin.cirugias-externas.index'), ['cirugia_externa_id' => $cirugia->id]);
        NotificationService::notifyRole('administrador', 'cirugia', 'Nueva cirugía externa', $mensaje, route('admin.cirugias-externas.index'), ['cirugia_externa_id' => $cirugia->id]);

        // El wizard envía por fetch y espera JSON; el fallback sin JS redirige.
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'ok' => true,
                'codigo' => $cirugia->codigo,
                'redirect' => route('cirugias-externas.public.gracias', $cirugia->codigo),
            ]);
        }

        return redirect()->route('cirugias-externas.public.gracias', $cirugia->codigo);
    }

    public function gracias(string $codigo): View
    {
        $cirugia = CirugiaExterna::with(['tipo', 'quirofano'])
            ->where('codigo', $codigo)
            ->firstOrFail();

        return view('cirugias-externas.publico.gracias', compact('cirugia'));
    }
}
