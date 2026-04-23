<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlmacenMedicamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class AlmacenMedicamentosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index(Request $request)
    {
        $query = AlmacenMedicamento::query();

        // Filtros
        if ($request->filled('area')) {
            $query->porArea($request->area);
        }

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

        $almacenMedicamentos = $query->activos()->orderBy('nombre')->paginate(20);

        // Estadísticas
        $stats = [
            'total' => AlmacenMedicamento::activos()->count(),
            'medicamentos' => AlmacenMedicamento::activos()->medicamentos()->count(),
            'insumos' => AlmacenMedicamento::activos()->insumos()->count(),
            'bajo_stock' => AlmacenMedicamento::activos()->bajoStock()->count(),
            'agotados' => AlmacenMedicamento::activos()->where('cantidad', 0)->count(),
            'vencidos' => AlmacenMedicamento::activos()->vencidos()->count(),
            'por_vencer' => AlmacenMedicamento::activos()->porVencer()->count(),
        ];

        $areas = ['emergencia', 'cirugia', 'internacion', 'uti', 'usi', 'neonato'];

        return view('admin.almacen-medicamentos.index', compact('almacenMedicamentos', 'stats', 'areas'));
    }

    public function create()
    {
        $areas = ['emergencia' => 'Emergencia', 'cirugia' => 'Cirugía', 'internacion' => 'Internación', 'uti' => 'UTI', 'usi' => 'USI', 'neonato' => 'Neonato'];
        $tipos = ['medicamento' => 'Medicamento', 'insumo' => 'Insumo'];
        $unidades = ['unidades', 'ml', 'mg', 'gr', 'cm', 'cajas', 'frascos', 'sobres'];

        return view('admin.almacen-medicamentos.create', compact('areas', 'tipos', 'unidades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'area' => 'required|in:emergencia,cirugia,internacion,uti,usi,neonato',
            'precio' => 'nullable|numeric|min:0',
            'fecha_vencimiento' => 'nullable|date|after:today',
            'lote' => 'nullable|string|max:100',
            'cantidad' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
            'unidad_medida' => 'required|string|max:50',
            'tipo' => 'required|in:medicamento,insumo',
            'observaciones' => 'nullable|string',
        ]);

        $medicamento = AlmacenMedicamento::create($request->all());

        // Registrar actividad en log
        Log::info('Usuario ' . Auth::user()->name . ' creó nuevo medicamento/insumo en almacén: ' . $medicamento->nombre, [
            'user_id' => Auth::id(),
            'medicamento_id' => $medicamento->id,
            'action' => 'create',
            'module' => 'almacen_medicamentos'
        ]);

        return redirect()->route('admin.almacen-medicamentos.index')
            ->with('success', 'Medicamento/Insumo agregado correctamente al almacén.');
    }

    public function show(AlmacenMedicamento $almacenMedicamento)
    {
        return view('admin.almacen-medicamentos.show', compact('almacenMedicamento'));
    }

    public function edit(AlmacenMedicamento $almacenMedicamento)
    {
        $areas = ['emergencia' => 'Emergencia', 'cirugia' => 'Cirugía', 'internacion' => 'Internación', 'uti' => 'UTI', 'usi' => 'USI', 'neonato' => 'Neonato'];
        $tipos = ['medicamento' => 'Medicamento', 'insumo' => 'Insumo'];
        $unidades = ['unidades', 'ml', 'mg', 'gr', 'cm', 'cajas', 'frascos', 'sobres'];

        return view('admin.almacen-medicamentos.edit', compact('almacenMedicamento', 'areas', 'tipos', 'unidades'));
    }

    public function update(Request $request, AlmacenMedicamento $almacenMedicamento)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'area' => 'required|in:emergencia,cirugia,internacion,uti,usi,neonato',
            'precio' => 'nullable|numeric|min:0',
            'fecha_vencimiento' => 'nullable|date',
            'lote' => 'nullable|string|max:100',
            'cantidad' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
            'unidad_medida' => 'required|string|max:50',
            'tipo' => 'required|in:medicamento,insumo',
            'observaciones' => 'nullable|string',
        ]);

        $almacenMedicamento->update($request->only([
            'nombre', 'descripcion', 'area', 'precio', 'fecha_vencimiento',
            'lote', 'cantidad', 'stock_minimo', 'unidad_medida', 'tipo', 'observaciones',
        ]));

        // Registrar actividad en log
        Log::info('Usuario ' . Auth::user()->name . ' actualizó medicamento/insumo en almacén: ' . $almacenMedicamento->nombre, [
            'user_id' => Auth::id(),
            'medicamento_id' => $almacenMedicamento->id,
            'action' => 'update',
            'module' => 'almacen_medicamentos'
        ]);

        return redirect()->route('admin.almacen-medicamentos.index')
            ->with('success', 'Medicamento/Insumo actualizado correctamente.');
    }

    public function destroy(AlmacenMedicamento $almacenMedicamento)
    {
        $nombre = $almacenMedicamento->nombre;
        $almacenMedicamento->update(['activo' => false]);

        // Registrar actividad en log
        Log::info('Usuario ' . Auth::user()->name . ' desactivó medicamento/insumo en almacén: ' . $nombre, [
            'user_id' => Auth::id(),
            'medicamento_id' => $almacenMedicamento->id,
            'action' => 'deactivate',
            'module' => 'almacen_medicamentos'
        ]);

        return redirect()->route('admin.almacen-medicamentos.index')
            ->with('success', 'Medicamento/Insumo desactivado correctamente.');
    }

    public function actualizarStock(Request $request, AlmacenMedicamento $almacenMedicamento)
    {
        $request->validate([
            'cantidad' => 'required|numeric|min:0',
            'motivo' => 'required|string|max:255',
        ]);

        $cantidadAnterior = $almacenMedicamento->cantidad;
        $almacenMedicamento->update(['cantidad' => $request->cantidad]);

        // Registrar actividad en log
        Log::info('Usuario ' . Auth::user()->name . ' actualizó stock de ' . $almacenMedicamento->nombre . ': ' . $cantidadAnterior . ' → ' . $request->cantidad . '. Motivo: ' . $request->motivo, [
            'user_id' => Auth::id(),
            'medicamento_id' => $almacenMedicamento->id,
            'action' => 'update_stock',
            'cantidad_anterior' => $cantidadAnterior,
            'cantidad_nueva' => $request->cantidad,
            'motivo' => $request->motivo,
            'module' => 'almacen_medicamentos'
        ]);

        return redirect()->back()
            ->with('success', 'Stock actualizado correctamente.');
    }

    public function reporteBajoStock()
    {
        $medicamentos = AlmacenMedicamento::activos()
            ->bajoStock()
            ->orderBy('cantidad')
            ->get();

        return view('admin.almacen-medicamentos.reporte-bajo-stock', compact('medicamentos'));
    }

    public function reporteVencimiento()
    {
        $vencidos = AlmacenMedicamento::activos()
            ->vencidos()
            ->orderBy('fecha_vencimiento')
            ->get();

        $porVencer = AlmacenMedicamento::activos()
            ->porVencer()
            ->orderBy('fecha_vencimiento')
            ->get();

        return view('admin.almacen-medicamentos.reporte-vencimiento', compact('vencidos', 'porVencer'));
    }

    public function porArea($area)
    {
        $medicamentos = AlmacenMedicamento::activos()
            ->porArea($area)
            ->orderBy('nombre')
            ->paginate(20);

        $stats = [
            'total' => $medicamentos->total(),
            'medicamentos' => $medicamentos->where('tipo', 'medicamento')->count(),
            'insumos' => $medicamentos->where('tipo', 'insumo')->count(),
            'bajo_stock' => $medicamentos->filter(fn($m) => $m->estaBajoStock())->count(),
            'agotados' => $medicamentos->where('cantidad', 0)->count(),
        ];

        return view('admin.almacen-medicamentos.por-area', compact('medicamentos', 'area', 'stats'));
    }
}
