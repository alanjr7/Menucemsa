<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hospitalizacion;
use App\Models\Paciente;
use App\Models\UtiAdmission;
use App\Models\UtiBed;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class InternacionStaffController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:internacion|admin|dirmedico');
    }

    public function index(): View
    {
        return view('internacion-staff.dashboard');
    }

    /**
     * API: Get internaciones for dashboard
     */
    public function apiInternaciones(Request $request): JsonResponse
    {
        $query = Hospitalizacion::with(['paciente', 'medico.user', 'medico.especialidad'])
            ->whereNull('fecha_alta')
            ->orderBy('fecha_ingreso', 'desc');

        if ($request->has('estado') && $request->estado !== 'todos') {
            $query->where('estado', $request->estado);
        }

        $internaciones = $query->get()->map(function($hosp) {
            return [
                'id' => $hosp->id,
                'codigo' => $hosp->id,
                'paciente_id' => $hosp->ci_paciente,
                'paciente_nombre' => $hosp->paciente?->nombre ?? 'Desconocido',
                'tipo' => strtolower($hosp->tipo),
                'servicio' => $hosp->servicio,
                'habitacion' => $hosp->habitacion_id ?? 'Por asignar',
                'hora_ingreso' => $hosp->fecha_ingreso?->format('H:i') ?? 'N/A',
                'fecha_ingreso' => $hosp->fecha_ingreso?->format('d/m/Y') ?? 'N/A',
                'estado' => $hosp->estado ?? 'activo',
                'medico' => $hosp->medico?->user?->name ?? 'No asignado',
                'diagnostico' => $hosp->diagnostico,
            ];
        });

        $stats = [
            'activos' => Hospitalizacion::whereNull('fecha_alta')->count(),
            'espera' => Hospitalizacion::whereNull('fecha_alta')->whereNull('habitacion_id')->count(),
            'atencion' => Hospitalizacion::whereNull('fecha_alta')->whereNotNull('habitacion_id')->count(),
            'hoy' => Hospitalizacion::whereDate('fecha_ingreso', today())->count(),
        ];

        return response()->json([
            'success' => true,
            'internaciones' => $internaciones,
            'stats' => $stats
        ]);
    }

    /**
     * API: Get statistics
     */
    public function apiEstadisticas(): JsonResponse
    {
        $stats = [
            'activos' => Hospitalizacion::whereNull('fecha_alta')->count(),
            'espera' => Hospitalizacion::whereNull('fecha_alta')->whereNull('habitacion_id')->count(),
            'atencion' => Hospitalizacion::whereNull('fecha_alta')->whereNotNull('habitacion_id')->count(),
            'hoy' => Hospitalizacion::whereDate('fecha_ingreso', today())->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * API: Update status
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $hospitalizacion = Hospitalizacion::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:activo,en_observacion,estable,crítico,alta,trasladado',
        ]);

        $hospitalizacion->update(['estado' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente',
            'status' => $hospitalizacion->estado,
        ]);
    }

    /**
     * API: Derivar a UTI
     */
    public function derivarAUti(Request $request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $hospitalizacion = Hospitalizacion::with('paciente')->findOrFail($id);

            // Verificar si ya está en UTI
            $utiActivo = UtiAdmission::where('patient_id', $hospitalizacion->ci_paciente)
                ->whereIn('estado', ['activo', 'alta_clinica'])
                ->first();

            if ($utiActivo) {
                return response()->json([
                    'success' => false,
                    'message' => 'El paciente ya tiene un ingreso activo en UTI',
                ], 422);
            }

            // Buscar cama disponible
            $cama = UtiBed::where('status', 'disponible')
                ->where('activa', true)
                ->first();

            if (!$cama) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay camas disponibles en UTI',
                ], 422);
            }

            // Generar número de ingreso UTI
            $prefijo = 'UTI';
            $anio = date('Y');
            $mes = date('m');
            $ultimo = UtiAdmission::whereYear('fecha_ingreso', date('Y'))
                ->whereMonth('fecha_ingreso', date('m'))
                ->count();
            $secuencia = str_pad($ultimo + 1, 4, '0', STR_PAD_LEFT);
            $nroIngreso = "{$prefijo}-{$anio}{$mes}-{$secuencia}";

            // Crear ingreso UTI
            $admission = UtiAdmission::create([
                'patient_id' => $hospitalizacion->ci_paciente,
                'bed_id' => $cama->id,
                'nro_ingreso' => $nroIngreso,
                'hospitalization_id' => $hospitalizacion->id,
                'estado_clinico' => 'estable',
                'diagnostico_principal' => $hospitalizacion->diagnostico,
                'tipo_ingreso' => 'derivacion_interna',
                'tipo_pago' => 'particular',
                'fecha_ingreso' => now(),
                'estado' => 'activo',
                'medico_responsable_id' => $hospitalizacion->ci_medico,
            ]);

            // Actualizar cama a ocupada
            $cama->update(['status' => 'ocupada']);

            // Actualizar hospitalización
            $hospitalizacion->update([
                'estado' => 'trasladado',
                'observaciones' => ($hospitalizacion->observaciones ? $hospitalizacion->observaciones . "\n" : '') .
                    'Trasladado a UTI: ' . now()->format('d/m/Y H:i'),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Paciente derivado a UTI correctamente',
                'admission' => [
                    'id' => $admission->id,
                    'nro_ingreso' => $admission->nro_ingreso,
                    'cama' => $cama->bed_number,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al derivar a UTI: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: Dar alta
     */
    public function darAlta(Request $request, $id): JsonResponse
    {
        try {
            $hospitalizacion = Hospitalizacion::findOrFail($id);

            if ($hospitalizacion->fecha_alta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este paciente ya fue dado de alta'
                ], 422);
            }

            $validated = $request->validate([
                'motivo_alta' => 'nullable|string|max:500',
            ]);

            $hospitalizacion->update([
                'fecha_alta' => now(),
                'motivo_alta' => $validated['motivo_alta'] ?? 'Alta médica',
                'estado' => 'alta',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Paciente dado de alta exitosamente',
                'hospitalizacion' => $hospitalizacion
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al dar de alta: ' . $e->getMessage()
            ], 500);
        }
    }
}
