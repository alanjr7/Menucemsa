<?php

namespace App\Http\Controllers;

use App\Models\AlmacenCatalogo;
use App\Models\AlmacenLote;
use App\Models\AlmacenStock;
use Illuminate\Http\Request;

class UtiMedicamentosController extends Controller
{
    protected $ubicacion = 'uti';

    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|uti|administrador|dirmedico|doctor']);
    }

    public function index(Request $request)
    {
        $ubicacion = $this->ubicacion;

        $query = AlmacenCatalogo::activos()
            ->with(['lotes' => function($q) use ($ubicacion) {
                $q->with(['stocks' => function($sq) use ($ubicacion) {
                    $sq->where('ubicacion', $ubicacion);
                }]);
            }])
            ->whereHas('lotes.stocks', function($q) use ($ubicacion) {
                $q->where('ubicacion', $ubicacion);
            });

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('buscar')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->buscar . '%')
                  ->orWhere('descripcion', 'like', '%' . $request->buscar . '%');
            });
        }

        $medicamentos = $query->orderBy('nombre')->paginate(20);

        $stats = [
            'total'      => AlmacenCatalogo::activos()->whereHas('lotes.stocks', fn($q) => $q->where('ubicacion', $ubicacion))->count(),
            'bajo_stock' => AlmacenStock::where('ubicacion', $ubicacion)->bajoStock()->count(),
            'agotados'   => AlmacenStock::where('ubicacion', $ubicacion)->agotado()->count(),
            'vencidos'   => AlmacenLote::vencidos()->whereHas('stocks', fn($q) => $q->where('ubicacion', $ubicacion))->count(),
        ];

        return view('uti.medicamentos.index', compact('medicamentos', 'stats'));
    }
}
