<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Consulta;
use App\Models\Emergencia;
use App\Models\Hospitalizacion;
use App\Models\Caja;
use App\Models\Emergency;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientsController extends Controller
{
    public function index(Request $request): View
    {
        $query = Paciente::with(['seguro', 'triage', 'registro.usuario'])
            ->whereHas('registro'); // Solo pacientes con registro

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('ci', 'LIKE', "%{$search}%")
                  ->orWhere('codigo_registro', 'LIKE', "%{$search}%");
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
                    $q->where('estado', 'Activo');
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

        // Estadísticas
        $stats = [
            'total' => Paciente::whereHas('registro')->count() + $pacientesTemporales->count(),
            'hospitalizados' => Paciente::whereHas('hospitalizaciones', function($q) {
                $q->where('estado', 'Activo');
            })->count(),
            'emergencias' => Paciente::whereHas('emergencias', function($q) {
                $q->where('estado', 'Activo');
            })->count() + $pacientesTemporales->count(),
            'pagados' => Paciente::whereHas('consultas.caja', function($q) {
                $q->whereNotNull('nro_factura');
            })->count(),
        ];

        return view('patients.index', compact('pacientes', 'stats'));
    }

    public function show($ci): View
    {
        $paciente = Paciente::with([
            'seguro',
            'triage', 
            'registro.usuario',
            'consultas' => function($q) {
                $q->with(['medico.usuario', 'especialidad', 'caja'])
                  ->orderBy('fecha', 'desc');
            },
            'emergencias' => function($q) {
                $q->with(['medico.usuario'])
                  ->orderBy('fecha', 'desc');
            },
            'hospitalizaciones' => function($q) {
                $q->with(['medico.usuario'])
                  ->orderBy('fecha_ingreso', 'desc');
            }
        ])->findOrFail($ci);

        return view('patients.show', compact('paciente'));
    }
}
