<?php

namespace App\Http\Controllers;

use App\Models\AlmacenMedicamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UtiMedicamentosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin|uti|administrador');
    }

    public function index(Request $request)
    {
        $query = AlmacenMedicamento::porArea('uti');

        // Filtros
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('buscar')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->buscar . '%')
                  ->orWhere('lote', 'like', '%' . $request->buscar . '%')
                  ->orWhere('descripcion', 'like', '%' . $request->buscar . '%');
            });
        }

        if ($request->filled('estado_stock')) {
            if ($request->estado_stock === 'bajo') {
                $query->bajoStock();
            } elseif ($request->estado_stock === 'agotado') {
                $query->where('cantidad', 0);
            }
        }

        if ($request->filled('estado_vencimiento')) {
            if ($request->estado_vencimiento === 'vencido') {
                $query->vencidos();
            } elseif ($request->estado_vencimiento === 'por_vencer') {
                $query->porVencer();
            }
        }

        $medicamentos = $query->activos()->orderBy('nombre')->paginate(10);

        // Estadísticas
        $stats = [
            'total' => AlmacenMedicamento::activos()->porArea('uti')->count(),
            'medicamentos' => AlmacenMedicamento::activos()->porArea('uti')->medicamentos()->count(),
            'insumos' => AlmacenMedicamento::activos()->porArea('uti')->insumos()->count(),
            'bajo_stock' => AlmacenMedicamento::activos()->porArea('uti')->bajoStock()->count(),
            'agotados' => AlmacenMedicamento::activos()->porArea('uti')->where('cantidad', 0)->count(),
            'vencidos' => AlmacenMedicamento::activos()->porArea('uti')->vencidos()->count(),
            'por_vencer' => AlmacenMedicamento::activos()->porArea('uti')->porVencer()->count(),
        ];

        return view('uti.medicamentos.index', compact('medicamentos', 'stats'));
    }

    public function create()
    {
        $tipos = ['medicamento' => 'Medicamento', 'insumo' => 'Insumo'];
        $unidades = ['unidades' => 'Unidades', 'ml' => 'Mililitros (ml)', 'mg' => 'Miligramos (mg)', 'gr' => 'Gramos (gr)', 'cm' => 'Centímetros (cm)', 'cajas' => 'Cajas', 'frascos' => 'Frascos', 'sobres' => 'Sobres'];

        return view('uti.medicamentos.create', compact('tipos', 'unidades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'nullable|numeric|min:0',
            'fecha_vencimiento' => 'nullable|date|after:today',
            'lote' => 'nullable|string|max:100',
            'cantidad' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
            'unidad_medida' => 'required|string|max:50',
            'tipo' => 'required|in:medicamento,insumo',
            'observaciones' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['area'] = 'uti';
        $data['activo'] = true;

        $medicamento = AlmacenMedicamento::create($data);

        Log::info('Usuario ' . Auth::user()->name . ' creó medicamento/insumo en UTI: ' . $medicamento->nombre, [
            'user_id' => Auth::id(),
            'medicamento_id' => $medicamento->id,
            'action' => 'create',
            'module' => 'uti_medicamentos'
        ]);

        return redirect()->route('uti.operativa.medicamentos.index')
            ->with('success', 'Medicamento/Insumo agregado correctamente al inventario de UTI.');
    }

    public function show(AlmacenMedicamento $medicamento)
    {
        // Verificar que pertenezca a uti
        if ($medicamento->area !== 'uti') {
            abort(404);
        }

        return view('uti.medicamentos.show', compact('medicamento'));
    }

    public function edit(AlmacenMedicamento $medicamento)
    {
        // Verificar que pertenezca a uti
        if ($medicamento->area !== 'uti') {
            abort(404);
        }

        $tipos = ['medicamento' => 'Medicamento', 'insumo' => 'Insumo'];
        $unidades = ['unidades' => 'Unidades', 'ml' => 'Mililitros (ml)', 'mg' => 'Miligramos (mg)', 'gr' => 'Gramos (gr)', 'cm' => 'Centímetros (cm)', 'cajas' => 'Cajas', 'frascos' => 'Frascos', 'sobres' => 'Sobres'];

        return view('uti.medicamentos.edit', compact('medicamento', 'tipos', 'unidades'));
    }

    public function update(Request $request, AlmacenMedicamento $medicamento)
    {
        // Verificar que pertenezca a uti
        if ($medicamento->area !== 'uti') {
            abort(404);
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'nullable|numeric|min:0',
            'fecha_vencimiento' => 'nullable|date',
            'lote' => 'nullable|string|max:100',
            'cantidad' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
            'unidad_medida' => 'required|string|max:50',
            'tipo' => 'required|in:medicamento,insumo',
            'observaciones' => 'nullable|string',
        ]);

        $medicamento->update($request->only([
            'nombre', 'descripcion', 'precio', 'fecha_vencimiento', 'lote',
            'cantidad', 'stock_minimo', 'unidad_medida', 'tipo', 'observaciones',
        ]));

        Log::info('Usuario ' . Auth::user()->name . ' actualizó medicamento/insumo en UTI: ' . $medicamento->nombre, [
            'user_id' => Auth::id(),
            'medicamento_id' => $medicamento->id,
            'action' => 'update',
            'module' => 'uti_medicamentos'
        ]);

        return redirect()->route('uti.operativa.medicamentos.index')
            ->with('success', 'Medicamento/Insumo actualizado correctamente.');
    }

    public function destroy(AlmacenMedicamento $medicamento)
    {
        // Verificar que pertenezca a uti
        if ($medicamento->area !== 'uti') {
            abort(404);
        }

        $nombre = $medicamento->nombre;
        $medicamento->update(['activo' => false]);

        Log::info('Usuario ' . Auth::user()->name . ' desactivó medicamento/insumo en UTI: ' . $nombre, [
            'user_id' => Auth::id(),
            'medicamento_id' => $medicamento->id,
            'action' => 'deactivate',
            'module' => 'uti_medicamentos'
        ]);

        return redirect()->route('uti.operativa.medicamentos.index')
            ->with('success', 'Medicamento/Insumo eliminado correctamente.');
    }

    public function actualizarStock(Request $request, AlmacenMedicamento $medicamento)
    {
        // Verificar que pertenezca a uti
        if ($medicamento->area !== 'uti') {
            abort(404);
        }

        $request->validate([
            'cantidad' => 'required|numeric|min:0',
            'motivo' => 'required|string|max:255',
        ]);

        $cantidadAnterior = $medicamento->cantidad;
        $medicamento->update(['cantidad' => $request->cantidad]);

        Log::info('Usuario ' . Auth::user()->name . ' actualizó stock en UTI de ' . $medicamento->nombre . ': ' . $cantidadAnterior . ' → ' . $request->cantidad . '. Motivo: ' . $request->motivo, [
            'user_id' => Auth::id(),
            'medicamento_id' => $medicamento->id,
            'action' => 'update_stock',
            'cantidad_anterior' => $cantidadAnterior,
            'cantidad_nueva' => $request->cantidad,
            'motivo' => $request->motivo,
            'module' => 'uti_medicamentos'
        ]);

        return redirect()->back()
            ->with('success', 'Stock actualizado correctamente.');
    }
}
