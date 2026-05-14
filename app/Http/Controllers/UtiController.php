<?php

namespace App\Http\Controllers;

use App\Models\Evaluacion;
use App\Models\CuentaCobroDetalle;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UtiController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:uti|admin|dirmedico|administrador']);
    }

    public function dashboard(Request $request): View
    {
        $fecha = $request->filled('fecha')
            ? \Carbon\Carbon::parse($request->fecha)->toDateString()
            : today()->toDateString();

        $evaluaciones = Evaluacion::with(['paciente', 'user', 'items'])
            ->where('area', 'uti')
            ->whereDate('created_at', $fecha)
            ->orderBy('created_at')
            ->get();

        $camillaRegistradas = CuentaCobroDetalle::with(['cuentaCobro.paciente', 'user'])
            ->where('area_origen', 'uti')
            ->whereDate('created_at', $fecha)
            ->orderBy('created_at')
            ->get();

        return view('uti.dashboard', compact('fecha', 'evaluaciones', 'camillaRegistradas'));
    }

    public function procedimientos(): \Illuminate\View\View
    {
        $procedimientos = \App\Models\Procedimiento::where('area', 'uti')
            ->where('activo', true)
            ->orderBy('nombre')
            ->paginate(50);

        return view('uti.procedimientos', compact('procedimientos'));
    }
}
