<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hospitalizacion;
use App\Models\Paciente;
use App\Models\UtiAdmission;
use App\Models\UtiBed;
use App\Models\Cama;
use App\Models\CuentaCobro;
use App\Models\CuentaCobroDetalle;
use App\Models\AlmacenMedicamento;
use App\Models\HospMedicamentoAdministrado;
use App\Models\HospCatering;
use App\Models\HospDrenaje;
use App\Models\Emergency;
use App\Models\Quirofano;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InternacionStaffController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:internacion|admin|dirmedico|enfermera-internacion|administrador');
    }

    public function index(): View
    {
        return view('internacion-staff.dashboard');
    }

    /**
     * Mostrar página de evaluación del paciente
     */
    public function evaluar($id): View
    {
        $hospitalizacion = Hospitalizacion::with(['paciente', 'medico.user', 'medico.especialidad'])->findOrFail($id);

        // Obtener permisos de la enfermera logueada
        $userPermissions = [];
        if (Auth::check()) {
            $enfermera = \App\Models\Enfermera::where('user_id', Auth::id())->first();
            if ($enfermera) {
                $userPermissions = $enfermera->getPermissionKeys();
            }
        }

        return view('internacion-staff.evaluar', compact('hospitalizacion', 'userPermissions'));
    }

    /**
     * API: Get internaciones for dashboard
     */
    public function apiInternaciones(Request $request): JsonResponse
    {
        $query = Hospitalizacion::with(['paciente', 'medico.user', 'medico.especialidad'])
            ->whereNull('fecha_alta')
            ->where('estado', '!=', 'trasladado')
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
            'activos' => Hospitalizacion::whereNull('fecha_alta')->where('estado', '!=', 'trasladado')->count(),
            'espera' => Hospitalizacion::whereNull('fecha_alta')->where('estado', '!=', 'trasladado')->whereNull('habitacion_id')->count(),
            'atencion' => Hospitalizacion::whereNull('fecha_alta')->where('estado', '!=', 'trasladado')->whereNotNull('habitacion_id')->count(),
            'hoy' => Hospitalizacion::whereDate('fecha_ingreso', today())->where('estado', '!=', 'trasladado')->count(),
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
            'activos' => Hospitalizacion::whereNull('fecha_alta')->where('estado', '!=', 'trasladado')->count(),
            'espera' => Hospitalizacion::whereNull('fecha_alta')->where('estado', '!=', 'trasladado')->whereNull('habitacion_id')->count(),
            'atencion' => Hospitalizacion::whereNull('fecha_alta')->where('estado', '!=', 'trasladado')->whereNotNull('habitacion_id')->count(),
            'hoy' => Hospitalizacion::whereDate('fecha_ingreso', today())->where('estado', '!=', 'trasladado')->count(),
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
                'medico_responsable_ci' => $hospitalizacion->ci_medico,
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
     * API: Derivar a Quirófano
     */
    public function derivarAQuirofano(Request $request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $hospitalizacion = Hospitalizacion::with('paciente')->findOrFail($id);

            // Verificar si ya está en quirófano (buscar en emergencias)
            $cirugiaActiva = Emergency::where('patient_id', $hospitalizacion->ci_paciente)
                ->where('ubicacion_actual', 'cirugia')
                ->whereIn('status', ['cirugia', 'en_evaluacion', 'estabilizado'])
                ->first();

            if ($cirugiaActiva) {
                return response()->json([
                    'success' => false,
                    'message' => 'El paciente ya tiene una cirugía activa',
                ], 422);
            }

            // Buscar quirófano (opcional - si existe alguno disponible, se usa para info)
            $quirofano = Quirofano::where('estado', '!=', 'mantenimiento')->first();

            // Generar número de cirugía
            $nroCirugia = 'CIR-' . now()->format('Ymd') . '-' . str_pad($hospitalizacion->id, 4, '0', STR_PAD_LEFT);

            // Crear registro en emergencias como paciente en cirugía
            $emergency = Emergency::create([
                'patient_id' => $hospitalizacion->ci_paciente,
                'user_id' => auth()->id(),
                'code' => Emergency::generateCode(),
                'is_temp_id' => false,
                'status' => 'cirugia',
                'ubicacion_actual' => 'cirugia',
                'nro_cirugia' => $nroCirugia,
                'symptoms' => 'Derivación desde internación para cirugía',
                'initial_assessment' => $hospitalizacion->diagnostico,
                'admission_date' => now(),
                'tipo_ingreso' => 'general',
                'destino_inicial' => 'cirugia',
                'flujo_historial' => [
                    [
                        'fecha' => now()->toDateTimeString(),
                        'desde' => 'internacion',
                        'hasta' => 'cirugia',
                        'usuario_id' => auth()->id(),
                        'notas' => 'Derivación desde internación a quirófano',
                    ]
                ],
            ]);

            // Actualizar hospitalización
            $hospitalizacion->update([
                'estado' => 'trasladado',
                'observaciones' => ($hospitalizacion->observaciones ? $hospitalizacion->observaciones . "\n" : '') .
                    'Trasladado a Quirófano: ' . now()->format('d/m/Y H:i'),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Paciente derivado a Quirófano correctamente',
                'cirugia' => [
                    'id' => $emergency->id,
                    'nro_cirugia' => $nroCirugia,
                    'quirofano' => $quirofano ? ($quirofano->tipo ?? 'Q' . $quirofano->id) : 'Sin asignar',
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error al derivar a quirófano: ' . $e->getMessage(), [
                'hospitalizacion_id' => $id,
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al derivar a Quirófano: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: Dar alta
     */
    public function darAlta(Request $request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();

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

            // 1. Si tiene cama asignada, liberarla y calcular estancia
            if ($hospitalizacion->cama_id) {
                $cama = Cama::find($hospitalizacion->cama_id);

                if ($cama) {
                    // Calcular días y costo
                    $dias = $hospitalizacion->getDiasEstancia();
                    $costoTotal = $hospitalizacion->getCostoEstancia();

                    // Actualizar detalle de cuenta de cobro
                    if ($hospitalizacion->cuenta_cobro_detalle_id) {
                        $detalle = CuentaCobroDetalle::find($hospitalizacion->cuenta_cobro_detalle_id);
                        if ($detalle) {
                            $detalle->update([
                                'cantidad' => $dias,
                                'subtotal' => $costoTotal,
                                'descripcion' => "Estancia Internación - {$dias} días (Alta médica)",
                            ]);

                            // Recalcular total de cuenta
                            $cuenta = $detalle->cuentaCobro;
                            if ($cuenta) {
                                $cuenta->total_calculado = $cuenta->detalles->sum('subtotal');
                                $cuenta->save();
                            }
                        }
                    }

                    // Guardar totales en hospitalización
                    $hospitalizacion->update([
                        'total_estancia' => $costoTotal,
                    ]);

                    // Liberar cama
                    $cama->update(['disponibilidad' => 'disponible']);

                    // Verificar si habitación quedó vacía
                    $habitacion = $cama->habitacion;
                    $camasOcupadas = $habitacion->camas()->where('disponibilidad', 'ocupada')->count();
                    if ($camasOcupadas === 0 && $habitacion->estado === 'ocupada') {
                        $habitacion->update(['estado' => 'disponible']);
                    }
                }
            }

            // 2. Marcar fecha de alta
            $hospitalizacion->update([
                'fecha_alta' => now(),
                'motivo_alta' => $validated['motivo_alta'] ?? 'Alta médica',
                'estado' => 'alta',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Paciente dado de alta exitosamente' . ($hospitalizacion->total_estancia > 0
                    ? '. Estancia: ' . $hospitalizacion->getDiasEstancia() . ' días, Total: Bs. ' . number_format($hospitalizacion->total_estancia, 2)
                    : ''),
                'hospitalizacion' => $hospitalizacion
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al dar de alta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get medicamentos disponibles del inventario de internacion
     */
    public function apiMedicamentosDisponibles(): JsonResponse
    {
        $medicamentos = AlmacenMedicamento::porArea('internacion')
            ->where('cantidad', '>', 0)
            ->activos()
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'tipo', 'unidad_medida', 'precio', 'cantidad']);

        return response()->json([
            'success' => true,
            'medicamentos' => $medicamentos
        ]);
    }

    /**
     * API: Buscar medicamentos por nombre
     */
    public function buscarMedicamentos(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        $medicamentos = AlmacenMedicamento::porArea('internacion')
            ->where('cantidad', '>', 0)
            ->activos()
            ->where('nombre', 'like', '%' . $query . '%')
            ->limit(10)
            ->get(['id', 'nombre', 'tipo', 'unidad_medida', 'precio', 'cantidad']);

        return response()->json([
            'success' => true,
            'medicamentos' => $medicamentos
        ]);
    }

    /**
     * API: Get medicamentos administrados a un paciente
     */
    public function apiMedicamentos($id): JsonResponse
    {
        $medicamentos = HospMedicamentoAdministrado::with(['medicamento', 'administeredBy'])
            ->where('hospitalizacion_id', $id)
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->get()
            ->map(function($med) {
                return [
                    'id' => $med->id,
                    'medicamento' => $med->medicamento?->nombre ?? 'Desconocido',
                    'tipo' => $med->medicamento?->tipo ?? '-',
                    'cantidad' => $med->cantidad,
                    'unidad' => $med->unidad,
                    'via_administracion' => $med->via_administracion,
                    'fecha' => $med->fecha?->format('d/m/Y'),
                    'hora' => $med->hora?->format('H:i'),
                    'administrado_por' => $med->administeredBy?->name ?? 'Desconocido',
                    'observaciones' => $med->observaciones,
                    'cargo_generado' => $med->cargo_generado,
                ];
            });

        return response()->json([
            'success' => true,
            'medicamentos' => $medicamentos
        ]);
    }

    /**
     * API: Registrar medicamento administrado
     */
    public function storeMedicamento(Request $request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $hospitalizacion = Hospitalizacion::with('paciente')->findOrFail($id);
            $medicamento = AlmacenMedicamento::findOrFail($request->medicamento_id);

            $validated = $request->validate([
                'medicamento_id' => 'required|exists:almacen_medicamentos,id',
                'cantidad' => 'required|numeric|min:0.01',
                'unidad' => 'required|string|max:20',
                'via_administracion' => 'nullable|string|max:50',
                'observaciones' => 'nullable|string',
            ]);

            // Verificar stock suficiente
            if ($medicamento->cantidad < $validated['cantidad']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuficiente. Disponible: ' . $medicamento->cantidad . ' ' . $medicamento->unidad_medida
                ], 422);
            }

            // Descontar del inventario
            $medicamento->decrement('cantidad', $validated['cantidad']);

            // Obtener CI del paciente (manejar pacientes temporales de emergencia)
            $ciPaciente = $hospitalizacion->ci_paciente;
            if (!$ciPaciente && $hospitalizacion->nro_emergencia) {
                $emergencia = Emergency::where('code', $hospitalizacion->nro_emergencia)
                    ->orWhere('id', $hospitalizacion->nro_emergencia)
                    ->first();
                if ($emergencia) {
                    $ciPaciente = $emergencia->patient_id;
                }
            }

            // Calcular precio y generar cargo
            $cuentaCobroDetalleId = null;
            $cargoGenerado = false;
            $precio = $medicamento->precio ?? 0;
            $subtotal = $precio * $validated['cantidad'];

            if ($subtotal > 0 && $ciPaciente) {
                $cuentaCobroDetalleId = $this->generarCargo(
                    $hospitalizacion,
                    $ciPaciente,
                    'Medicamento: ' . $medicamento->nombre,
                    $subtotal,
            1,
                    'medicamento',
                    $medicamento->id
                );
                $cargoGenerado = true;
            }

            // Crear registro
            $registro = HospMedicamentoAdministrado::create([
                'hospitalizacion_id' => $id,
                'medicamento_id' => $validated['medicamento_id'],
                'administered_by' => Auth::id(),
                'fecha' => now(),
                'hora' => now(),
                'cantidad' => $validated['cantidad'],
                'unidad' => $validated['unidad'],
                'via_administracion' => $validated['via_administracion'],
                'observaciones' => $validated['observaciones'],
                'cargo_generado' => $cargoGenerado,
                'cuenta_cobro_detalle_id' => $cuentaCobroDetalleId,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Medicamento registrado correctamente' . ($cargoGenerado ? ' y cargo generado' : ''),
                'registro' => $registro
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar medicamento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get catering del paciente
     */
    public function apiCatering($id): JsonResponse
    {
        $hospitalizacion = Hospitalizacion::findOrFail($id);

        // Obtener o inicializar registros de hoy
        $fecha = now()->toDateString();
        $tiposComida = ['desayuno', 'almuerzo', 'merienda', 'cena'];
        $catering = [];

        foreach ($tiposComida as $tipo) {
            $registro = HospCatering::where('hospitalizacion_id', $id)
                ->where('fecha', $fecha)
                ->where('tipo_comida', $tipo)
                ->first();

            if ($registro) {
                $catering[] = [
                    'id' => $registro->id,
                    'tipo_comida' => $tipo,
                    'tipo_label' => $registro->tipo_comida_label,
                    'estado' => $registro->estado,
                    'estado_label' => $registro->estado_label,
                    'estado_color' => $registro->estado_color,
                    'hora_registro' => $registro->hora_registro?->format('H:i'),
                    'observaciones' => $registro->observaciones,
                    'precio' => $registro->precio,
                    'cargo_generado' => $registro->cargo_generado,
                ];
            } else {
                $precios = config('hospitalizacion.catering.precios', []);
                $catering[] = [
                    'id' => null,
                    'tipo_comida' => $tipo,
                    'tipo_label' => ucfirst($tipo),
                    'estado' => 'no_dado',
                    'estado_label' => 'No Dado',
                    'estado_color' => 'red',
                    'hora_registro' => null,
                    'observaciones' => null,
                    'precio' => $precios[$tipo] ?? 0,
                    'cargo_generado' => false,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'fecha' => $fecha,
            'catering' => $catering
        ]);
    }

    /**
     * API: Registrar/actualizar catering
     */
    public function storeCatering(Request $request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $hospitalizacion = Hospitalizacion::with('paciente')->findOrFail($id);

            $validated = $request->validate([
                'tipo_comida' => 'required|in:desayuno,almuerzo,merienda,cena',
                'estado' => 'required|in:dado,no_dado,no_aplica',
                'observaciones' => 'nullable|string',
            ]);

            $fecha = now()->toDateString();

            // Buscar registro existente
            $registro = HospCatering::where('hospitalizacion_id', $id)
                ->where('fecha', $fecha)
                ->where('tipo_comida', $validated['tipo_comida'])
                ->first();

            $precio = 0;
            $cuentaCobroDetalleId = null;
            $cargoGenerado = false;

            // Obtener CI del paciente (manejar pacientes temporales de emergencia)
            $ciPaciente = $hospitalizacion->ci_paciente;
            if (!$ciPaciente && $hospitalizacion->nro_emergencia) {
                $emergencia = Emergency::where('code', $hospitalizacion->nro_emergencia)
                    ->orWhere('id', $hospitalizacion->nro_emergencia)
                    ->first();
                if ($emergencia) {
                    $ciPaciente = $emergencia->patient_id;
                }
            }

            if ($validated['estado'] === 'dado' && $ciPaciente) {
                $precios = config('hospitalizacion.catering.precios', []);
                $precio = $precios[$validated['tipo_comida']] ?? 0;

                // Si ya tenía un cargo anterior, no generar otro
                if ($registro && $registro->cargo_generado && $registro->estado === 'dado') {
                    $cuentaCobroDetalleId = $registro->cuenta_cobro_detalle_id;
                    $cargoGenerado = true;
                } elseif ($precio > 0) {
                    $cuentaCobroDetalleId = $this->generarCargo(
                        $hospitalizacion,
                        $ciPaciente,
                        'Catering: ' . ucfirst($validated['tipo_comida']),
                        $precio,
                        1,
                        'servicio'
                    );
                    $cargoGenerado = true;
                }
            }

            $data = [
                'hospitalizacion_id' => $id,
                'registered_by' => Auth::id(),
                'fecha' => $fecha,
                'tipo_comida' => $validated['tipo_comida'],
                'estado' => $validated['estado'],
                'hora_registro' => $validated['estado'] === 'dado' ? now() : null,
                'observaciones' => $validated['observaciones'] ?? null,
                'precio' => $precio,
                'cargo_generado' => $cargoGenerado,
                'cuenta_cobro_detalle_id' => $cuentaCobroDetalleId,
            ];

            if ($registro) {
                // Si cambia de 'dado' a otro estado, anular el cargo anterior
                if ($registro->estado === 'dado' && $validated['estado'] !== 'dado' && $registro->cuenta_cobro_detalle_id) {
                    $this->anularCargo($registro->cuenta_cobro_detalle_id);
                    $data['cargo_generado'] = false;
                    $data['cuenta_cobro_detalle_id'] = null;
                    $data['precio'] = 0;
                }
                $registro->update($data);
            } else {
                $registro = HospCatering::create($data);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Catering registrado correctamente' . ($cargoGenerado ? ' y cargo generado' : ''),
                'registro' => $registro
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar catering: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get drenajes del paciente
     */
    public function apiDrenajes($id): JsonResponse
    {
        $drenajes = HospDrenaje::with('registeredBy')
            ->where('hospitalizacion_id', $id)
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->get()
            ->map(function($drenaje) {
                return [
                    'id' => $drenaje->id,
                    'fecha' => $drenaje->fecha?->format('d/m/Y'),
                    'hora' => $drenaje->hora?->format('H:i'),
                    'tipo_drenaje' => $drenaje->tipo_drenaje,
                    'realizado' => $drenaje->realizado,
                    'observaciones' => $drenaje->observaciones,
                    'precio' => $drenaje->precio,
                    'cargo_generado' => $drenaje->cargo_generado,
                    'registrado_por' => $drenaje->registeredBy?->name ?? 'Desconocido',
                ];
            });

        return response()->json([
            'success' => true,
            'drenajes' => $drenajes
        ]);
    }

    /**
     * API: Registrar drenaje
     */
    public function storeDrenaje(Request $request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $hospitalizacion = Hospitalizacion::with('paciente')->findOrFail($id);

            $validated = $request->validate([
                'tipo_drenaje' => 'nullable|string|max:50',
                'realizado' => 'required|boolean',
                'observaciones' => 'nullable|string',
            ]);

            // Obtener CI del paciente (manejar pacientes temporales de emergencia)
            $ciPaciente = $hospitalizacion->ci_paciente;
            if (!$ciPaciente && $hospitalizacion->nro_emergencia) {
                $emergencia = Emergency::where('code', $hospitalizacion->nro_emergencia)
                    ->orWhere('id', $hospitalizacion->nro_emergencia)
                    ->first();
                if ($emergencia) {
                    $ciPaciente = $emergencia->patient_id;
                }
            }

            $precio = 0;
            $cuentaCobroDetalleId = null;
            $cargoGenerado = false;

            if ($validated['realizado'] && $ciPaciente) {
                $precios = config('hospitalizacion.drenajes.precios', []);
                $precio = $precios[$validated['tipo_drenaje']] ?? config('hospitalizacion.drenajes.precio_default', 40);

                if ($precio > 0) {
                    $cuentaCobroDetalleId = $this->generarCargo(
                        $hospitalizacion,
                        $ciPaciente,
                        'Drenaje: ' . ($validated['tipo_drenaje'] ?? 'General'),
                        $precio,
                        1,
                        'procedimiento'
                    );
                    $cargoGenerado = true;
                }
            }

            $registro = HospDrenaje::create([
                'hospitalizacion_id' => $id,
                'registered_by' => Auth::id(),
                'fecha' => now(),
                'hora' => now(),
                'tipo_drenaje' => $validated['tipo_drenaje'],
                'realizado' => $validated['realizado'],
                'observaciones' => $validated['observaciones'],
                'precio' => $precio,
                'cargo_generado' => $cargoGenerado,
                'cuenta_cobro_detalle_id' => $cuentaCobroDetalleId,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Drenaje registrado correctamente' . ($cargoGenerado ? ' y cargo generado' : ''),
                'registro' => $registro
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar drenaje: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper: Generar cargo en cuenta de cobro
     */
    private function generarCargo($hospitalizacion, $ciPaciente, $concepto, $precio, $cantidad = 1, $tipoItem = 'servicio', $origenId = null): ?int
    {
        // Usar la cuenta principal del paciente (Master Account)
        $cuenta = \App\Services\CuentaCobroService::obtenerCuentaPostPagoActiva((string)$ciPaciente);

        // Si por alguna razón crítica no existe (no debería pasar), crear una de internación
        if (!$cuenta) {
            $cuenta = \App\Services\CuentaCobroService::crearCuentaInternacion(
                (string)$ciPaciente,
                $hospitalizacion->id
            );
        }

        $detalleData = [
            'cuenta_cobro_id' => $cuenta->id,
            'tipo_item' => $tipoItem,
            'descripcion' => $concepto,
            'cantidad' => $cantidad,
            'precio_unitario' => $precio,
            'subtotal' => $precio * $cantidad,
        ];

        if ($origenId) {
            $detalleData['origen_type'] = AlmacenMedicamento::class;
            $detalleData['origen_id'] = $origenId;
        }

        $detalle = CuentaCobroDetalle::create($detalleData);

        // Recalcular total
        $cuenta->recalcularTotales();

        return $detalle->id;
    }

    /**
     * Helper: Anular cargo
     */
    private function anularCargo($detalleId): void
    {
        $detalle = CuentaCobroDetalle::find($detalleId);
        if ($detalle) {
            $cuenta = $detalle->cuentaCobro;
            $detalle->delete();
            if ($cuenta) {
                $cuenta->recalcularTotales();
            }
        }
    }

    /**
     * Mostrar página de historial del paciente
     */
    public function historial($id): View
    {
        $hospitalizacion = Hospitalizacion::with([
            'paciente',
            'medico.user',
            'medico.especialidad'
        ])->findOrFail($id);

        // Cargar medicamentos con relaciones
        $medicamentos = HospMedicamentoAdministrado::with(['medicamento', 'administeredBy'])
            ->where('hospitalizacion_id', $id)
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->get();

        // Cargar catering
        $catering = HospCatering::with('registeredBy')
            ->where('hospitalizacion_id', $id)
            ->orderBy('fecha', 'desc')
            ->orderBy('hora_registro', 'desc')
            ->get();

        // Cargar drenajes
        $drenajes = HospDrenaje::with('registeredBy')
            ->where('hospitalizacion_id', $id)
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->get();

        // Buscar emergencia relacionada si existe
        $emergencia = null;
        if ($hospitalizacion->nro_emergencia) {
            $emergencia = \App\Models\Emergency::where('code', $hospitalizacion->nro_emergencia)
                ->orWhere('nro_hospitalizacion', $hospitalizacion->id)
                ->first();
        }

        // Construir timeline
        $timeline = collect();

        // Evento de ingreso
        $timeline->push([
            'tipo' => 'ingreso',
            'titulo' => 'Ingreso a Internación',
            'descripcion' => 'Ingreso del paciente a la habitación ' . ($hospitalizacion->habitacion_id ?? 'Por asignar'),
            'fecha' => $hospitalizacion->fecha_ingreso?->format('d/m/Y'),
            'hora' => $hospitalizacion->fecha_ingreso?->format('H:i'),
            'responsable' => $hospitalizacion->medico?->user?->name ?? 'Sistema',
            'detalles' => null,
        ]);

        // Medicamentos
        foreach ($medicamentos as $med) {
            $timeline->push([
                'tipo' => 'medicamento',
                'titulo' => $med->medicamento?->nombre ?? 'Medicamento',
                'descripcion' => $med->observaciones,
                'fecha' => $med->fecha?->format('d/m/Y'),
                'hora' => $med->hora?->format('H:i'),
                'responsable' => $med->administeredBy?->name ?? 'Desconocido',
                'detalles' => [
                    'cantidad' => $med->cantidad,
                    'unidad' => $med->unidad,
                    'via_administracion' => $med->via_administracion,
                    'cargo_generado' => $med->cargo_generado,
                ],
            ]);
        }

        // Catering
        foreach ($catering as $cat) {
            $timeline->push([
                'tipo' => 'catering',
                'titulo' => $cat->tipo_label,
                'descripcion' => null,
                'fecha' => $cat->fecha?->format('d/m/Y'),
                'hora' => $cat->hora_registro?->format('H:i'),
                'responsable' => $cat->registeredBy?->name ?? 'Sistema',
                'detalles' => [
                    'estado' => $cat->estado,
                    'estado_label' => $cat->estado_label,
                    'cargo_generado' => $cat->cargo_generado,
                ],
            ]);
        }

        // Drenajes
        foreach ($drenajes as $dren) {
            $timeline->push([
                'tipo' => 'drenaje',
                'titulo' => $dren->tipo_drenaje ?: 'Drenaje General',
                'descripcion' => $dren->observaciones,
                'fecha' => $dren->fecha?->format('d/m/Y'),
                'hora' => $dren->hora?->format('H:i'),
                'responsable' => $dren->registeredBy?->name ?? 'Desconocido',
                'detalles' => [
                    'tipo_drenaje' => $dren->tipo_drenaje,
                    'realizado' => $dren->realizado,
                ],
            ]);
        }

        // Equipos Médicos
        $equiposMedicos = $hospitalizacion->equipos_medicos ?? [];
        foreach ($equiposMedicos as $evolucion) {
            if (isset($evolucion['equipos_medicos']) && is_array($evolucion['equipos_medicos'])) {
                $fechaEvolucion = $evolucion['fecha'] ?? null;
                foreach ($evolucion['equipos_medicos'] as $equipo) {
                    $timeline->push([
                        'tipo' => 'equipo_medico',
                        'titulo' => $equipo['nombre'] ?? 'Equipo Médico',
                        'descripcion' => null,
                        'fecha' => $fechaEvolucion ? \Carbon\Carbon::parse($fechaEvolucion)->format('d/m/Y') : 'N/A',
                        'hora' => $fechaEvolucion ? \Carbon\Carbon::parse($fechaEvolucion)->format('H:i') : 'N/A',
                        'responsable' => 'Médico',
                        'detalles' => [
                            'cantidad' => $equipo['cantidad'] ?? 1,
                            'precio_unitario' => $equipo['precio_unitario'] ?? $equipo['precio'] ?? 0,
                            'subtotal' => $equipo['subtotal'] ?? 0,
                        ],
                    ]);
                }
            }
        }

        // Evento de alta si existe
        if ($hospitalizacion->fecha_alta) {
            $timeline->push([
                'tipo' => 'alta',
                'titulo' => 'Alta Médica',
                'descripcion' => 'Paciente dado de alta de internación',
                'fecha' => $hospitalizacion->fecha_alta?->format('d/m/Y'),
                'hora' => $hospitalizacion->fecha_alta?->format('H:i'),
                'responsable' => 'Sistema',
                'detalles' => null,
            ]);
        }

        // Ordenar timeline por fecha/hora (más reciente primero)
        $timeline = $timeline->sortByDesc(function ($item) {
            $fechaHora = $item['fecha'] . ' ' . ($item['hora'] ?? '00:00');
            return \Carbon\Carbon::createFromFormat('d/m/Y H:i', $fechaHora);
        })->values();

        return view('internacion-staff.historial', compact(
            'hospitalizacion',
            'medicamentos',
            'catering',
            'drenajes',
            'emergencia',
            'timeline'
        ));
    }

    /**
     * API: Actualizar receta/diagnóstico del paciente
     */
    public function updateReceta(Request $request, $id): JsonResponse
    {
        try {
            $hospitalizacion = Hospitalizacion::findOrFail($id);

            $validated = $request->validate([
                'diagnostico' => 'nullable|string',
                'tratamiento' => 'nullable|string',
            ]);

            $hospitalizacion->update([
                'diagnostico' => $validated['diagnostico'] ?? $hospitalizacion->diagnostico,
                'tratamiento' => $validated['tratamiento'] ?? $hospitalizacion->tratamiento,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Información médica actualizada correctamente',
                'hospitalizacion' => [
                    'diagnostico' => $hospitalizacion->diagnostico,
                    'tratamiento' => $hospitalizacion->tratamiento,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar historial general de TODAS las internaciones (activas y dadas de alta)
     */
    public function historialGeneral(Request $request): View
    {
        $query = Hospitalizacion::with(['paciente', 'medico.user', 'habitacion'])
            ->orderBy('fecha_ingreso', 'desc'); // Todos los pacientes, ordenados por ingreso

        // Filtros
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_ingreso', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_ingreso', '<=', $request->fecha_hasta);
        }
        if ($request->filled('busqueda')) {
            $busqueda = $request->busqueda;
            $query->whereHas('paciente', function($q) use ($busqueda) {
                $q->where('nombre', 'like', "%{$busqueda}%")
                  ->orWhere('ci', 'like', "%{$busqueda}%");
            });
        }

        $hospitalizaciones = $query->paginate(15);

        // Estadísticas - TODAS las internaciones
        $stats = [
            'total' => Hospitalizacion::count(),
            'activos' => Hospitalizacion::whereNull('fecha_alta')->count(),
            'hoy' => Hospitalizacion::whereDate('fecha_ingreso', today())->count(),
            'mes' => Hospitalizacion::whereMonth('fecha_ingreso', now()->month)->whereYear('fecha_ingreso', now()->year)->count(),
        ];

        return view('internacion-staff.historial-general', compact('hospitalizaciones', 'stats'));
    }

    /**
     * Exportar historial de internaciones a Excel
     */
    public function exportHistorial(Request $request)
    {
        $query = Hospitalizacion::with(['paciente', 'medico.user']);

        // Aplicar filtros
        if ($request->filled('fecha_desde')) {
            $query->whereDate('fecha_ingreso', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha_ingreso', '<=', $request->fecha_hasta);
        }

        $hospitalizaciones = $query->orderBy('fecha_ingreso', 'desc')->get();

        // Crear archivo CSV
        $filename = 'historial_internaciones_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($hospitalizaciones) {
            $file = fopen('php://output', 'w');
            // Agregar BOM para Excel
            fprintf($file, "\xEF\xBB\xBF");

            // Headers con delimitador punto y coma (compatible con Excel en español)
            fputcsv($file, ['ID', 'Paciente', 'CI', 'Fecha Ingreso', 'Fecha Alta', 'Días Estancia', 'Médico', 'Diagnóstico', 'Total Estancia', 'Estado'], ';');

            foreach ($hospitalizaciones as $hosp) {
                $dias = $hosp->fecha_ingreso && $hosp->fecha_alta
                    ? ceil($hosp->fecha_ingreso->diffInDays($hosp->fecha_alta))
                    : 0;

                fputcsv($file, [
                    $hosp->id,
                    $hosp->paciente?->nombre ?? 'N/A',
                    $hosp->ci_paciente,
                    $hosp->fecha_ingreso?->format('d/m/Y H:i'),
                    $hosp->fecha_alta?->format('d/m/Y H:i') ?? 'N/A',
                    $dias,
                    $hosp->medico?->user?->name ?? 'N/A',
                    $hosp->diagnostico ?? 'Sin diagnóstico',
                    number_format($hosp->total_estancia ?? 0, 2, ',', '.'),
                    $hosp->fecha_alta ? 'Alta' : 'Activo',
                ], ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * API: Get equipos médicos del paciente
     */
    public function apiEquiposMedicos($id): JsonResponse
    {
        $hospitalizacion = Hospitalizacion::findOrFail($id);

        $equiposMedicos = $hospitalizacion->equipos_medicos ?? [];

        // Procesar equipos de las evoluciones
        $equiposList = [];
        $totalEquipos = 0;

        foreach ($equiposMedicos as $evolucion) {
            if (isset($evolucion['equipos_medicos']) && is_array($evolucion['equipos_medicos'])) {
                foreach ($evolucion['equipos_medicos'] as $equipo) {
                    $equiposList[] = $equipo;
                    $totalEquipos += floatval($equipo['subtotal'] ?? 0);
                }
            }
        }

        return response()->json([
            'success' => true,
            'equipos' => $equiposList,
            'total' => $totalEquipos,
            'count' => count($equiposList)
        ]);
    }
}
