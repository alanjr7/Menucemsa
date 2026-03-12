<?php

namespace App\Http\Controllers\Farmacia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cliente;

class ClientesController extends Controller
{
    public function index()
    {
        // Obtener todos los clientes
        $clientes = Cliente::orderBy('created_at', 'desc')->get();
        
        // Preparar datos para JavaScript
        $clientesArray = $clientes->map(function($cliente) {
            return [
                'id' => $cliente->id,
                'nombre' => $cliente->nombre,
                'fecha' => $cliente->fecha,
                'telefono' => $cliente->telefono,
                'email' => $cliente->email,
                'direccion' => $cliente->direccion
            ];
        })->toArray();

        return view('farmacia.clientes', compact('clientes', 'clientesArray'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:clientes,email',
            'direccion' => 'nullable|string|max:500'
        ]);

        $cliente = Cliente::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cliente creado exitosamente',
            'cliente' => $cliente
        ]);
    }

    public function update(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:clientes,email,' . $id,
            'direccion' => 'nullable|string|max:500'
        ]);

        $cliente->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cliente actualizado exitosamente',
            'cliente' => $cliente
        ]);
    }

    public function destroy($id)
    {
        try {
            $cliente = Cliente::findOrFail($id);
            $cliente->delete();

            return response()->json([
                'success' => true,
                'message' => 'Cliente eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el cliente: ' . $e->getMessage()
            ], 500);
        }
    }
}
