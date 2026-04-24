<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\Paciente;
use App\Models\UtiBed;
use App\Models\UtiAdmission;
use App\Models\Emergency;
use App\Models\Seguro;
use App\Services\NotificationService;

class UtiRecepcionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|reception|dirmedico']);
    }

    /**
     * Vista de ingreso a UTI desde recepción
     */
    public function index(): View
    {
        return view('reception.uti.ingreso');
    }

    /**
     * API: Buscar paciente para ingreso a UTI
     */
    public function buscarPaciente(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ci' => 'required|string|max:20',
        ]);

        $paciente = Paciente::with('seguro')
            ->where('ci', $validated['ci'])
            ->first();

        if (!$paciente) {
            return response()->json([
                'success' => false,
                'message' => 'Paciente no encontrado',
                'existe' => false,
            ]);
        }

        // Verificar si ya está en UTI activo
        $utiActivo = UtiAdmission::where('patient_id', $paciente->ci)
            ->whereIn('estado', ['activo', 'alta_clinica'])
            ->first();

        if ($utiActivo) {
            return response()->json([
                'success' => false,
                'message' => 'El paciente ya tiene un ingreso activo en UTI',
                'existe' => true,
                'uti_activo' => [
                    'id' => $utiActivo->id,
                    'nro_ingreso' => $utiActivo->nro_ingreso,
                    'estado' => $utiActivo->estado,
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'existe' => true,
            'paciente' => [
                'ci' => $paciente->ci,
                'nombre' => $paciente->nombre,
                'sexo' => $paciente->sexo,
                'telefono' => $paciente->telefono,
                'direccion' => $paciente->direccion,
                'seguro' => $paciente->seguro ? [
                    'id' => $paciente->seguro->id,
                    'nombre' => $paciente->seguro->nombre_empresa,
                ] : null,
            ],
        ]);
    }

    /**
     * API: Registrar ingreso a UTI
     */
    public function registrarIngreso(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:pacientes,ci',
            'tipo_ingreso' => 'required|in:emergencia,quirofano,derivacion_interna',
            'tipo_pago' => 'required|in:particular,seguro',
            'seguro_id' => 'nullable|exists:seguros,id',
            'nro_autorizacion' => 'nullable|string|max:50',
            'emergency_id' => 'nullable|exists:emergencies,id',
            'diagnostico_principal' => 'nullable|string|max:500',
            'medico_responsable_ci' => 'nullable|exists:medicos,ci',
        ]);

        DB::beginTransaction();

        try {
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

            // Generar número de ingreso
            $nroIngreso = $this->generarNroIngreso();

            // Crear ingreso UTI
            $admission = UtiAdmission::create([
                'patient_id' => $validated['patient_id'],
                'bed_id' => $cama->id,
                'nro_ingreso' => $nroIngreso,
                'emergency_id' => $validated['emergency_id'] ?? null,
                'estado_clinico' => 'estable',
                'diagnostico_principal' => $validated['diagnostico_principal'] ?? null,
                'tipo_ingreso' => $validated['tipo_ingreso'],
                'tipo_pago' => $validated['tipo_pago'],
                'seguro_id' => $validated['seguro_id'] ?? null,
                'nro_autorizacion' => $validated['nro_autorizacion'] ?? null,
                'fecha_ingreso' => now(),
                'estado' => 'activo',
                'medico_responsable_ci' => $validated['medico_responsable_ci'] ?? null,
            ]);

            // Actualizar cama a ocupada
            $cama->update(['status' => 'ocupada']);

            // Si viene de emergencia, actualizar estado
            if (!empty($validated['emergency_id'])) {
                $emergency = Emergency::find($validated['emergency_id']);
                if ($emergency) {
                    $emergency->update([
                        'status' => 'uti',
                        'ubicacion_actual' => 'uti',
                    ]);
                    $emergency->registrarMovimiento('emergencia', 'uti', 'Derivación a UTI');
                }
            }

            DB::commit();

            // Notificar sobre ingreso a UTI
            $paciente = Paciente::find($validated['patient_id']);
            NotificationService::notifyRole('uti', 'derivacion', 'Nuevo Ingreso UTI', "Paciente: {$paciente->nombre} - Origen: " . ucfirst($validated['tipo_ingreso']), route('uti.operativa.index'), ['admission_id' => $admission->id]);
            NotificationService::notifyAdmins('derivacion', 'Ingreso UTI', "Paciente {$paciente->nombre} ingresado a UTI desde " . ucfirst($validated['tipo_ingreso']), route('uti.operativa.index'));

            return response()->json([
                'success' => true,
                'message' => 'Ingreso a UTI registrado correctamente',
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
                'message' => 'Error al registrar ingreso: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: Obtener camas disponibles
     */
    public function getCamasDisponibles(): JsonResponse
    {
        $camas = UtiBed::where('status', 'disponible')
            ->where('activa', true)
            ->orderBy('bed_number')
            ->get(['id', 'bed_number', 'tipo', 'precio_dia']);

        return response()->json([
            'success' => true,
            'camas' => $camas,
        ]);
    }

    /**
     * API: Obtener emergencias pendientes de derivación a UTI
     */
    public function getEmergenciasPendientes(): JsonResponse
    {
        $emergencias = Emergency::where('ubicacion_actual', 'emergencia')
            ->whereIn('status', ['estabilizado', 'en_evaluacion'])
            ->with(['paciente'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($emg) {
                return [
                    'id' => $emg->id,
                    'code' => $emg->code,
                    'paciente' => [
                        'ci' => $emg->paciente?->ci,
                        'nombre' => $emg->paciente?->nombre,
                    ],
                    'tipo_ingreso' => $emg->tipo_ingreso,
                    'status' => $emg->status,
                    'hora_ingreso' => $emg->created_at->format('H:i'),
                ];
            });

        return response()->json([
            'success' => true,
            'emergencias' => $emergencias,
        ]);
    }

    /**
     * API: Obtener seguros activos
     */
    public function getSeguros(): JsonResponse
    {
        $seguros = Seguro::where('estado', 'activo')
            ->orderBy('nombre_empresa')
            ->get(['id', 'nombre_empresa']);

        return response()->json([
            'success' => true,
            'seguros' => $seguros,
        ]);
    }

    /**
     * Generar número de ingreso UTI
     */
    private function generarNroIngreso(): string
    {
        $prefijo = 'UTI';
        $anio = date('Y');
        $mes = date('m');

        $ultimo = UtiAdmission::whereYear('fecha_ingreso', date('Y'))
            ->whereMonth('fecha_ingreso', date('m'))
            ->count();

        $secuencia = str_pad($ultimo + 1, 4, '0', STR_PAD_LEFT);

        return "{$prefijo}-{$anio}{$mes}-{$secuencia}";
    }
}
