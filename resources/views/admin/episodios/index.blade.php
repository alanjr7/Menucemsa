@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen">

    <div class="flex justify-between items-end mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Episodios de Pacientes</h1>
            <p class="text-sm text-gray-500">Historial clínico por episodio de ingreso</p>
        </div>
    </div>

    {{-- Búsqueda --}}
    <form method="GET" class="mb-6">
        <div class="flex gap-3">
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                placeholder="Buscar por CI o nombre..."
                class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                Buscar
            </button>
            @if(request('q'))
                <a href="{{ route('admin.episodios.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200">
                    Limpiar
                </a>
            @endif
        </div>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Paciente</th>
                    <th class="px-4 py-3 text-center">CI</th>
                    <th class="px-4 py-3 text-center">Episodios</th>
                    <th class="px-4 py-3 text-center">Estado actual</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($pacientes as $paciente)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $paciente->nombre }}</td>
                    <td class="px-4 py-3 text-center text-gray-600">{{ $paciente->ci }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-blue-100 text-blue-700 font-semibold text-xs">
                            {{ $paciente->episodios_count }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($paciente->episodioAbierto)
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                Episodio abierto
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                Sin episodio activo
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('admin.episodios.paciente', $paciente->ci) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 text-white text-xs rounded-lg hover:bg-blue-700 transition-colors">
                            Ver episodios
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-12 text-center text-gray-400">
                        No se encontraron pacientes con episodios registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($pacientes->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $pacientes->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
