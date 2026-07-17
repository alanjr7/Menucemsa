@extends('layouts.app')

@section('title', 'Importar Stock por Excel')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-3xl mx-auto">

        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('admin.almacen-medicamentos.index') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Importar Stock por Excel</h1>
                <p class="text-sm text-gray-500">Carga masiva de medicamentos e insumos desde una planilla</p>
            </div>
        </div>

        @if(session('error'))
        <div class="p-4 mb-5 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">{{ session('error') }}</div>
        @endif

        @if($errors->any())
        <div class="p-4 mb-5 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
        @endif

        <!-- Instrucciones -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-5">
            <h3 class="text-sm font-semibold text-blue-900 mb-2">Cómo funciona</h3>
            <ol class="text-sm text-blue-800 space-y-1 list-decimal list-inside">
                <li>Descarga la plantilla. Completá <span class="font-mono text-xs bg-white px-1 rounded">nombre</span> y <span class="font-mono text-xs bg-white px-1 rounded">cantidad</span>. También podés llenar campos de lote como <span class="font-mono text-xs bg-white px-1 rounded">proveedor</span>, <span class="font-mono text-xs bg-white px-1 rounded">codigo_lote</span>, <span class="font-mono text-xs bg-white px-1 rounded">fecha_vencimiento</span> y <span class="font-mono text-xs bg-white px-1 rounded">precio_compra</span>.</li>
                <li>Si el nombre <strong>coincide</strong> con un ítem existente, se ajusta su stock. Si <strong>no existe</strong>, se crea (si está activada la opción).</li>
                <li>Subí el archivo, revisá la <strong>previsualización</strong> y confirmá. Ahí elegís si <strong>sumar</strong> o <strong>reemplazar</strong> las cantidades.</li>
            </ol>
            <a href="{{ route('admin.almacen-medicamentos.importar.plantilla') }}"
               class="inline-flex items-center gap-2 mt-3 px-3 py-1.5 bg-white border border-blue-300 text-blue-700 rounded-lg text-sm hover:bg-blue-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Descargar plantilla Excel
            </a>
        </div>

        <!-- Formulario -->
        <form method="POST" action="{{ route('admin.almacen-medicamentos.importar.previsualizar') }}"
              enctype="multipart/form-data" class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">
            @csrf

            <!-- Área destino -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Área destino <span class="text-red-500">*</span></label>
                <select name="area" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 outline-none">
                    @foreach($areas as $val => $label)
                        <option value="{{ $val }}" {{ $val === 'central' ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 mt-1">El stock se cargará directamente en esta área. <strong>Central</strong> es el almacén principal de recepción.</p>
            </div>

            <!-- Motivo -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Motivo <span class="text-red-500">*</span></label>
                <input type="text" name="motivo" maxlength="255" required value="{{ old('motivo') }}"
                       placeholder="Ej: Compra mensual, Inventario inicial, Donación..."
                       class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 outline-none">
            </div>

            <!-- Crear nuevos -->
            <label class="flex items-start gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                <input type="checkbox" name="crear_nuevos" value="1" checked class="mt-0.5 w-4 h-4 text-blue-600 rounded">
                <span class="text-sm text-gray-700">
                    <span class="font-medium">Crear medicamentos/insumos nuevos</span>
                    <span class="block text-xs text-gray-500">Si un nombre del Excel no existe en el catálogo, se crea automáticamente. Desactivá esto para solo actualizar los ya existentes.</span>
                </span>
            </label>

            <!-- Archivo -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Archivo Excel / CSV <span class="text-red-500">*</span></label>
                <input type="file" name="archivo" accept=".xlsx,.xls,.csv,.txt" required
                       class="w-full text-sm text-gray-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-600 file:text-white hover:file:bg-blue-700 border border-gray-300 rounded-lg">
                <p class="text-xs text-gray-400 mt-1">Formatos: .xlsx, .xls, .csv</p>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('admin.almacen-medicamentos.index') }}" class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    Previsualizar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
