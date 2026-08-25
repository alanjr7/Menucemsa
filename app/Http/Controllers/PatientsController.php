<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Consulta;
use App\Models\Hospitalizacion;
use App\Models\Caja;
use App\Models\Emergency;
use App\Models\AltaPaciente;
use App\Models\Seguro;
use App\Models\Registro;
use App\Services\EpisodioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            ->where(function($q) {
                $q->whereHas('episodioAbierto')
                  ->orWhereHas('emergencias', function($eq) {
                      $eq->whereIn('status', ['recibido', 'en_evaluacion', 'estabilizado']);
                  });
            });

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('ci', 'LIKE', "%{$search}%")
                  ->orWhere('temp_code', 'LIKE', "%{$search}%")
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

        // Temp patients are now real Paciente records with is_temp=true — no separate query needed
        $pacientesTemporales = collect();

        // Combinar colecciones si no hay filtro de estado específico o si es 'emergencia'
        if (!$request->filled('estado') || $request->estado === 'emergencia') {
            $dbTotal  = $pacientes->total();
            $page     = $request->get('page', 1);
            $perPage  = 15;

            // Temporales solo se muestran en página 1 y se restan del cupo
            $pageItems = collect($pacientes->items());
            if ($page == 1) {
                $pageItems = $pacientesTemporales->merge($pageItems)->sortByDesc('created_at')->values();
            }

            $pacientes = new \Illuminate\Pagination\LengthAwarePaginator(
                $pageItems,
                $dbTotal + $pacientesTemporales->count(),
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
            'total' => Paciente::where(function($q) {
                $q->whereHas('episodioAbierto')->orWhereHas('emergencias', function($eq) {
                    $eq->whereIn('status', ['recibido', 'en_evaluacion', 'estabilizado']);
                });
            })->count(),
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

    public static function determinarTipoIngreso(Paciente $paciente): string
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
                  ->orWhere('temp_code', 'LIKE', "%{$search}%")
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

        $pacientesTemporales = collect();

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
            'total' => Paciente::where(function($q) {
                $q->whereHas('episodioAbierto')->orWhereHas('emergencias', function($eq) {
                    $eq->whereIn('status', ['recibido', 'en_evaluacion', 'estabilizado']);
                });
            })->count(),
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

    public function show($id): View
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
        ])->findOrFail($id);

        return view('patients.show', compact('paciente'));
    }

    public function print($id): View
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
                $q->with(['user', 'medicamentos', 'equiposMedicos'])
                  ->orderBy('created_at', 'desc');
            },
            'hospitalizaciones' => function($q) {
                $q->with(['medico.user', 'medicamentos', 'equiposMedicos', 'habitacion'])
                  ->orderBy('fecha_ingreso', 'desc');
            }
        ])->findOrFail($id);

        // Obtener información del garante si existe
        $garante = null;
        if ($paciente->registro && $paciente->registro->garante_ci) {
            $garante = Paciente::where('ci', $paciente->registro->garante_ci)->first();
        }

        return view('patients.print', compact('paciente', 'garante'));
    }

    /**
     * Mostrar formulario para editar paciente (regular o temporal)
     */
    public function edit(Request $request, $id): View
    {
        $paciente = Paciente::with(['seguro', 'triage', 'registro.user'])->findOrFail($id);
        $seguros = Seguro::all();
        $backUrl = $request->query('back_url') ?: $this->urlVolverPaciente($paciente->id);
        return view('patients.edit', compact('paciente', 'seguros', 'backUrl'));
    }

    /**
     * Destino de "volver" tras ver/editar un paciente según el rol:
     * admin/administrador vuelven a la gestión o ficha; internación a la ficha/dashboard; recepción a su listado.
     */
    private function urlVolverPaciente(?int $pacienteId = null): string
    {
        $user = auth()->user();
        if ($user && in_array($user->role, ['internacion', 'enfermera-internacion'], true)) {
            return $pacienteId ? route('patients.show', $pacienteId) : route('internacion-staff.dashboard');
        }
        if ($user && in_array($user->role, ['admin', 'administrador'], true)) {
            return $pacienteId ? route('patients.show', $pacienteId) : route('admin.pacientes.gestionar');
        }
        return route('reception.pacientes.index');
    }

    /**
     * Actualizar información del paciente (incluyendo pacientes temporales)
     */
    public function update(Request $request, $id)
    {
        $paciente = Paciente::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'ci' => 'nullable|integer|unique:pacientes,ci,' . $id,
            'sexo' => 'nullable|in:M,F',
            'fecha_nacimiento' => 'nullable|date',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:255',
            'lugar_expedicion' => 'nullable|string|max:2',
            'nacionalidad' => 'nullable|string|max:100',
            'estado_civil' => 'nullable|string|max:50',
            'profesion' => 'nullable|string|max:100',
            'empresa_trabajo' => 'nullable|string|max:255',
            'seguro_id' => 'nullable|exists:seguros,id',
            'seguro_poliza' => 'nullable|string|max:100',
            'seguro_vigencia_desde' => 'nullable|date',
            'seguro_vigencia_hasta' => 'nullable|date',
        ]);

        if (empty($validated['sexo'])) {
            $validated['sexo'] = $paciente->sexo ?: 'M';
        }

        // Si se le asigna un CI oficial y era temporal, actualizar estado a paciente regular
        if (!empty($validated['ci']) && $paciente->is_temp) {
            $validated['is_temp'] = false;
        }

        // Si es un paciente regular y no tiene registro_codigo asignado, generarlo automáticamente
        if (empty($paciente->registro_codigo) && !($validated['is_temp'] ?? $paciente->is_temp)) {
            $sexoCodigo = ($validated['sexo'] ?? $paciente->sexo ?? 'M') === 'F' ? 'F' : 'M';
            $nombre     = $validated['nombre'] ?? $paciente->nombre;
            $fechaNac   = $validated['fecha_nacimiento'] ?? $paciente->fecha_nacimiento;

            $registroCodigo = Registro::generarCodigo([
                'fecha_nacimiento' => $fechaNac,
                'sexo'             => $sexoCodigo,
                'nombre'           => $nombre,
            ]);

            Registro::firstOrCreate(
                ['codigo' => $registroCodigo],
                [
                    'fecha'   => now()->toDateString(),
                    'hora'    => now()->toTimeString(),
                    'motivo'  => 'Registro generado al actualizar datos del paciente',
                    'user_id' => Auth::id() ?? 1,
                ]
            );

            $validated['registro_codigo'] = $registroCodigo;
        }

        $paciente->update($validated);

        $backUrl = $request->input('back_url') ?: $this->urlVolverPaciente($paciente->id);

        return redirect()->to($backUrl)
            ->with('success', 'Información del paciente ' . ($paciente->is_temp ? '(Temporal) ' : '') . 'actualizada correctamente.');
    }

    /**
     * Mostrar cuenta del paciente con opción de eliminar items
     */
    public function verCuenta($id): View
    {
        $paciente = Paciente::findOrFail($id);

        $cuentas = \App\Models\CuentaCobro::with(['detalles', 'pagos'])
            ->where('paciente_id', $paciente->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Historial de anulaciones (auditoría: hora / quién / qué / cuánto), agrupado
        // por cuenta. Incluye la línea viva para decidir si se puede revertir.
        $eliminados = \App\Models\CuentaCobroDetalleEliminado::with(['usuarioEliminacion', 'revertidoPor', 'detalle'])
            ->whereIn('cuenta_cobro_id', $cuentas->pluck('id'))
            ->orderBy('eliminado_en', 'desc')
            ->get()
            ->groupBy('cuenta_cobro_id');

        return view('admin.pacientes.cuenta', compact('paciente', 'cuentas', 'eliminados'));
    }

    // La eliminación de ítems de cuenta se unificó en
    // App\Http\Controllers\Admin\AjusteCargoController (anular/revertir),
    // fuente única de eliminaciones seguras con soporte de cantidades parciales.

    /**
     * Vista de listado de pacientes para dar de alta (roles autorizados)
     */
    public function darDeAltaIndex(Request $request): View
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
                },
                'cuentasPendientes',
            ])
            ->whereHas('registro')
            ->whereHas('episodioAbierto'); // Solo pacientes con episodio activo

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('ci', 'LIKE', "%{$search}%")
                  ->orWhere('temp_code', 'LIKE', "%{$search}%")
                  ->orWhereHas('registro', function($rq) use ($search) {
                      $rq->where('codigo', 'LIKE', "%{$search}%");
                  });
            });
        }

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

        // Agregar tipo_ingreso
        $pacientes->getCollection()->transform(function($paciente) {
            $paciente->tipo_ingreso = $this->determinarTipoIngreso($paciente);
            return $paciente;
        });

        $stats = [
            'total'          => Paciente::whereHas('episodioAbierto')->count(),
            'hospitalizados' => Paciente::whereHas('episodioAbierto')->whereHas('hospitalizaciones', function($q) {
                $q->where('estado', 'activo');
            })->count(),
            'emergencias'    => Paciente::whereHas('episodioAbierto')->whereHas('emergencias', function($q) {
                $q->whereIn('status', ['recibido', 'en_evaluacion', 'estabilizado']);
            })->count(),
        ];

        return view('patients.dar-de-alta', compact('pacientes', 'stats'));
    }

    /**
     * Procesar el alta de un paciente
     */
    public function darDeAlta(Request $request, $id)
    {
        $paciente = Paciente::with('cuentasPendientes')->whereHas('registro')->findOrFail($id);

        // Verificar cuentas pendientes
        $cuentasPendientes = $paciente->cuentasPendientes()->count();
        if ($cuentasPendientes > 0) {
            return redirect()->back()->with(
                'error',
                "El paciente {$paciente->nombre} tiene {$cuentasPendientes} cuenta(s) pendiente(s) de pago. Debe regularizarlas antes de dar el alta."
            );
        }

        $validated = $request->validate([
            'motivo_alta'   => 'required|in:alta_medica,voluntaria,fallecimiento,traslado',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($paciente, $validated) {
            AltaPaciente::create([
                'paciente_id'       => $paciente->id,
                'dado_de_alta_por'  => auth()->id(),
                'motivo_alta'       => $validated['motivo_alta'],
                'observaciones'     => $validated['observaciones'] ?? null,
                'fecha_alta'        => now(),
            ]);

            EpisodioService::cerrarEpisodioDelPaciente(
                $paciente->id,
                auth()->id(),
                $validated['motivo_alta']
            );

            // Sincronizar el estado de la internación con el alta. La fuente de verdad
            // del alta es AltaPaciente; aquí cerramos la hospitalización activa para que
            // los conteos de "internación activa" y los KPIs reflejen el egreso (antes
            // quedaban en estado 'activo' para siempre porque nadie los cerraba).
            Hospitalizacion::where('paciente_id', $paciente->id)
                ->whereNull('fecha_alta')
                ->where('estado', '!=', 'trasladado')
                ->update([
                    'fecha_alta' => now(),
                    'estado'     => $validated['motivo_alta'] === 'traslado' ? 'trasladado' : 'alta',
                ]);
        });

        return redirect()->route('patients.dar-de-alta.index')
            ->with('success', "Paciente {$paciente->nombre} dado de alta correctamente.");
    }

    /**
     * Historial de pacientes dados de alta (solo admin/administrador)
     */
    public function historialAltas(Request $request): View
    {
        $query = AltaPaciente::with(['paciente.seguro', 'usuario'])
            ->orderBy('fecha_alta', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('paciente', function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('ci', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('motivo')) {
            $query->where('motivo_alta', $request->motivo);
        }

        if ($request->filled('desde')) {
            $query->whereDate('fecha_alta', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('fecha_alta', '<=', $request->hasta);
        }

        $altas = $query->paginate(20);

        $statsAltas = [
            'total'        => AltaPaciente::count(),
            'este_mes'     => AltaPaciente::whereMonth('fecha_alta', now()->month)
                                ->whereYear('fecha_alta', now()->year)->count(),
            'alta_medica'  => AltaPaciente::where('motivo_alta', 'alta_medica')->count(),
            'voluntaria'   => AltaPaciente::where('motivo_alta', 'voluntaria')->count(),
        ];

        return view('patients.historial-altas', compact('altas', 'statsAltas'));
    }
}
