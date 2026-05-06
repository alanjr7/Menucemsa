<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlmacenInventario;
use Illuminate\Http\Request;

class AlmacenInventarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin|administrador');
    }

    public function index(Request $request)
    {
        $query = AlmacenInventario::query();

        if ($request->filled('buscar')) {
            $q = $request->buscar;
            $query->where(function ($w) use ($q) {
                $w->where('nombre', 'like', "%$q%")
                  ->orWhere('codigo_activo', 'like', "%$q%")
                  ->orWhere('proveedor', 'like', "%$q%");
            });
        }

        $items = $query->orderBy('nombre')->paginate(20)->withQueryString();

        return view('admin.almacen-inventario.index', compact('items'));
    }

    public function create()
    {
        return view('admin.almacen-inventario.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo_activo'  => 'required|string|max:100|unique:almacen_inventario,codigo_activo',
            'nombre'         => 'required|string|max:255',
            'precio'         => 'required|numeric|min:0',
            'cantidad'       => 'required|integer|min:0',
            'marca'          => 'nullable|string|max:150',
            'proveedor'      => 'nullable|string|max:255',
            'nro_factura'    => 'nullable|string|max:100',
            'numero_recibo'  => 'nullable|string|max:100',
        ]);

        AlmacenInventario::create($data);

        return redirect()->route('admin.almacen-inventario.index')
            ->with('success', 'Activo registrado correctamente.');
    }

    public function show(AlmacenInventario $almacenInventario)
    {
        return view('admin.almacen-inventario.show', compact('almacenInventario'));
    }

    public function edit(AlmacenInventario $almacenInventario)
    {
        return view('admin.almacen-inventario.edit', compact('almacenInventario'));
    }

    public function update(Request $request, AlmacenInventario $almacenInventario)
    {
        $data = $request->validate([
            'codigo_activo'  => 'required|string|max:100|unique:almacen_inventario,codigo_activo,' . $almacenInventario->id,
            'nombre'         => 'required|string|max:255',
            'precio'         => 'required|numeric|min:0',
            'cantidad'       => 'required|integer|min:0',
            'marca'          => 'nullable|string|max:150',
            'proveedor'      => 'nullable|string|max:255',
            'nro_factura'    => 'nullable|string|max:100',
            'numero_recibo'  => 'nullable|string|max:100',
        ]);

        $almacenInventario->update($data);

        return redirect()->route('admin.almacen-inventario.index')
            ->with('success', 'Activo actualizado correctamente.');
    }

    public function destroy(AlmacenInventario $almacenInventario)
    {
        $almacenInventario->delete();

        return redirect()->route('admin.almacen-inventario.index')
            ->with('success', 'Activo eliminado correctamente.');
    }
}
