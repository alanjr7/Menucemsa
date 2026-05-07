@extends('layouts.app')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Camillas</h1>
                <p class="text-sm text-gray-500">Gestión de camillas para UTI y Emergencias</p>
            </div>
            <a href="{{ route('admin.camillas.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Nueva camilla</a>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-800">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-red-800">{{ session('error') }}</div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Código</th>
                        <th class="px-4 py-3 text-left">Nombre</th>
                        <th class="px-4 py-3 text-left">Área</th>
                        <th class="px-4 py-3 text-right">Precio/hora (Bs.)</th>
                        <th class="px-4 py-3 text-center">Usos</th>
                        <th class="px-4 py-3 text-center">Activa</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($camillas as $camilla)
                        <tr class="border-t">
                            <td class="px-4 py-3 font-mono font-semibold text-gray-700">{{ $camilla->codigo }}</td>
                            <td class="px-4 py-3">{{ $camilla->nombre }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $camilla->area === 'uti' ? 'bg-purple-100 text-purple-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $camilla->area_label }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">{{ number_format($camilla->precio_por_hora, 2) }}</td>
                            <td class="px-4 py-3 text-center">{{ $camilla->usos_count }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($camilla->activa)
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700">Sí</span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-600">No</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.camillas.edit', $camilla) }}"
                                        class="px-3 py-1 border border-gray-200 rounded text-xs hover:bg-gray-50">Editar</a>
                                    <form action="{{ route('admin.camillas.destroy', $camilla) }}" method="POST"
                                        onsubmit="return confirm('¿Eliminar camilla {{ $camilla->nombre }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-3 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">No hay camillas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $camillas->links() }}
        </div>
    </div>
@endsection
