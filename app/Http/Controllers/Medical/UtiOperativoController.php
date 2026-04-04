<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\UtiBed;
use App\Models\UtiAdmission;
use App\Models\UtiVitalSign;
use App\Models\UtiDailyRecord;
use App\Models\UtiMedication;
use App\Models\UtiSupply;
use App\Models\UtiCatering;
use App\Models\UtiRecipe;
use App\Models\UtiRecipeDetail;
use App\Models\Paciente;
use App\Models\Medicamentos;
use App\Models\Insumos;
use App\Models\Medico;
use App\Services\CuentaCobroService;

class UtiOperativoController extends Controller
{
    protected $cuentaCobroService;

    public function __construct(CuentaCobroService $cuentaCobroService)
    {
        $this->cuentaCobroService = $cuentaCobroService;
        $this->middleware(['auth', 'role:admin|dirmedico|doctor|enfermeria|uti']);
    }

    /**
     * Vista principal del panel operativo UTI
     */
    public function index(): View
    {
        return view('uti.operativo.dashboard');
    }

    /**
     * API: Obtener lista de pacientes en UTI
     */
    public function getPacientesUti(Request $request): JsonResponse
    {
        $query = UtiAdmission::with(['paciente', 'bed', 'medico', 'vitalSigns' => function($q) {
                $q->latest()->limit(1);
            }, 'dailyRecords' => function($q) {
                $q->where('fecha', today())->limit(1);
            }])
            ->whereIn('estado', ['activo', 'alta_clinica'])
            ->orderBy('fecha_ingreso', 'desc');

        if ($request->has('estado_clinico') && $request->estado_clinico !== 'todos') {
            $query->where('estado_clinico', $request->estado_clinico);
        }

        $pacientes = $query->get()->map(function($adm) {
            $ultimosSignos = $adm->vitalSigns->first();
            $registroHoy = $adm->dailyRecords->first();

            return [
                'id' => $adm->id,
                'nro_ingreso' => $adm->nro_ingreso,
                'paciente' => [
                    'ci' => $adm->paciente?->ci,
                    'nombre' => $adm->paciente?->nombre ?? 'Desconocido',
                    'telefono' => $adm->paciente?->telefono,
                ],
                'estado_clinico' => $adm->estado_clinico,
                'estado_clinico_color' => $adm->estado_clinico_color,
                'cama' => $adm->bed?->bed_number ?? 'Sin cama',
                'dias_en_uti' => $adm->dias_en_uti,
                'tiempo_texto' => $adm->tiempo_en_uti_texto,
                'diagnostico_principal' => $adm->diagnostico_principal,
                'medico_responsable' => $adm->medico?->nombre ?? 'No asignado',
                'tipo_pago' => $adm->tipo_pago,
                'estado' => $adm->estado,
                'ultimos_signos' => $ultimosSignos ? [
                    'presion' => $ultimosSignos->presion_arterial,
                    'fc' => $ultimosSignos->frecuencia_cardiaca,
                    'fr' => $ultimosSignos->frecuencia_respiratoria,
                    'temp' => $ultimosSignos->temperatura,
                    'sat' => $ultimosSignos->saturacion_o2,
                    'fecha' => $ultimosSignos->fecha->format('d/m/Y'),
                    'turno' => $ultimosSignos->turno_label,
                ] : null,
                'estado_dia' => $registroHoy ? [
                    'validado' => $registroHoy->dia_validado,
                    'ronda_completada' => $registroHoy->ronda_completada,
                    'label' => $registroHoy->estado_dia_label,
                    'color' => $registroHoy->estado_dia_color,
                ] : [
                    'validado' => false,
                    'ronda_completada' => false,
                    'label' => 'Sin registros',
                    'color' => 'red',
                ],
            ];
        });

        $stats = [
            'total' => UtiAdmission::whereIn('estado', ['activo', 'alta_clinica'])->count(),
            'estables' => UtiAdmission::where('estado_clinico', 'estable')->whereIn('estado', ['activo', 'alta_clinica'])->count(),
            'criticos' => UtiAdmission::where('estado_clinico', 'critico')->whereIn('estado', ['activo', 'alta_clinica'])->count(),
            'muy_criticos' => UtiAdmission::where('estado_clinico', 'muy_critico')->whereIn('estado', ['activo', 'alta_clinica'])->count(),
            'camas_disponibles' => UtiBed::where('status', 'disponible')->where('activa', true)->count(),
            'camas_ocupadas' => UtiBed::where('status', 'ocupada')->where('activa', true)->count(),
        ];

        return response()->json([
            'success' => true,
            'pacientes' => $pacientes,
            'stats' => $stats,
        ]);
    }

    /**
     * Vista detalle de paciente UTI
     */
    public function show($id): View
    {
        $admission = UtiAdmission::with(['paciente', 'bed', 'medico', 'seguro'])->findOrFail($id);
        return view('uti.operativo.paciente-detalle', compact('admission'));
    }

    /**
     * API: Obtener detalle completo del paciente
     */
    public function getPacienteDetalle($id): JsonResponse
    {
        $admission = UtiAdmission::with([
            'paciente.seguro',
            'bed',
            'medico',
            'seguro',
            'vitalSigns' => function($q) {
                $q->orderBy('fecha', 'desc')->orderBy('hora', 'desc')->limit(50);
            },
            'dailyRecords' => function($q) {
                $q->orderBy('fecha', 'desc')->limit(30);
            },
            'medicaments.medicamento',
            'supplies.insumo',
            'catering',
            'recipes.medico',
        ])->findOrFail($id);

        $hoy = today()->toDateString();
        $registroHoy = $admission->dailyRecords->where('fecha', $hoy)->first();
        $signosHoy = $admission->vitalSigns->where('fecha', $hoy);

        return response()->json([
            'success' => true,
            'admission' => [
                'id' => $admission->id,
                'nro_ingreso' => $admission->nro_ingreso,
                'estado_clinico' => $admission->estado_clinico,
                'estado_clinico_color' => $admission->estado_clinico_color,
                'diagnostico_principal' => $admission->diagnostico_principal,
                'diagnostico_secundario' => $admission->diagnostico_secundario,
                'tipo_ingreso' => $admission->tipo_ingreso,
                'tipo_pago' => $admission->tipo_pago,
                'estado' => $admission->estado,
                'fecha_ingreso' => $admission->fecha_ingreso?->format('d/m/Y H:i'),
                'dias_en_uti' => $admission->dias_en_uti,
                'tiempo_texto' => $admission->tiempo_en_uti_texto,
                'nro_autorizacion' => $admission->nro_autorizacion,
            ],
            'paciente' => [
                'ci' => $admission->paciente?->ci,
                'nombre' => $admission->paciente?->nombre,
                'sexo' => $admission->paciente?->sexo,
                'telefono' => $admission->paciente?->telefono,
                'direccion' => $admission->paciente?->direccion,
                'seguro' => $admission->paciente?->seguro?->nombre,
            ],
            'cama' => $admission->bed ? [
                'id' => $admission->bed->id,
                'numero' => $admission->bed->bed_number,
                'tipo' => $admission->bed->tipo,
            ] : null,
            'medico' => $admission->medico ? [
                'id' => $admission->medico->id,
                'nombre' => $admission->medico->nombre,
            ] : null,
            'validaciones_hoy' => [
                'ronda_completada' => $registroHoy?->ronda_completada ?? false,
                'dia_validado' => $registroHoy?->dia_validado ?? false,
                'tiene_signos_manana' => $signosHoy->where('turno', 'manana')->isNotEmpty(),
                'tiene_signos_tarde' => $signosHoy->where('turno', 'tarde')->isNotEmpty(),
                'tiene_signos_noche' => $signosHoy->where('turno', 'noche')->isNotEmpty(),
                'puede_cerrar_dia' => ($registroHoy?->ronda_completada ?? false) && $signosHoy->isNotEmpty(),
            ],
            'signos_vitales' => $admission->vitalSigns->map(fn($s) => [
                'id' => $s->id,
                'fecha' => $s->fecha->format('d/m/Y'),
                'turno' => $s->turno_label,
                'hora' => $s->hora?->format('H:i'),
                'presion' => $s->presion_arterial,
                'fc' => $s->frecuencia_cardiaca,
                'fr' => $s->frecuencia_respiratoria,
                'temp' => $s->temperatura,
                'sat' => $s->saturacion_o2,
                'glicemia' => $s->glicemia,
            ]),
            'evoluciones' => $admission->dailyRecords->map(fn($r) => [
                'id' => $r->id,
                'fecha' => $r->fecha->format('d/m/Y'),
                'evolucion_medica' => $r->evolucion_medica,
                'indicaciones' => $r->indicaciones,
                'plan_tratamiento' => $r->plan_tratamiento,
                'ronda_completada' => $r->ronda_completada,
                'dia_validado' => $r->dia_validado,
                'medico' => $r->medico?->nombre,
            ]),
            'medicamentos' => $admission->medications->map(fn($m) => [
                'id' => $m->id,
                'medicamento' => $m->medicamento?->nombre,
                'dosis' => $m->dosis . ' ' . $m->unidad,
                'via' => $m->via_administracion,
                'fecha' => $m->fecha->format('d/m/Y'),
                'hora' => $m->hora?->format('H:i'),
                'cargo_generado' => $m->cargo_generado,
            ]),
            'insumos' => $admission->supplies->map(fn($s) => [
                'id' => $s->id,
                'insumo' => $s->insumo?->nombre,
                'cantidad' => $s->cantidad,
                'fecha' => $s->fecha->format('d/m/Y'),
                'cargo_generado' => $s->cargo_generado,
            ]),
            'alimentacion' => $admission->catering->map(fn($c) => [
                'id' => $c->id,
                'tipo_comida' => $c->tipo_comida_label,
                'estado' => $c->estado,
                'fecha' => $c->fecha->format('d/m/Y'),
                'cargo_generado' => $c->cargo_generado,
            ]),
            'recetas' => $admission->recipes->map(fn($r) => [
                'id' => $r->id,
                'nro_receta' => $r->nro_receta,
                'fecha' => $r->fecha->format('d/m/Y'),
                'estado' => $r->estado,
                'estado_color' => $r->estado_color,
                'medico' => $r->medico?->nombre,
            ]),
        ]);
    }

    /**
     * API: Guardar signos vitales
     */
    public function guardarSignosVitales(Request $request, $admissionId): JsonResponse
    {
        $admission = UtiAdmission::findOrFail($admissionId);

        $validated = $request->validate([
            'turno' => 'required|in:manana,tarde,noche',
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i',
            'presion_arterial_sistolica' => 'nullable|numeric|min:0|max:300',
            'presion_arterial_diastolica' => 'nullable|numeric|min:0|max:200',
            'frecuencia_cardiaca' => 'nullable|numeric|min:0|max:300',
            'frecuencia_respiratoria' => 'nullable|numeric|min:0|max:100',
            'temperatura' => 'nullable|numeric|min:30|max:45',
            'saturacion_o2' => 'nullable|numeric|min:0|max:100',
            'glicemia' => 'nullable|numeric|min:0|max:1000',
            'peso' => 'nullable|numeric|min:0|max:500',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $validated['uti_admission_id'] = $admissionId;
        $validated['registered_by'] = Auth::id();

        // Buscar si ya existe registro para este turno y fecha
        $existente = UtiVitalSign::where('uti_admission_id', $admissionId)
            ->where('fecha', $validated['fecha'])
            ->where('turno', $validated['turno'])
            ->first();

        if ($existente) {
            $existente->update($validated);
            $signo = $existente;
        } else {
            $signo = UtiVitalSign::create($validated);
        }

        return response()->json([
            'success' => true,
            'message' => 'Signos vitales registrados correctamente',
            'data' => $signo,
        ]);
    }

    /**
     * API: Guardar evolución médica (ronda)
     */
    public function guardarEvolucion(Request $request, $admissionId): JsonResponse
    {
        $admission = UtiAdmission::findOrFail($admissionId);

        $validated = $request->validate([
            'fecha' => 'required|date',
            'evolucion_medica' => 'required|string|max:2000',
            'indicaciones' => 'nullable|string|max:2000',
            'plan_tratamiento' => 'nullable|string|max:2000',
            'medico_id' => 'required|exists:medicos,id',
        ]);

        $validated['uti_admission_id'] = $admissionId;
        $validated['ronda_completada'] = true;
        $validated['hora_ronda'] = now();

        $existente = UtiDailyRecord::where('uti_admission_id', $admissionId)
            ->where('fecha', $validated['fecha'])
            ->first();

        if ($existente) {
            $existente->update($validated);
            $registro = $existente;
        } else {
            $registro = UtiDailyRecord::create($validated);
        }

        return response()->json([
            'success' => true,
            'message' => 'Evolución médica registrada correctamente',
            'data' => $registro,
        ]);
    }

    /**
     * API: Validar día UTI
     */
    public function validarDia(Request $request, $admissionId): JsonResponse
    {
        $admission = UtiAdmission::findOrFail($admissionId);

        $validated = $request->validate([
            'fecha' => 'required|date',
        ]);

        $registro = UtiDailyRecord::where('uti_admission_id', $admissionId)
            ->where('fecha', $validated['fecha'])
            ->first();

        if (!$registro) {
            return response()->json([
                'success' => false,
                'message' => 'No existe registro de ronda para este día',
            ], 422);
        }

        if (!$registro->ronda_completada) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede validar el día sin ronda médica completada',
            ], 422);
        }

        // Verificar signos vitales del día
        $tieneSignos = UtiVitalSign::where('uti_admission_id', $admissionId)
            ->where('fecha', $validated['fecha'])
            ->exists();

        if (!$tieneSignos) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede validar el día sin signos vitales registrados',
            ], 422);
        }

        $registro->update([
            'dia_validado' => true,
            'hora_validacion' => now(),
        ]);

        // Generar cargo automático por día de estadía
        $this->generarCargoEstadia($admission, $validated['fecha']);

        return response()->json([
            'success' => true,
            'message' => 'Día validado correctamente',
        ]);
    }

    /**
     * API: Registrar medicamento administrado
     */
    public function registrarMedicamento(Request $request, $admissionId): JsonResponse
    {
        $admission = UtiAdmission::findOrFail($admissionId);

        $validated = $request->validate([
            'medicamento_id' => 'required|exists:medicamentos,id',
            'dosis' => 'required|numeric|min:0.01',
            'unidad' => 'required|string|max:20',
            'via_administracion' => 'required|string|max:50',
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $validated['uti_admission_id'] = $admissionId;
        $validated['administered_by'] = Auth::id();

        $medication = UtiMedication::create($validated);

        // Generar cargo automático
        $this->generarCargoMedicamento($medication);

        return response()->json([
            'success' => true,
            'message' => 'Medicamento registrado correctamente',
            'data' => $medication,
        ]);
    }

    /**
     * API: Registrar insumo utilizado
     */
    public function registrarInsumo(Request $request, $admissionId): JsonResponse
    {
        $admission = UtiAdmission::findOrFail($admissionId);

        $validated = $request->validate([
            'insumo_id' => 'required|exists:insumos,id',
            'cantidad' => 'required|numeric|min:0.01',
            'fecha' => 'required|date',
            'hora' => 'required|date_format:H:i',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $validated['uti_admission_id'] = $admissionId;
        $validated['used_by'] = Auth::id();

        $supply = UtiSupply::create($validated);

        // Generar cargo automático
        $this->generarCargoInsumo($supply);

        return response()->json([
            'success' => true,
            'message' => 'Insumo registrado correctamente',
            'data' => $supply,
        ]);
    }

    /**
     * API: Registrar alimentación
     */
    public function registrarAlimentacion(Request $request, $admissionId): JsonResponse
    {
        $admission = UtiAdmission::findOrFail($admissionId);

        $validated = $request->validate([
            'tipo_comida' => 'required|in:desayuno,almuerzo,merienda,cena',
            'estado' => 'required|in:dado,no_dado,no_aplica',
            'fecha' => 'required|date',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $validated['uti_admission_id'] = $admissionId;
        $validated['registered_by'] = Auth::id();
        $validated['hora_registro'] = now();

        $existente = UtiCatering::where('uti_admission_id', $admissionId)
            ->where('fecha', $validated['fecha'])
            ->where('tipo_comida', $validated['tipo_comida'])
            ->first();

        if ($existente) {
            $existente->update($validated);
            $catering = $existente;
        } else {
            $catering = UtiCatering::create($validated);
        }

        // Generar cargo si fue dado
        if ($validated['estado'] === 'dado') {
            $this->generarCargoAlimentacion($catering);
        }

        return response()->json([
            'success' => true,
            'message' => 'Alimentación registrada correctamente',
            'data' => $catering,
        ]);
    }

    /**
     * API: Cambiar estado clínico del paciente
     */
    public function cambiarEstadoClinico(Request $request, $admissionId): JsonResponse
    {
        $admission = UtiAdmission::findOrFail($admissionId);

        $validated = $request->validate([
            'estado_clinico' => 'required|in:estable,critico,muy_critico',
        ]);

        $admission->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Estado clínico actualizado correctamente',
            'estado_clinico' => $admission->estado_clinico,
            'estado_color' => $admission->estado_clinico_color,
        ]);
    }

    /**
     * API: Dar alta clínica
     */
    public function darAltaClinica(Request $request, $admissionId): JsonResponse
    {
        $admission = UtiAdmission::findOrFail($admissionId);

        $validated = $request->validate([
            'destino_alta' => 'required|in:hospitalizacion,domicilio,otro_hospital',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        // Verificar que el día de hoy esté validado
        $hoy = today()->toDateString();
        $registroHoy = UtiDailyRecord::where('uti_admission_id', $admissionId)
            ->where('fecha', $hoy)
            ->first();

        if (!$registroHoy || !$registroHoy->dia_validado) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede dar alta sin validar el día actual',
            ], 422);
        }

        // Liberar cama
        if ($admission->bed_id) {
            UtiBed::where('id', $admission->bed_id)->update(['status' => 'disponible']);
        }

        $admission->update([
            'estado' => 'alta_clinica',
            'fecha_alta_clinica' => now(),
            'destino_alta' => $validated['destino_alta'],
            'observaciones' => $validated['observaciones'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Alta clínica registrada correctamente',
        ]);
    }

    /**
     * API: Trasladar paciente
     */
    public function trasladarPaciente(Request $request, $admissionId): JsonResponse
    {
        $admission = UtiAdmission::findOrFail($admissionId);

        $validated = $request->validate([
            'destino' => 'required|in:hospitalizacion,quirofano',
            'motivo' => 'nullable|string|max:500',
        ]);

        // Liberar cama UTI
        if ($admission->bed_id) {
            UtiBed::where('id', $admission->bed_id)->update(['status' => 'disponible']);
        }

        $admission->update([
            'estado' => 'trasladado',
            'observaciones' => ($admission->observaciones ? $admission->observaciones . "\n" : '') .
                'Trasladado a ' . $validated['destino'] . ': ' . ($validated['motivo'] ?? ''),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Paciente trasladado correctamente',
        ]);
    }

    /**
     * API: Obtener camas disponibles
     */
    public function getCamasDisponibles(): JsonResponse
    {
        $camas = UtiBed::where('status', 'disponible')
            ->where('activa', true)
            ->get();

        return response()->json([
            'success' => true,
            'camas' => $camas,
        ]);
    }

    /**
     * API: Asignar cama
     */
    public function asignarCama(Request $request, $admissionId): JsonResponse
    {
        $admission = UtiAdmission::findOrFail($admissionId);

        $validated = $request->validate([
            'bed_id' => 'required|exists:uti_beds,id',
        ]);

        $cama = UtiBed::findOrFail($validated['bed_id']);

        if ($cama->status !== 'disponible') {
            return response()->json([
                'success' => false,
                'message' => 'La cama seleccionada no está disponible',
            ], 422);
        }

        // Liberar cama anterior si existe
        if ($admission->bed_id) {
            UtiBed::where('id', $admission->bed_id)->update(['status' => 'disponible']);
        }

        $admission->update(['bed_id' => $validated['bed_id']]);
        $cama->update(['status' => 'ocupada']);

        return response()->json([
            'success' => true,
            'message' => 'Cama asignada correctamente',
        ]);
    }

    /**
     * API: Obtener medicamentos disponibles
     */
    public function getMedicamentosDisponibles(Request $request): JsonResponse
    {
        $search = $request->get('search', '');

        $medicamentos = Medicamentos::where('nombre', 'like', "%{$search}%")
            ->orWhere('principio_activo', 'like', "%{$search}%")
            ->limit(20)
            ->get(['id', 'nombre', 'principio_activo', 'presentacion']);

        return response()->json([
            'success' => true,
            'medicamentos' => $medicamentos,
        ]);
    }

    /**
     * API: Obtener insumos disponibles
     */
    public function getInsumosDisponibles(Request $request): JsonResponse
    {
        $search = $request->get('search', '');

        $insumos = Insumos::where('nombre', 'like', "%{$search}%")
            ->limit(20)
            ->get(['id', 'nombre', 'tipo', 'unidad_medida']);

        return response()->json([
            'success' => true,
            'insumos' => $insumos,
        ]);
    }

    /**
     * Métodos privados para generación de cargos
     */
    private function generarCargoEstadia($admission, $fecha)
    {
        // Implementar integración con servicio de cuenta de cobro
        // Este es un placeholder - se debe implementar según la lógica del sistema
    }

    private function generarCargoMedicamento($medication)
    {
        // Implementar integración con servicio de cuenta de cobro
        $medication->update(['cargo_generado' => true]);
    }

    private function generarCargoInsumo($supply)
    {
        // Implementar integración con servicio de cuenta de cobro
        $supply->update(['cargo_generado' => true]);
    }

    private function generarCargoAlimentacion($catering)
    {
        // Implementar integración con servicio de cuenta de cobro
        $catering->update(['cargo_generado' => true]);
    }
}
