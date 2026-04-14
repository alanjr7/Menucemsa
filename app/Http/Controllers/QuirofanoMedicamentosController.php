<?php

namespace App\Http\Controllers;

use App\Models\AlmacenMedicamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class QuirofanoMedicamentosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index(Request $request)
    {
        $query = AlmacenMedicamento::porArea('cirugia');

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

        $medicamentos = $query->activos()->orderBy('nombre')->paginate(20);

        // Estadísticas
        $stats = [
            'total' => AlmacenMedicamento::activos()->porArea('cirugia')->count(),
            'medicamentos' => AlmacenMedicamento::activos()->porArea('cirugia')->medicamentos()->count(),
            'insumos' => AlmacenMedicamento::activos()->porArea('cirugia')->insumos()->count(),
            'bajo_stock' => AlmacenMedicamento::activos()->porArea('cirugia')->bajoStock()->count(),
            'agotados' => AlmacenMedicamento::activos()->porArea('cirugia')->where('cantidad', 0)->count(),
            'vencidos' => AlmacenMedicamento::activos()->porArea('cirugia')->vencidos()->count(),
            'por_vencer' => AlmacenMedicamento::activos()->porArea('cirugia')->porVencer()->count(),
        ];

        return view('quirofano.medicamentos.index', compact('medicamentos', 'stats'));
    }

    public function create()
    {
        $tipos = ['medicamento' => 'Medicamento', 'insumo' => 'Insumo'];
        $unidades = ['unidades' => 'Unidades', 'ml' => 'Mililitros (ml)', 'mg' => 'Miligramos (mg)', 'gr' => 'Gramos (gr)', 'cm' => 'Centímetros (cm)', 'cajas' => 'Cajas', 'frascos' => 'Frascos', 'sobres' => 'Sobres'];

        return view('quirofano.medicamentos.create', compact('tipos', 'unidades'));
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
        $data['area'] = 'cirugia';
        $data['activo'] = true;

        $medicamento = AlmacenMedicamento::create($data);

        Log::info('Usuario ' . Auth::user()->name . ' creó medicamento/insumo en quirófano: ' . $medicamento->nombre, [
            'user_id' => Auth::id(),
            'medicamento_id' => $medicamento->id,
            'action' => 'create',
            'module' => 'quirofano_medicamentos'
        ]);

        return redirect()->route('quirofano.medicamentos.index')
            ->with('success', 'Medicamento/Insumo agregado correctamente al inventario de quirófano.');
    }

    public function show(AlmacenMedicamento $medicamento)
    {
        // Verificar que pertenezca a cirugía
        if ($medicamento->area !== 'cirugia') {
            abort(404);
        }

        return view('quirofano.medicamentos.show', compact('medicamento'));
    }

    public function edit(AlmacenMedicamento $medicamento)
    {
        // Verificar que pertenezca a cirugía
        if ($medicamento->area !== 'cirugia') {
            abort(404);
        }

        $tipos = ['medicamento' => 'Medicamento', 'insumo' => 'Insumo'];
        $unidades = ['unidades' => 'Unidades', 'ml' => 'Mililitros (ml)', 'mg' => 'Miligramos (mg)', 'gr' => 'Gramos (gr)', 'cm' => 'Centímetros (cm)', 'cajas' => 'Cajas', 'frascos' => 'Frascos', 'sobres' => 'Sobres'];

        return view('quirofano.medicamentos.edit', compact('medicamento', 'tipos', 'unidades'));
    }

    public function update(Request $request, AlmacenMedicamento $medicamento)
    {
        // Verificar que pertenezca a cirugía
        if ($medicamento->area !== 'cirugia') {
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

        $medicamento->update($request->all());

        Log::info('Usuario ' . Auth::user()->name . ' actualizó medicamento/insumo en quirófano: ' . $medicamento->nombre, [
            'user_id' => Auth::id(),
            'medicamento_id' => $medicamento->id,
            'action' => 'update',
            'module' => 'quirofano_medicamentos'
        ]);

        return redirect()->route('quirofano.medicamentos.index')
            ->with('success', 'Medicamento/Insumo actualizado correctamente.');
    }

    public function destroy(AlmacenMedicamento $medicamento)
    {
        // Verificar que pertenezca a cirugía
        if ($medicamento->area !== 'cirugia') {
            abort(404);
        }

        $nombre = $medicamento->nombre;
        $medicamento->update(['activo' => false]);

        Log::info('Usuario ' . Auth::user()->name . ' desactivó medicamento/insumo en quirófano: ' . $nombre, [
            'user_id' => Auth::id(),
            'medicamento_id' => $medicamento->id,
            'action' => 'deactivate',
            'module' => 'quirofano_medicamentos'
        ]);

        return redirect()->route('quirofano.medicamentos.index')
            ->with('success', 'Medicamento/Insumo eliminado correctamente.');
    }

    public function actualizarStock(Request $request, AlmacenMedicamento $medicamento)
    {
        // Verificar que pertenezca a cirugía
        if ($medicamento->area !== 'cirugia') {
            abort(404);
        }

        $request->validate([
            'cantidad' => 'required|numeric|min:0',
            'motivo' => 'required|string|max:255',
        ]);

        $cantidadAnterior = $medicamento->cantidad;
        $medicamento->update(['cantidad' => $request->cantidad]);

        Log::info('Usuario ' . Auth::user()->name . ' actualizó stock en quirófano de ' . $medicamento->nombre . ': ' . $cantidadAnterior . ' → ' . $request->cantidad . '. Motivo: ' . $request->motivo, [
            'user_id' => Auth::id(),
            'medicamento_id' => $medicamento->id,
            'action' => 'update_stock',
            'cantidad_anterior' => $cantidadAnterior,
            'cantidad_nueva' => $request->cantidad,
            'motivo' => $request->motivo,
            'module' => 'quirofano_medicamentos'
        ]);

        return redirect()->back()
            ->with('success', 'Stock actualizado correctamente.');
    }
}
