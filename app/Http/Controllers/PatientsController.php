<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Consulta;
use App\Models\Emergencia;
use App\Models\Hospitalizacion;
use App\Models\Caja;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientsController extends Controller
{
    public function index(Request $request): View
    {
        $query = Paciente::with(['seguro', 'triage', 'registro.usuario'])
            ->whereHas('registro') // Solo pacientes con registro
            ->whereHas('consultas.caja', function($q) {
                $q->whereNotNull('nro_factura'); // Solo pacientes que han pagado
            });

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

        // Estadísticas
        $stats = [
            'total' => Paciente::whereHas('registro')->count(),
            'hospitalizados' => Paciente::whereHas('hospitalizaciones', function($q) {
                $q->where('estado', 'Activo');
            })->count(),
            'emergencias' => Paciente::whereHas('emergencias', function($q) {
                $q->where('estado', 'Activo');
            })->count(),
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
