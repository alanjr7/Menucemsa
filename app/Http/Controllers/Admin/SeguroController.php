<?php

namespace App\Http\Controllers\Admin;

use App\Exports\HistorialSegurosExport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Seguro;
use App\Models\Paciente;
use App\Models\CuentaCobro;
use App\Models\Emergency;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class SeguroController extends Controller
{
    /**
     * Vista principal de gestión de seguros
     */
    public function index()
    {
        $seguros = Seguro::all();
        $totalAfiliados = Paciente::select('seguro_id')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('seguro_id')
            ->pluck('total', 'seguro_id');

        // Pre-autorizaciones pendientes (cuentas por cobrar con seguro pendiente de autorización)
        $preautorizaciones = CuentaCobro::with(['paciente', 'seguro'])
            ->whereNotNull('seguro_id')
            ->where('seguro_estado', 'pendiente_autorizacion')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($cuenta) {
                return [
                    'id' => $cuenta->id,
                    'numero' => $cuenta->id,
                    'fecha' => $cuenta->created_at->format('Y-m-d'),
                    'paciente' => $cuenta->paciente?->nombre ?? 'N/A',
                    'paciente_ci' => $cuenta->paciente?->ci ?? $cuenta->paciente?->temp_code,
                    'seguro' => $cuenta->seguro?->nombre_empresa ?? 'Sin seguro',
                    'seguro_id' => $cuenta->seguro_id,
                    'tipo_cobertura' => $cuenta->seguro?->descripcion_cobertura ?? 'No definida',
                    'servicio' => $cuenta->tipo_atencion_label,
                    'monto' => $cuenta->total_calculado,
                    'estado' => $cuenta->seguro_estado,
                    'estado_label' => 'Pendiente de Autorización',
                ];
            });

        // Autorizaciones resueltas (aceptadas / rechazadas): historial embebido con
        // búsqueda, exportación e impresión. Lista liviana para la tabla; el detalle
        // completo se carga por endpoint (Ver / Imprimir) desde la misma fuente única.
        $autorizaciones = CuentaCobro::with(['paciente', 'seguro', 'seguroAutorizadoPor'])
            ->whereNotNull('seguro_id')
            ->whereIn('seguro_estado', ['autorizado', 'rechazado'])
            ->orderByDesc('seguro_fecha_autorizacion')
            ->limit(300)
            ->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'fecha' => optional($c->seguro_fecha_autorizacion)->format('d/m/Y H:i') ?? $c->created_at->format('d/m/Y H:i'),
                'paciente' => $c->paciente?->nombre ?? 'N/A',
                'paciente_ci' => $c->paciente?->ci ?? $c->paciente?->temp_code ?? '—',
                'seguro' => $c->seguro?->nombre_empresa ?? 'N/A',
                'servicio' => $c->tipo_atencion_label,
                'monto_total' => (float) $c->total_calculado,
                'cobertura' => (float) $c->seguro_monto_cobertura,
                'copago' => (float) $c->seguro_monto_paciente,
                'estado' => $c->seguro_estado,
                'autorizado_por' => $c->seguroAutorizadoPor?->name ?? '—',
            ]);

        // Estadísticas
        $stats = [
            'pendientes' => $preautorizaciones->count(),
            'en_proceso' => CuentaCobro::where('seguro_estado', 'autorizado')
                ->where('estado', 'parcial')
                ->count(),
            'aprobadas' => CuentaCobro::where('seguro_estado', 'autorizado')
                ->whereIn('estado', ['pagado', 'parcial'])
                ->count(),
            'rechazadas' => CuentaCobro::where('seguro_estado', 'rechazado')->count(),
            // Cobertura total que asumen las aseguradoras en las cuentas autorizadas (Bs).
            'monto_total' => CuentaCobro::where('seguro_estado', 'autorizado')->sum('seguro_monto_cobertura'),
        ];

        return view('admin.seguros', compact('seguros', 'totalAfiliados', 'preautorizaciones', 'stats', 'autorizaciones'));
    }

    /**
     * Fuente única del detalle COMPLETO de una autorización de seguro (paciente + póliza +
     * vigencia + datos de la aseguradora + autorización + cargos). La usan tanto el modal
     * "Ver" (JSON) como la hoja imprimible, para no duplicar la armada de datos.
     */
    private function datosAutorizacion(CuentaCobro $cuenta): array
    {
        $cuenta->loadMissing(['paciente.seguro', 'seguro', 'seguroAutorizadoPor', 'detalles', 'seguroCobros']);
        $p = $cuenta->paciente;
        $s = $cuenta->seguro;
        $cobro = $cuenta->seguroCobros->firstWhere('estado', '!=', 'anulado');

        return [
            'cuenta' => [
                'id' => $cuenta->id,
                'servicio' => $cuenta->tipo_atencion_label,
                'fecha' => $cuenta->created_at->format('d/m/Y H:i'),
                'monto_total' => number_format($cuenta->total_calculado, 2),
                'estado' => $cuenta->seguro_estado,
                'estado_label' => $cuenta->seguro_estado === 'autorizado' ? 'Autorizado' : 'Rechazado',
            ],
            'paciente' => [
                'nombre' => $p?->nombre ?? 'N/A',
                'ci' => $p?->ci ?? $p?->temp_code ?? '—',
                'sexo' => $p?->sexo ?? '—',
                'telefono' => $p?->telefono ?? '—',
                'direccion' => $p?->direccion ?? '—',
            ],
            'seguro' => [
                'nombre' => $s?->nombre_empresa ?? 'N/A',
                'nit' => $s?->nit ?: '—',
                'tipo' => $s?->tipo ?? '—',
                'tipo_cobertura' => $s?->descripcion_cobertura ?? '—',
                'telefono' => $s?->telefono ?: '—',
                'poliza' => $p?->seguro_poliza ?: '—',
                'vigencia_desde' => optional($p?->seguro_vigencia_desde)->format('d/m/Y') ?? '—',
                'vigencia_hasta' => optional($p?->seguro_vigencia_hasta)->format('d/m/Y') ?? '—',
            ],
            'autorizacion' => [
                'estado' => $cuenta->seguro_estado,
                'estado_label' => $cuenta->seguro_estado === 'autorizado' ? 'Autorizado' : 'Rechazado',
                'nro_autorizacion' => $cuenta->seguro_nro_autorizacion ?: '—',
                'cobertura' => number_format($cuenta->seguro_monto_cobertura ?? 0, 2),
                'copago' => number_format($cuenta->seguro_monto_paciente ?? 0, 2),
                'autorizado_por' => $cuenta->seguroAutorizadoPor?->name ?? '—',
                'fecha' => optional($cuenta->seguro_fecha_autorizacion)->format('d/m/Y H:i') ?? '—',
                'observaciones' => $cuenta->seguro_observaciones ?: '—',
                'venta_id' => $cobro?->id ?? '—',
                'debito_fiscal' => $cobro ? number_format($cobro->debito_fiscal, 2) : '0.00',
                'estado_cobro' => $cobro?->estado_label ?? '—',
            ],
            'cargos' => $cuenta->detalles->whereNull('deshabilitado_en')->map(fn ($d) => [
                'descripcion' => $d->descripcion,
                'cantidad' => $d->cantidad,
                'subtotal' => number_format($d->subtotal, 2),
            ])->values()->all(),
        ];
    }

    /** Detalle completo de una autorización en JSON (modal "Ver"). */
    public function verAutorizacion(string $cuentaId): JsonResponse
    {
        $cuenta = CuentaCobro::whereNotNull('seguro_id')
            ->whereIn('seguro_estado', ['autorizado', 'rechazado'])
            ->findOrFail($cuentaId);

        return response()->json(['success' => true, 'datos' => $this->datosAutorizacion($cuenta)]);
    }

    /** Hoja imprimible con toda la información del seguro de una autorización. */
    public function imprimirAutorizacion(string $cuentaId)
    {
        $cuenta = CuentaCobro::whereNotNull('seguro_id')
            ->whereIn('seguro_estado', ['autorizado', 'rechazado'])
            ->findOrFail($cuentaId);

        return view('admin.seguros-autorizacion-print', ['d' => $this->datosAutorizacion($cuenta)]);
    }

    public function cambiarEstado(Request $request, Seguro $seguro): JsonResponse
    {
        try {
            // Invertimos el estado actual
            $nuevoEstado = $seguro->estado === 'activo' ? 'inactivo' : 'activo';
            $seguro->update(['estado' => $nuevoEstado]);

            return response()->json([
                'success' => true,
                'message' => "Estado actualizado a " . strtoupper($nuevoEstado),
                'nuevo_estado' => $nuevoEstado
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    /**
     * API: Listar todos los seguros
     */
    public function apiIndex(): JsonResponse
    {
        $seguros = Seguro::all()->map(function ($seguro) {
            return [
                'id' => $seguro->id,
                'nombre_empresa' => $seguro->nombre_empresa,
                'tipo' => $seguro->tipo,
                'telefono' => $seguro->telefono,
                'formulario' => $seguro->formulario,
                'estado' => $seguro->estado,
                'total_afiliados' => Paciente::where('seguro_id', $seguro->id)->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'seguros' => $seguros,
        ]);
    }

    /**
     * API: Crear nuevo seguro
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nombre_empresa' => 'required|string|max:255|unique:seguros,nombre_empresa',
                'tipo' => 'required|string|max:100',
                'nit' => 'nullable|string|max:20',
                'telefono' => 'nullable|string|max:50',
                'formulario' => 'nullable|string|max:100',
                'tipo_cobertura' => 'required|in:porcentaje,solo_consulta,tope_monto',
                'cobertura_porcentaje' => 'nullable|numeric|decimal:0,2|min:0|max:100',
                'copago_porcentaje' => 'nullable|numeric|decimal:0,2|min:0|max:100',
                'tope_monto' => 'nullable|numeric|decimal:0,2|min:0',
            ]);

            $validated = $this->prepararCobertura($validated);
            $validated['estado'] = 'activo';

            $seguro = Seguro::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Seguro creado exitosamente',
                'seguro' => $seguro,
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->validator->errors()->first()], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear seguro: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Normaliza y valida la cobertura según el tipo: porcentaje exige cobertura% y que
     * cobertura+copago = 100 (deriva el copago si falta); tope exige el monto; cada tipo
     * limpia los campos que no le aplican para no dejar datos contradictorios.
     */
    private function prepararCobertura(array $data): array
    {
        $tipo = $data['tipo_cobertura'] ?? null;

        if ($tipo === 'porcentaje') {
            $cobertura = $data['cobertura_porcentaje'] ?? null;
            if ($cobertura === null || $cobertura === '') {
                throw ValidationException::withMessages(['cobertura_porcentaje' => 'Indica el % de cobertura.']);
            }
            $copago = $data['copago_porcentaje'] ?? null;
            if ($copago === null || $copago === '') {
                $copago = bcsub('100', (string) $cobertura, 2); // copago = 100 − cobertura
            }
            if (bccomp(bcadd((string) $cobertura, (string) $copago, 2), '100.00', 2) !== 0) {
                throw ValidationException::withMessages(['copago_porcentaje' => 'La cobertura y el copago deben sumar 100%.']);
            }
            $data['cobertura_porcentaje'] = $cobertura;
            $data['copago_porcentaje'] = $copago;
            $data['tope_monto'] = null;
        } elseif ($tipo === 'tope_monto') {
            if (empty($data['tope_monto'])) {
                throw ValidationException::withMessages(['tope_monto' => 'Indica el tope de monto.']);
            }
            $data['cobertura_porcentaje'] = null;
            $data['copago_porcentaje'] = null;
        } elseif ($tipo === 'solo_consulta') {
            $data['cobertura_porcentaje'] = null;
            $data['copago_porcentaje'] = null;
            $data['tope_monto'] = null;
        }

        return $data;
    }

    /**
     * API: Actualizar seguro
     */
    public function update(Request $request, Seguro $seguro): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nombre_empresa' => 'required|string|max:255|unique:seguros,nombre_empresa,' . $seguro->id,
                'tipo' => 'required|string|max:100',
                'nit' => 'nullable|string|max:20',
                'telefono' => 'nullable|string|max:50',
                'formulario' => 'nullable|string|max:100',
                'estado' => 'nullable|in:activo,inactivo',
                'tipo_cobertura' => 'nullable|in:porcentaje,solo_consulta,tope_monto',
                'cobertura_porcentaje' => 'nullable|numeric|decimal:0,2|min:0|max:100',
                'copago_porcentaje' => 'nullable|numeric|decimal:0,2|min:0|max:100',
                'tope_monto' => 'nullable|numeric|decimal:0,2|min:0',
            ]);

            $validated = $this->prepararCobertura($validated);
            $seguro->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Seguro actualizado exitosamente',
                'seguro' => $seguro,
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->validator->errors()->first()], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar seguro: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: Eliminar seguro
     */
    public function destroy(Seguro $seguro): JsonResponse
    {
        try {
            // Verificar si hay pacientes afiliados
            $afiliados = Paciente::where('seguro_id', $seguro->id)->count();
            if ($afiliados > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "No se puede eliminar. Tiene {$afiliados} pacientes afiliados.",
                ], 422);
            }

            $seguro->delete();

            return response()->json([
                'success' => true,
                'message' => 'Seguro eliminado exitosamente',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar seguro: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: Obtener detalle de seguro con afiliados
     */
    public function show(Seguro $seguro): JsonResponse
    {
        $pacientes = Paciente::where('seguro_id', $seguro->id)
            ->limit(50)
            ->get(['ci', 'nombre', 'telefono']);

        return response()->json([
            'success' => true,
            'seguro' => $seguro,
            'total_afiliados' => Paciente::where('seguro_id', $seguro->id)->count(),
            'pacientes' => $pacientes,
        ]);
    }

    /**
     * API: Obtener pre-autorizaciones
     */
    public function getPreautorizaciones(): JsonResponse
    {
        $preautorizaciones = CuentaCobro::with(['paciente', 'paciente.seguro'])
            ->whereHas('paciente', function ($q) {
                $q->whereNotNull('seguro_id');
            })
            ->whereIn('estado', ['pendiente', 'parcial'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($cuenta) {
                return [
                    'id' => $cuenta->id,
                    'numero' => $cuenta->id,
                    'fecha' => $cuenta->created_at->format('Y-m-d'),
                    'paciente' => $cuenta->paciente?->nombre ?? 'N/A',
                    'paciente_ci' => $cuenta->paciente?->ci ?? $cuenta->paciente?->temp_code,
                    'seguro' => $cuenta->paciente?->seguro?->nombre_empresa ?? 'Sin seguro',
                    'seguro_id' => $cuenta->paciente?->seguro_id,
                    'servicio' => $cuenta->tipo_atencion_label,
                    'monto' => $cuenta->total_calculado,
                    'saldo' => $cuenta->saldo_pendiente,
                    'estado' => $cuenta->estado,
                    'estado_label' => $cuenta->estado_label,
                ];
            });

        return response()->json([
            'success' => true,
            'preautorizaciones' => $preautorizaciones,
        ]);
    }

    /**
     * API: Cambiar estado de pre-autorización con cálculo de copago
     */
    public function cambiarEstadoPreautorizacion(Request $request, string $cuentaId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'estado' => 'required|in:autorizado,rechazado',
                'observaciones' => 'nullable|string|max:500',
                'nro_autorizacion' => 'nullable|string|max:60',
            ]);

            $cuenta = CuentaCobro::with(['seguro', 'paciente'])->findOrFail($cuentaId);

            if ($validated['estado'] === 'autorizado') {
                $seguro = $cuenta->seguro;

                // Fuente única: autoriza, congela el snapshot y crea la venta devengada
                // a la aseguradora (cuenta por cobrar + débito fiscal IVA).
                $resultado = $cuenta->autorizarSeguro($seguro, (float) $cuenta->total_calculado, [
                    'observaciones' => $validated['observaciones'] ?? null,
                    'nro_autorizacion' => $validated['nro_autorizacion'] ?? null,
                ]);

                $mensaje = $resultado['paciente'] > 0
                    ? 'Seguro autorizado. El paciente debe pagar Bs. ' . number_format($resultado['paciente'], 2) . ' en caja.'
                    : 'Seguro autorizado. Cobertura completa.';

                $this->registrarEnHistorialMedico($cuenta, 'autorizado', $validated['observaciones'] ?? null);
            } else {
                $cuenta->update([
                    'seguro_estado' => 'rechazado',
                    'seguro_autorizado_por' => auth()->id(),
                    'seguro_fecha_autorizacion' => now(),
                    'seguro_observaciones' => $validated['observaciones'] ?? null,
                ]);

                $mensaje = 'Seguro rechazado. El paciente debe pagar el total en caja.';
                $this->registrarEnHistorialMedico($cuenta, 'rechazado', $validated['observaciones'] ?? null);
            }

            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'cuenta' => [
                    'id' => $cuenta->id,
                    'seguro_estado' => $cuenta->seguro_estado,
                    'monto_cubierto' => $cuenta->seguro_monto_cobertura,
                    'monto_paciente' => $cuenta->seguro_monto_paciente,
                    'estado' => $cuenta->estado,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Registrar autorización en historial médico del paciente
     */
    private function registrarEnHistorialMedico(CuentaCobro $cuenta, string $estado, ?string $observaciones): void
    {
        $tipoAtencion = $cuenta->tipo_atencion_label;
        $seguroNombre = $cuenta->seguro?->nombre_empresa ?? 'Seguro';

        if ($estado === 'autorizado') {
            $montoCubierto = number_format($cuenta->seguro_monto_cobertura, 2);
            $montoPaciente = number_format($cuenta->seguro_monto_paciente, 2);
            $descripcion = "Autorización de seguro {$seguroNombre} para {$tipoAtencion}. ";
            $descripcion .= "Monto cubierto: Bs. {$montoCubierto}. ";
            if ($cuenta->seguro_monto_paciente > 0) {
                $descripcion .= "Copago paciente: Bs. {$montoPaciente}. ";
            }
        } else {
            $descripcion = "Rechazo de seguro {$seguroNombre} para {$tipoAtencion}. ";
            $descripcion .= "Motivo: {$observaciones}. ";
        }

        // All patients are now in the pacientes table (including temp ones)
        if ($cuenta->paciente_id) {
            \App\Models\HistorialMedico::create([
                'paciente_id'    => $cuenta->paciente_id,
                'fecha'          => now()->toDateString(),
                'detalle'        => $descripcion,
                'observaciones'  => $observaciones,
                'user_medico_id' => auth()->id(),
            ]);
        }
    }

    /**
     * Vista de historial de seguros
     */
    public function historial(Request $request)
    {
        $query = CuentaCobro::with(['paciente', 'seguro', 'seguroAutorizadoPor', 'detalles'])
            ->whereNotNull('seguro_id')
            ->whereIn('seguro_estado', ['autorizado', 'rechazado']);

        // Filtros
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }
        if ($request->filled('paciente')) {
            $query->whereHas('paciente', function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->paciente . '%')
                    ->orWhere('ci', 'like', '%' . $request->paciente . '%');
            });
        }
        if ($request->filled('seguro_id')) {
            $query->where('seguro_id', $request->seguro_id);
        }
        if ($request->filled('estado')) {
            $query->where('seguro_estado', $request->estado);
        }

        $historial = $query->orderBy('created_at', 'desc')->paginate(15);

        // Calcular estadísticas
        $stats = [
            'total_autorizados' => CuentaCobro::where('seguro_estado', 'autorizado')
                ->when($request->filled('fecha_inicio'), fn($q) => $q->whereDate('created_at', '>=', $request->fecha_inicio))
                ->when($request->filled('fecha_fin'), fn($q) => $q->whereDate('created_at', '<=', $request->fecha_fin))
                ->count(),
            'total_rechazados' => CuentaCobro::where('seguro_estado', 'rechazado')
                ->when($request->filled('fecha_inicio'), fn($q) => $q->whereDate('created_at', '>=', $request->fecha_inicio))
                ->when($request->filled('fecha_fin'), fn($q) => $q->whereDate('created_at', '<=', $request->fecha_fin))
                ->count(),
            'monto_total_cubierto' => CuentaCobro::where('seguro_estado', 'autorizado')
                ->when($request->filled('fecha_inicio'), fn($q) => $q->whereDate('created_at', '>=', $request->fecha_inicio))
                ->when($request->filled('fecha_fin'), fn($q) => $q->whereDate('created_at', '<=', $request->fecha_fin))
                ->sum('seguro_monto_cobertura') ?? 0,
            'monto_total_paciente' => CuentaCobro::where('seguro_estado', 'autorizado')
                ->when($request->filled('fecha_inicio'), fn($q) => $q->whereDate('created_at', '>=', $request->fecha_inicio))
                ->when($request->filled('fecha_fin'), fn($q) => $q->whereDate('created_at', '<=', $request->fecha_fin))
                ->sum('seguro_monto_paciente') ?? 0,
        ];

        $seguros = Seguro::where('estado', 'activo')->orderBy('nombre_empresa')->get();

        return view('admin.seguros-historial', compact('historial', 'stats', 'seguros'));
    }

    public function exportarExcel(Request $request)
    {
        $filtros = $request->only(['fecha_inicio', 'fecha_fin', 'paciente', 'seguro_id', 'estado']);
        $nombreArchivo = 'historial_seguros_' . now()->format('Y-m-d_His') . '.xlsx';

        // Ahora Excel::download funcionará porque viene del Facade
        return Excel::download(new \App\Exports\HistorialSegurosExport($filtros), $nombreArchivo);
    }
    /**
     * Exportar historial de seguros a Excel
     */
    public function exportarHistorial(Request $request)
    {
        $query = CuentaCobro::with(['paciente', 'seguro', 'seguroAutorizadoPor', 'detalles'])
            ->whereNotNull('seguro_id')
            ->whereIn('seguro_estado', ['autorizado', 'rechazado']);

        // Aplicar mismos filtros
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }
        if ($request->filled('paciente')) {
            $query->whereHas('paciente', function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->paciente . '%')
                    ->orWhere('ci', 'like', '%' . $request->paciente . '%');
            });
        }
        if ($request->filled('seguro_id')) {
            $query->where('seguro_id', $request->seguro_id);
        }
        if ($request->filled('estado')) {
            $query->where('seguro_estado', $request->estado);
        }

        $registros = $query->orderBy('created_at', 'desc')->get();

        $filename = 'historial_seguros_' . now()->format('YmdHis') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($registros) {
            $file = fopen('php://output', 'w');

            // Cabeceras
            fputcsv($file, [
                'Fecha',
                'Paciente',
                'CI',
                'Seguro',
                'Tipo Atención',
                'Monto Total',
                'Cobertura Seguro',
                'Copago Paciente',
                'Estado',
                'Autorizado Por',
                'Fecha Autorización',
                'Observaciones',
                'Cargos Detallados'
            ]);

            foreach ($registros as $registro) {
                // Agrupar cargos por tipo
                $cargosPorTipo = [];
                foreach ($registro->detalles as $detalle) {
                    $tipo = $detalle->tipo_item_label;
                    if (!isset($cargosPorTipo[$tipo])) {
                        $cargosPorTipo[$tipo] = [];
                    }
                    $cargosPorTipo[$tipo][] = "{$detalle->descripcion} (Bs. {$detalle->subtotal})";
                }

                $cargosTexto = [];
                foreach ($cargosPorTipo as $tipo => $items) {
                    $cargosTexto[] = "$tipo: " . implode(', ', $items);
                }

                fputcsv($file, [
                    $registro->created_at->format('d/m/Y H:i'),
                    $registro->paciente?->nombre ?? 'N/A',
                    $registro->paciente?->ci ?? $registro->paciente?->temp_code,
                    $registro->seguro?->nombre_empresa ?? 'N/A',
                    $registro->tipo_atencion_label,
                    number_format($registro->total_calculado, 2),
                    number_format($registro->seguro_monto_cobertura ?? 0, 2),
                    number_format($registro->seguro_monto_paciente ?? 0, 2),
                    ucfirst($registro->seguro_estado),
                    $registro->seguroAutorizadoPor?->name ?? 'N/A',
                    $registro->seguro_fecha_autorizacion?->format('d/m/Y H:i') ?? 'N/A',
                    $registro->seguro_observaciones ?? '',
                    implode(' | ', $cargosTexto)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
