@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen">

    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Pacientes de Médicos</h1>
            <p class="text-sm text-gray-500">Selecciona un médico para ver todos sus pacientes asignados</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('consulta.index') }}" class="flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-600 bg-white hover:bg-gray-50 font-medium transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </a>
            <a href="{{ route('consulta.historial-medico') }}" class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Ver Historial
            </a>
        </div>
    </div>

    <!-- Lista de Médicos -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-800">Médicos con Pacientes Asignados</h2>
            <p class="text-sm text-gray-500 mt-1">Total: {{ $medicos->count() }} médicos en el sistema</p>
        </div>
        
        <div class="divide-y divide-gray-100">
            @forelse($medicos as $medico)
                <div class="p-6 hover:bg-gray-50 transition cursor-pointer" onclick="window.location.href='{{ route('consulta.pacientes-medicos', $medico->ci) }}'">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                {{ substr($medico->usuario->name, 0, 1) }}{{ substr(explode(' ', $medico->usuario->name)[1] ?? '', 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">{{ $medico->usuario->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $medico->especialidad->nombre ?? 'Sin especialidad' }}</p>
                                <p class="text-xs text-gray-500">CI: {{ $medico->ci }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-700">Ver pacientes</div>
                                <div class="text-xs text-purple-600">Click para detalles →</div>
                            </div>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p class="text-lg font-medium">No hay médicos registrados</p>
                    <p class="text-sm mt-1">No se encontraron médicos en el sistema</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
