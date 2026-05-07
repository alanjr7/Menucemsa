<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Consulta;
use App\Models\Hospitalizacion;
use App\Models\Caja;
use App\Models\Emergency;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientsController extends Controller
{
    public function index(Request $request): View
    {
        $query = Paciente::with([
                'seguro',
                'triage',
                'registro.user',
                'consultas' => function($q) {
                    $q->with('caja')->orderBy('created_at', 'desc')->limit(1);
                },
                'hospitalizaciones' => function($q) {
                    $q->orderBy('created_at', 'desc')->limit(1);
                },
                'emergencias' => function($q) {
                    $q->orderBy('created_at', 'desc')->limit(1);
                }
            ])
            ->whereHas('registro'); // Solo pacientes con registro

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('ci', 'LIKE', "%{$search}%")
                  ->orWhereHas('registro', function($rq) use ($search) {
                      $rq->where('codigo', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Estado del paciente
        if ($request->filled('estado')) {
            $estado = $request->estado;
            if ($estado === 'hospitalizado') {
                $query->whereHas('hospitalizaciones', function($q) {
                    $q->where('estado', 'Activo');
                });
            } elseif ($estado === 'emergencia') {
                $query->whereHas('emergencias', function($q) {
                    $q->whereIn('status', ['recibido', 'en_evaluacion', 'estabilizado']);
                });
            }
        }

        $pacientes = $query->orderBy('created_at', 'desc')->paginate(15);

        // Obtener pacientes temporales de emergencias (que no están en tabla pacientes)
        $emergencyQuery = Emergency::where('is_temp_id', true)
            ->whereIn('status', ['recibido', 'en_evaluacion', 'estabilizado']);

        // Aplicar búsqueda también a emergencias temporales
        if ($request->filled('search')) {
            $search = $request->search;
            $emergencyQuery->where(function($q) use ($search) {
                $q->where('temp_id', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }

        $pacientesTemporales = $emergencyQuery->orderBy('created_at', 'desc')->get()->map(function($emergency) {
            return (object)[
                'ci' => $emergency->temp_id,
                'nombre' => 'Paciente Temporal - Emergencia',
                'sexo' => null,
                'direccion' => null,
                'telefono' => null,
                'correo' => null,
                'codigo_registro' => null,
                'codigo_seguro' => null,
                'id_triage' => null,
                'is_temporal' => true,
                'emergency_id' => $emergency->id,
                'emergency_code' => $emergency->code,
                'emergency_status' => $emergency->status,
                'created_at' => $emergency->created_at,
                'tipo_ingreso' => $emergency->tipo_ingreso_label,
                'ubicacion_actual' => $emergency->ubicacion_actual,
            ];
        });

        // Combinar colecciones si no hay filtro de estado específico o si es 'emergencia'
        if (!$request->filled('estado') || $request->estado === 'emergencia') {
            // Convertir pacientes paginados a colección y combinar
            $pacientesCollection = collect($pacientes->items());
            $todosPacientes = $pacientesCollection->merge($pacientesTemporales)->sortByDesc('created_at');
            
            // Recrear paginador manualmente
            $page = $request->get('page', 1);
            $perPage = 15;
            $total = $todosPacientes->count();
            $pacientes = new \Illuminate\Pagination\LengthAwarePaginator(
                $todosPacientes->forPage($page, $perPage)->values(),
                $total,
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        }

        // Agregar información de tipo de ingreso a cada paciente
        $pacientes->getCollection()->transform(function($paciente) {
            if (!($paciente instanceof Paciente)) {
                $paciente->tipo_ingreso = $paciente->tipo_ingreso ?? 'emergencia';
                return $paciente;
            }
            $paciente->tipo_ingreso = $this->determinarTipoIngreso($paciente);
            return $paciente;
        });

        // Estadísticas
        $stats = [
            'total' => Paciente::whereHas('registro')->count() + $pacientesTemporales->count(),
            'hospitalizados' => Paciente::whereHas('hospitalizaciones', function($q) {
                $q->where('estado', 'Activo');
            })->count(),
            'emergencias' => Paciente::whereHas('emergencias', function($q) {
                $q->whereIn('status', ['recibido', 'en_evaluacion', 'estabilizado']);
            })->count() + $pacientesTemporales->count(),
            'pagados' => Paciente::whereHas('consultas.caja', function($q) {
                $q->whereNotNull('nro_factura');
            })->count(),
        ];

        return view('patients.index', compact('pacientes', 'stats'));
    }

    private function determinarTipoIngreso(Paciente $paciente): string
    {
        $consulta = $paciente->consultas->first();
        $emergencia = $paciente->emergencias->first();
        $hospitalizacion = $paciente->hospitalizaciones->first();

        $fechaMasReciente = null;
        $tipoIngreso = 'otro';

        if ($consulta) {
            $fechaConsulta = $consulta->created_at ?? $consulta->fecha;
            if ($fechaMasReciente === null || $fechaConsulta > $fechaMasReciente) {
                $fechaMasReciente = $fechaConsulta;
                $tipoIngreso = $consulta->tipo === 'enfermeria' ? 'enfermeria' : 'consulta_externa';
            }
        }

        if ($emergencia) {
            $fechaEmergencia = $emergencia->created_at;
            if ($fechaMasReciente === null || $fechaEmergencia > $fechaMasReciente) {
                $fechaMasReciente = $fechaEmergencia;
                $tipoIngreso = 'emergencia';
            }
        }

        if ($hospitalizacion) {
            $fechaHospitalizacion = $hospitalizacion->created_at ?? $hospitalizacion->fecha_ingreso;
            if ($fechaMasReciente === null || $fechaHospitalizacion > $fechaMasReciente) {
                $fechaMasReciente = $fechaHospitalizacion;
                $tipoIngreso = 'internacion';
            }
        }

        return $tipoIngreso;
    }

    /**
     * Vista de gestión administrativa de pacientes
     */
    public function gestionar(Request $request): View
    {
        // Reutilizar la misma lógica que index() pero con vista diferente
        $query = Paciente::with([
                'seguro',
                'triage',
                'registro.user',
                'consultas' => function($q) {
                    $q->with('caja')->orderBy('created_at', 'desc')->limit(1);
                },
                'hospitalizaciones' => function($q) {
                    $q->orderBy('created_at', 'desc')->limit(1);
                },
                'emergencias' => function($q) {
                    $q->orderBy('created_at', 'desc')->limit(1);
                }
            ])
            ->whereHas('registro');

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('ci', 'LIKE', "%{$search}%")
                  ->orWhereHas('registro', function($rq) use ($search) {
                      $rq->where('codigo', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Estado del paciente
        if ($request->filled('estado')) {
            $estado = $request->estado;
            if ($estado === 'hospitalizado') {
                $query->whereHas('hospitalizaciones', function($q) {
                    $q->where('estado', 'Activo');
                });
            } elseif ($estado === 'emergencia') {
                $query->whereHas('emergencias', function($q) {
                    $q->whereIn('status', ['recibido', 'en_evaluacion', 'estabilizado']);
                });
            }
        }

        $pacientes = $query->orderBy('created_at', 'desc')->paginate(15);

        // Obtener pacientes temporales de emergencias
        $emergencyQuery = Emergency::where('is_temp_id', true)
            ->whereIn('status', ['recibido', 'en_evaluacion', 'estabilizado']);

        // Aplicar búsqueda también a emergencias temporales
        if ($request->filled('search')) {
            $search = $request->search;
            $emergencyQuery->where(function($q) use ($search) {
                $q->where('temp_id', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }

        $pacientesTemporales = $emergencyQuery->orderBy('created_at', 'desc')->get()->map(function($emergency) {
            return (object)[
                'ci' => $emergency->temp_id,
                'nombre' => 'Paciente Temporal - Emergencia',
                'sexo' => null,
                'direccion' => null,
                'telefono' => null,
                'correo' => null,
                'codigo_registro' => null,
                'codigo_seguro' => null,
                'id_triage' => null,
                'is_temporal' => true,
                'emergency_id' => $emergency->id,
                'emergency_code' => $emergency->code,
                'emergency_status' => $emergency->status,
                'created_at' => $emergency->created_at,
                'tipo_ingreso' => $emergency->tipo_ingreso_label,
                'ubicacion_actual' => $emergency->ubicacion_actual,
            ];
        });

        // Combinar colecciones si no hay filtro de estado específico o si es 'emergencia'
        if (!$request->filled('estado') || $request->estado === 'emergencia') {
            $pacientesCollection = collect($pacientes->items());
            $todosPacientes = $pacientesCollection->merge($pacientesTemporales)->sortByDesc('created_at');
            
            $page = $request->get('page', 1);
            $perPage = 15;
            $total = $todosPacientes->count();
            $pacientes = new \Illuminate\Pagination\LengthAwarePaginator(
                $todosPacientes->forPage($page, $perPage)->values(),
                $total,
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        }

        // Agregar información de tipo de ingreso
        $pacientes->getCollection()->transform(function($paciente) {
            if (!($paciente instanceof Paciente)) {
                $paciente->tipo_ingreso = $paciente->tipo_ingreso ?? 'emergencia';
                return $paciente;
            }
            $paciente->tipo_ingreso = $this->determinarTipoIngreso($paciente);
            return $paciente;
        });

        // Estadísticas adicionales para admin
        $stats = [
            'total' => Paciente::whereHas('registro')->count() + $pacientesTemporales->count(),
            'hospitalizados' => Paciente::whereHas('hospitalizaciones', function($q) {
                $q->where('estado', 'Activo');
            })->count(),
            'emergencias' => Paciente::whereHas('emergencias', function($q) {
                $q->whereIn('status', ['recibido', 'en_evaluacion', 'estabilizado']);
            })->count() + $pacientesTemporales->count(),
            'pagados' => Paciente::whereHas('consultas.caja', function($q) {
                $q->whereNotNull('nro_factura');
            })->count(),
            'con_cuentas_pendientes' => \App\Models\CuentaCobro::whereIn('estado', ['pendiente', 'parcial'])->count(),
        ];

        return view('admin.pacientes.gestionar', compact('pacientes', 'stats'));
    }

    public function show($ci): View
    {
        $paciente = Paciente::with([
            'seguro',
            'triage', 
            'registro.user',
            'consultas' => function($q) {
                $q->with(['medico.user', 'especialidad', 'caja'])
                  ->orderBy('fecha', 'desc');
            },
            'emergencias' => function($q) {
                $q->with(['user'])
                  ->orderBy('created_at', 'desc');
            },
            'hospitalizaciones' => function($q) {
                $q->with(['medico.user'])
                  ->orderBy('fecha_ingreso', 'desc');
            }
        ])->findOrFail($ci);

        return view('patients.show', compact('paciente'));
    }

    /**
     * Mostrar formulario para editar paciente
     */
    public function edit($ci): View
    {
        $paciente = Paciente::findOrFail($ci);
        return view('patients.edit', compact('paciente'));
    }

    /**
     * Actualizar información del paciente
     */
    public function update(Request $request, $ci)
    {
        $paciente = Paciente::findOrFail($ci);
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'ci' => 'required|string|max:20|unique:pacientes,ci,' . $ci . ',ci',
            'sexo' => 'required|in:M,F',
            'fecha_nacimiento' => 'nullable|date',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:255',
            'lugar_expedicion' => 'nullable|string|max:100',
            'nacionalidad' => 'nullable|string|max:100',
            'estado_civil' => 'nullable|string|max:50',
            'profesion' => 'nullable|string|max:100',
            'empresa_trabajo' => 'nullable|string|max:255',
            'codigo_seguro' => 'nullable|string|max:50',
        ]);

        $paciente->update($validated);

        return redirect()->route('admin.pacientes.gestionar')
            ->with('success', 'Información del paciente actualizada correctamente.');
    }

    /**
     * Mostrar cuenta del paciente con opción de eliminar items
     */
    public function verCuenta($ci): View
    {
        $paciente = Paciente::findOrFail($ci);
        
        // Obtener cuentas del paciente
        $cuentas = \App\Models\CuentaCobro::with(['detalles', 'pagos'])
            ->where('paciente_ci', $paciente->ci)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.pacientes.cuenta', compact('paciente', 'cuentas'));
    }

    /**
     * Eliminar item de cuenta
     */
    public function eliminarItemCuenta($cuentaId, $detalleId)
    {
        $detalle = \App\Models\CuentaCobroDetalle::findOrFail($detalleId);
        $cuenta = $detalle->cuenta;
        
        // Eliminar el detalle
        $detalle->delete();
        
        // Recalcular total de la cuenta
        $nuevoTotal = $cuenta->detalles()->sum('subtotal');
        $cuenta->update(['total' => $nuevoTotal]);
        
        return redirect()->back()
            ->with('success', 'Item eliminado correctamente.');
    }
}
