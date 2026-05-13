@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Nueva cuna</h1>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
        <form method="POST" action="{{ route('admin.neonato.cunas.store') }}">
            @csrf
            <div class="grid grid-cols-1 gap-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}"
                        class="w-full border rounded-lg px-3 py-2 text-sm @error('nombre') border-red-400 @enderror"
                        placeholder="Ej: Cuna 1 — Neonatología" autocomplete="off">
                    @error('nombre')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Código <span class="text-red-500">*</span></label>
                    <input type="text" name="codigo" value="{{ old('codigo') }}"
                        class="w-full border rounded-lg px-3 py-2 text-sm uppercase @error('codigo') border-red-400 @enderror"
                        placeholder="Ej: CUN-01" style="text-transform:uppercase" autocomplete="off">
                    @error('codigo')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio por hora (Bs.) <span class="text-red-500">*</span></label>
                    <input type="number" name="precio_por_hora" value="{{ old('precio_por_hora', 0) }}"
                        step="0.01" min="0"
                        class="w-full border rounded-lg px-3 py-2 text-sm @error('precio_por_hora') border-red-400 @enderror">
                    @error('precio_por_hora')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="activa" id="activa" value="1" {{ old('activa', true) ? 'checked' : '' }}
                        class="rounded border-gray-300">
                    <label for="activa" class="text-sm text-gray-700">Activa</label>
                </div>
            </div>

            <div class="mt-6 flex gap-3 justify-end">
                <a href="{{ route('admin.neonato.cunas') }}" class="px-4 py-2 border rounded-lg text-sm">Cancelar</a>
                <button type="submit" class="px-4 py-2 bg-pink-600 text-white rounded-lg text-sm hover:bg-pink-700">Guardar</button>
            </div>
        </form>
    </div>
</div>
@endsection
