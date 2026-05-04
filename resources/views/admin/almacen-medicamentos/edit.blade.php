@extends('layouts.app')

@section('title', 'Editar: ' . $catalogo->nombre)

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Editar: {{ $catalogo->nombre }}</h1>
            <p class="text-gray-600 mt-1">Solo se editan los datos del catálogo. Los lotes se gestionan desde la vista de detalle.</p>
        </div>
        <a href="{{ route('admin.almacen-medicamentos.show', $catalogo) }}"
           class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
        <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <div class="max-w-2xl">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form method="POST" action="{{ route('admin.almacen-medicamentos.update', $catalogo) }}">
                @csrf @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                        <input type="text" name="nombre" value="{{ old('nombre', $catalogo->nombre) }}" required maxlength="255"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg @error('nombre') border-red-500 @enderror">
                        @error('nombre')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo <span class="text-red-500">*</span></label>
                            <select name="tipo" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                @foreach($tipos as $v => $l)
                                    <option value="{{ $v }}" {{ old('tipo', $catalogo->tipo) == $v ? 'selected' : '' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Unidad de medida <span class="text-red-500">*</span></label>
                            <select name="unidad_medida" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                @foreach($unidades as $u)
                                    <option value="{{ $u }}" {{ old('unidad_medida', $catalogo->unidad_medida) == $u ? 'selected' : '' }}>{{ $u }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea name="descripcion" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg">{{ old('descripcion', $catalogo->descripcion) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                        <textarea name="observaciones" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg">{{ old('observaciones', $catalogo->observaciones) }}</textarea>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm">
                        Guardar cambios
                    </button>
                    <a href="{{ route('admin.almacen-medicamentos.show', $catalogo) }}"
                       class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium text-sm">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
