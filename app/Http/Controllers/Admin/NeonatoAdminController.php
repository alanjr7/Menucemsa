<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlmacenStock;
use App\Models\Camilla;
use App\Models\CamillaUso;
use App\Models\Evaluacion;
use App\Models\Neonato;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NeonatoAdminController extends Controller
{
    // -------------------------------------------------------------------------
    // Dashboard
    // -------------------------------------------------------------------------

    public function dashboard(): View
    {
        $hoy = now()->toDateString();

        $stats = [
            'total_hoy'      => Neonato::whereDate('admission_date', $hoy)->count(),
            'activos'        => Neonato::whereNotIn('status', ['alta', 'fallecido'])->count(),
            'en_observacion' => Neonato::where('status', 'en_observacion')->count(),
            'uti_neonatal'   => Neonato::where('status', 'uti_neonatal')->count(),
            'alta_hoy'       => Neonato::where('status', 'alta')->whereDate('discharge_date', $hoy)->count(),
            'cunas_activas'  => Camilla::where('area', 'neonato')->where('activa', true)->count(),
        ];

        $recientes = Neonato::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();

        $evaluaciones = Evaluacion::where('area', 'neonato')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->whereDate('created_at', $hoy)
            ->limit(20)
            ->get();

        $usosCunas = CamillaUso::whereHas('camilla', fn($q) => $q->where('area', 'neonato'))
            ->with(['camilla'])
            ->whereDate('created_at', $hoy)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('admin.neonato.dashboard', compact(
            'stats', 'recientes', 'evaluaciones', 'usosCunas'
        ));
    }

    // -------------------------------------------------------------------------
    // Medicamentos — solo lectura
    // -------------------------------------------------------------------------

    public function medicamentos(): View
    {
        $stocks = AlmacenStock::with(['lote.catalogo'])
            ->where('ubicacion', 'neonato')
            ->orderBy('ubicacion')
            ->paginate(30);

        return view('admin.neonato.medicamentos', compact('stocks'));
    }

    // -------------------------------------------------------------------------
    // Procedimientos — solo lectura
    // -------------------------------------------------------------------------

    public function procedimientos(): View
    {
        $procedimientos = \App\Models\Procedimiento::where('area', 'neonato')
            ->where('activo', true)
            ->orderBy('nombre')
            ->paginate(30);

        return view('neonato.procedimientos', compact('procedimientos'));
    }

    // -------------------------------------------------------------------------
    // Cunas (Camillas area=neonato) — CRUD
    // -------------------------------------------------------------------------

    public function cunas(): View
    {
        $cunas = Camilla::withCount('usos')
            ->where('area', 'neonato')
            ->orderBy('nombre')
            ->paginate(20);

        return view('admin.neonato.cunas.index', compact('cunas'));
    }

    public function createCuna(): View
    {
        return view('admin.neonato.cunas.create');
    }

    public function storeCuna(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre'          => ['required', 'string', 'max:100'],
            'codigo'          => ['required', 'string', 'max:30', 'unique:camillas,codigo'],
            'precio_por_hora' => ['required', 'numeric', 'min:0'],
        ]);

        $data['codigo']  = strtoupper($data['codigo']);
        $data['area']    = 'neonato';
        $data['activa']  = $request->boolean('activa', true);

        Camilla::create($data);

        return redirect()->route('admin.neonato.cunas')
            ->with('success', 'Cuna creada correctamente.');
    }

    public function editCuna(Camilla $cuna): View
    {
        abort_unless($cuna->area === 'neonato', 404);

        return view('admin.neonato.cunas.edit', compact('cuna'));
    }

    public function updateCuna(Request $request, Camilla $cuna): RedirectResponse
    {
        abort_unless($cuna->area === 'neonato', 404);

        $data = $request->validate([
            'nombre'          => ['required', 'string', 'max:100'],
            'codigo'          => ['required', 'string', 'max:30', 'unique:camillas,codigo,' . $cuna->id],
            'precio_por_hora' => ['required', 'numeric', 'min:0'],
        ]);

        $data['codigo'] = strtoupper($data['codigo']);
        $data['activa'] = $request->boolean('activa', true);

        $cuna->update($data);

        return redirect()->route('admin.neonato.cunas')
            ->with('success', 'Cuna actualizada correctamente.');
    }

    public function destroyCuna(Camilla $cuna): RedirectResponse
    {
        abort_unless($cuna->area === 'neonato', 404);

        if ($cuna->usos()->exists()) {
            return redirect()->route('admin.neonato.cunas')
                ->with('error', 'No se puede eliminar: la cuna tiene usos registrados.');
        }

        $cuna->delete();

        return redirect()->route('admin.neonato.cunas')
            ->with('success', 'Cuna eliminada correctamente.');
    }

    // -------------------------------------------------------------------------
    // Recién nacidos — solo lectura
    // -------------------------------------------------------------------------

    public function recienNacidos(Request $request): View
    {
        $query = Neonato::with('user')->orderBy('created_at', 'desc');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('temp_id', 'like', "%{$search}%")
                  ->orWhere('madre_nombre', 'like', "%{$search}%")
                  ->orWhere('madre_ci', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $neonatos = $query->paginate(20)->withQueryString();

        return view('admin.neonato.recien-nacidos.index', compact('neonatos'));
    }

    public function showRecienNacido(Neonato $neonato): View
    {
        $neonato->load('user');

        $evaluaciones = Evaluacion::where('area', 'neonato')
            ->where('temp_id', $neonato->temp_id)
            ->with(['user', 'items'])
            ->orderBy('created_at', 'desc')
            ->get();

        $usosCunas = CamillaUso::where('paciente_ci', $neonato->temp_id)
            ->whereHas('camilla', fn($q) => $q->where('area', 'neonato'))
            ->with('camilla')
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        return view('admin.neonato.recien-nacidos.show', compact(
            'neonato', 'evaluaciones', 'usosCunas'
        ));
    }
}
