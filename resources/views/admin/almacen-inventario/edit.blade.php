@extends('layouts.app')

@section('title', 'Editar Activo — Almacén Inventario')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-2xl mx-auto">
        <div class="mb-6 flex items-center gap-3">
            <a href="{{ route('admin.almacen-inventario.index') }}"
               class="text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="text-xl font-bold text-gray-900">Editar Activo</h1>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form method="POST" action="{{ route('admin.almacen-inventario.update', $almacenInventario) }}" class="space-y-4">
                @csrf @method('PUT')

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Código de Activo *</label>
                        <input type="text" name="codigo_activo"
                               value="{{ old('codigo_activo', $almacenInventario->codigo_activo) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('codigo_activo') border-red-400 @enderror">
                        @error('codigo_activo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                        <input type="text" name="nombre"
                               value="{{ old('nombre', $almacenInventario->nombre) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nombre') border-red-400 @enderror">
                        @error('nombre')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Precio (Bs.) *</label>
                        <input type="number" name="precio"
                               value="{{ old('precio', $almacenInventario->precio) }}"
                               step="0.01" min="0"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('precio') border-red-400 @enderror">
                        @error('precio')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad *</label>
                        <input type="number" name="cantidad"
                               value="{{ old('cantidad', $almacenInventario->cantidad) }}"
                               min="0"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 @error('cantidad') border-red-400 @enderror">
                        @error('cantidad')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
                        <input type="text" name="marca"
                               value="{{ old('marca', $almacenInventario->marca) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Proveedor</label>
                        <input type="text" name="proveedor"
                               value="{{ old('proveedor', $almacenInventario->proveedor) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nro. Factura</label>
                        <input type="text" name="nro_factura"
                               value="{{ old('nro_factura', $almacenInventario->nro_factura) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Número de Recibo</label>
                        <input type="text" name="numero_recibo"
                               value="{{ old('numero_recibo', $almacenInventario->numero_recibo) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('admin.almacen-inventario.index') }}"
                       class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
