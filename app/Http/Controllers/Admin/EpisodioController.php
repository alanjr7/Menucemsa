<?php

namespace App\Http\Controllers\Admin;

use App\Exports\EpisodioExport;
use App\Http\Controllers\Controller;
use App\Models\Episodio;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class EpisodioController extends Controller
{
    public function index(Request $request)
    {
        $query = Paciente::whereHas('episodios');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('ci', 'like', "%{$q}%")
                    ->orWhere('nombre', 'like', "%{$q}%");
            });
        }

        $pacientes = $query->withCount('episodios')
            ->with('episodioAbierto')
            ->orderBy('nombre')
            ->paginate(25)
            ->withQueryString();

        return view('admin.episodios.index', compact('pacientes'));
    }

    public function porPaciente($ci)
    {
        $paciente = Paciente::where('ci', $ci)->firstOrFail();

        $episodios = Episodio::where('paciente_id', $paciente->id)
            ->with(['creadoPor', 'cerradoPor'])
            ->withCount(['evaluaciones', 'historialMedico', 'cuentasCobro'])
            ->orderBy('numero', 'desc')
            ->get();

        return view('admin.episodios.paciente', compact('paciente', 'episodios'));
    }

    public function show($id)
    {
        $episodio = Episodio::with([
            'paciente',
            'creadoPor',
            'cerradoPor',
            'evaluaciones.user',
            'evaluaciones.items',
            'historialMedico.userMedico',
            'emergencias',
            'hospitalizaciones.medico.user',
            'cuentasCobro.detalles',
        ])->findOrFail($id);

        return view('admin.episodios.show', compact('episodio'));
    }

    public function exportExcel($id)
    {
        $episodio = Episodio::with([
            'paciente',
            'evaluaciones.user',
            'evaluaciones.items',
            'emergencias',
            'hospitalizaciones.medico.user',
            'cuentasCobro.detalles',
        ])->findOrFail($id);

        $nombre = 'episodio-' . $episodio->numero . '-' . ($episodio->paciente?->ci ?? $episodio->paciente?->temp_code ?? $episodio->paciente_id) . '.xlsx';

        return Excel::download(new EpisodioExport($episodio), $nombre);
    }

    public function exportPdf($id)
    {
        $episodio = Episodio::with([
            'paciente',
            'evaluaciones.user',
            'evaluaciones.items',
            'emergencias',
            'hospitalizaciones.medico.user',
            'cuentasCobro.detalles',
        ])->findOrFail($id);

        return view('admin.episodios.print', compact('episodio'));
    }
}
