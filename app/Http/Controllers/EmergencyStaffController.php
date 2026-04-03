<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Emergency;
use App\Models\Paciente;
use App\Models\Quirofano;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class EmergencyStaffController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:emergencia|admin|dirmedico');
    }

    public function index(): View
    {
        return view('emergency-staff.dashboard');
    }

    /**
     * API: Get emergencies for dashboard
     */
    public function apiEmergencias(Request $request): JsonResponse
    {
        $query = Emergency::with(['paciente'])
            ->where('ubicacion_actual', 'emergencia')
            ->whereIn('status', ['recibido', 'en_evaluacion', 'estabilizado'])
            ->orderBy('created_at', 'desc');

        if ($request->has('estado') && $request->estado !== 'todos') {
            $query->where('status', $request->estado);
        }

        $emergencias = $query->get()->map(function($emg) {
            return [
                'id' => $emg->id,
                'code' => $emg->code,
                'paciente_id' => $emg->patient_id,
                'paciente_nombre' => $emg->is_temp_id ? 'Paciente Temporal' : ($emg->paciente?->nombre ?? 'Desconocido'),
                'is_temp_id' => $emg->is_temp_id,
                'tipo_ingreso' => $emg->tipo_ingreso,
                'tipo_ingreso_label' => $emg->tipo_ingreso_label,
                'destino_inicial' => $emg->destino_inicial,
                'hora_ingreso' => $emg->admission_date?->format('H:i') ?? $emg->created_at->format('H:i'),
                'status' => $emg->status,
                'status_label' => $this->getStatusLabel($emg->status),
            ];
        });

        $stats = [
            'activos' => Emergency::where('ubicacion_actual', 'emergencia')->whereIn('status', ['recibido', 'en_evaluacion', 'estabilizado'])->count(),
            'espera' => Emergency::where('status', 'recibido')->where('ubicacion_actual', 'emergencia')->count(),
            'atencion' => Emergency::where('status', 'en_evaluacion')->where('ubicacion_actual', 'emergencia')->count(),
            'hoy' => Emergency::whereDate('created_at', today())->count(),
        ];

        return response()->json([
            'success' => true,
            'emergencias' => $emergencias,
            'stats' => $stats
        ]);
    }

    /**
     * API: Get statistics
     */
    public function apiEstadisticas(): JsonResponse
    {
        $stats = [
            'activos' => Emergency::where('ubicacion_actual', 'emergencia')->whereIn('status', ['recibido', 'en_evaluacion', 'estabilizado'])->count(),
            'espera' => Emergency::where('status', 'recibido')->where('ubicacion_actual', 'emergencia')->count(),
            'atencion' => Emergency::where('status', 'en_evaluacion')->where('ubicacion_actual', 'emergencia')->count(),
            'hoy' => Emergency::whereDate('created_at', today())->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * API: Update status
     */
    public function updateStatus(Request $request, Emergency $emergency): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:recibido,en_evaluacion,estabilizado,uti,cirugia,alta,fallecido',
        ]);

        $estadoAnterior = $emergency->status;
        $emergency->update(['status' => $validated['status']]);

        // Registrar en flujo historial
        $emergency->registrarMovimiento('emergencia', 'emergencia', 'Cambio de estado: ' . $estadoAnterior . ' → ' . $validated['status']);

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente',
            'status' => $emergency->status,
            'status_color' => $emergency->status_color,
        ]);
    }

    /**
     * API: Derivar a otro módulo
     */
    public function derivar(Request $request, Emergency $emergency): JsonResponse
    {
        $validated = $request->validate([
            'destino' => 'required|in:cirugia,uti,hospitalizacion,observacion,alta',
            'forzar' => 'nullable|boolean',
        ]);

        $destino = $validated['destino'];
        $forzar = $request->boolean('forzar');

        // Validar disponibilidad de recursos
        if (!$forzar) {
            $validacion = $this->validarRecursos($destino);
            if (!$validacion['disponible']) {
                return response()->json([
                    'success' => false,
                    'requiere_confirmacion' => true,
                    'message' => $validacion['mensaje'],
                ]);
            }
        }

        $ubicacionAnterior = $emergency->ubicacion_actual;
        $statusAnterior = $emergency->status;

        // Actualizar según destino
        switch ($destino) {
            case 'cirugia':
                $emergency->update([
                    'status' => 'cirugia',
                    'ubicacion_actual' => 'cirugia',
                    'nro_cirugia' => $this->generarNroCirugia($emergency),
                ]);
                break;
            case 'uti':
                $emergency->update([
                    'status' => 'uti',
                    'ubicacion_actual' => 'uti',
                    'nro_uti' => $this->generarNroUti($emergency),
                ]);
                break;
            case 'hospitalizacion':
                $emergency->update([
                    'status' => 'hospitalizacion',
                    'ubicacion_actual' => 'hospitalizacion',
                    'nro_hospitalizacion' => $this->generarNroHospitalizacion($emergency),
                ]);
                break;
            case 'observacion':
                $emergency->update([
                    'status' => 'estabilizado',
                    'ubicacion_actual' => 'observacion',
                ]);
                break;
            case 'alta':
                $emergency->update([
                    'status' => 'alta',
                    'ubicacion_actual' => 'alta',
                    'discharge_date' => now(),
                ]);
                break;
        }

        // Registrar movimiento
        $emergency->registrarMovimiento($ubicacionAnterior, $destino, 'Derivación desde emergencia');

        return response()->json([
            'success' => true,
            'message' => 'Paciente derivado correctamente a ' . $destino,
        ]);
    }

    /**
     * API: Dar de alta
     */
    public function darAlta(Emergency $emergency): JsonResponse
    {
        $ubicacionAnterior = $emergency->ubicacion_actual;

        $emergency->update([
            'status' => 'alta',
            'ubicacion_actual' => 'alta',
            'discharge_date' => now(),
        ]);

        $emergency->registrarMovimiento($ubicacionAnterior, 'alta', 'Paciente dado de alta');

        return response()->json([
            'success' => true,
            'message' => 'Paciente dado de alta correctamente',
        ]);
    }

    /**
     * Validar disponibilidad de recursos
     */
    private function validarRecursos(string $destino): array
    {
        switch ($destino) {
            case 'cirugia':
                $quirofanosDisponibles = Quirofano::where('estado', 'disponible')->count();
                if ($quirofanosDisponibles === 0) {
                    return [
                        'disponible' => false,
                        'mensaje' => 'No hay quirófanos disponibles en este momento.'
                    ];
                }
                return ['disponible' => true];

            case 'uti':
                // Verificar camas UTI disponibles
                $camasUti = DB::table('camas')->where('area', 'UTI')->where('estado', 'disponible')->count();
                if ($camasUti === 0) {
                    return [
                        'disponible' => false,
                        'mensaje' => 'No hay camas disponibles en UTI.'
                    ];
                }
                return ['disponible' => true];

            case 'hospitalizacion':
                $camasHosp = DB::table('camas')->where('area', 'hospitalizacion')->where('estado', 'disponible')->count();
                if ($camasHosp === 0) {
                    return [
                        'disponible' => false,
                        'mensaje' => 'No hay camas disponibles en hospitalización.'
                    ];
                }
                return ['disponible' => true];

            default:
                return ['disponible' => true];
        }
    }

    /**
     * Generar número de cirugía
     */
    private function generarNroCirugia(Emergency $emergency): string
    {
        return 'CIR-' . now()->format('Ymd') . '-' . str_pad($emergency->id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generar número de UTI
     */
    private function generarNroUti(Emergency $emergency): string
    {
        return 'UTI-' . now()->format('Ymd') . '-' . str_pad($emergency->id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generar número de hospitalización
     */
    private function generarNroHospitalizacion(Emergency $emergency): string
    {
        return 'HOSP-' . now()->format('Ymd') . '-' . str_pad($emergency->id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get status label
     */
    private function getStatusLabel(string $status): string
    {
        return match($status) {
            'recibido' => 'Recibido',
            'en_evaluacion' => 'En Evaluación',
            'estabilizado' => 'Estabilizado',
            'cirugia' => 'En Cirugía',
            'uti' => 'En UTI',
            'alta' => 'Dado de Alta',
            'fallecido' => 'Fallecido',
            default => $status,
        };
    }

    public function create(): View
    {
        return view('emergency-staff.create');
    }

    public function show(Emergency $emergency): View
    {
        return view('emergency-staff.show', compact('emergency'));
    }

    public function edit(Emergency $emergency): View
    {
        return view('emergency-staff.edit', compact('emergency'));
    }

    public function pending(): View
    {
        return view('emergency-staff.pending');
    }
}
