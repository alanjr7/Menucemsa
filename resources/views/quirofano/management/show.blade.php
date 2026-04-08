@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen">

    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Quirófano Q{{ $quirofano->id }}</h1>
            <p class="text-sm text-gray-500">Detalles del quirófano</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('quirofanos.management.index') }}" class="flex items-center px-4 py-2 border border-gray-200 rounded-lg text-gray-600 bg-white hover:bg-gray-50 font-medium transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </a>
            <a href="{{ route('quirofanos.management.edit', $quirofano->id) }}" class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Información del Quirófano -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Quirófano</h3>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Número</span>
                    <span class="text-lg font-bold text-gray-900">Q{{ $quirofano->id }}</span>
                </div>
                
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Tipo</span>
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">{{ $quirofano->tipo }}</span>
                </div>
                
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Estado</span>
                    @php
                        $estadoColor = match($quirofano->estado) {
                            'disponible' => 'bg-green-100 text-green-800',
                            'ocupado' => 'bg-red-100 text-red-800',
                            'mantenimiento' => 'bg-amber-100 text-amber-800',
                            default => 'bg-gray-100 text-gray-800'
                        };
                    @endphp
                    <span class="px-3 py-1 {{ $estadoColor }} rounded-full text-sm font-medium">{{ ucfirst($quirofano->estado) }}</span>
                </div>
                
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Creado</span>
                    <span class="text-sm text-gray-900">{{ $quirofano->created_at?->format('d/m/Y H:i') ?? 'N/A' }}</span>
                </div>
                
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Última actualización</span>
                    <span class="text-sm text-gray-900">{{ $quirofano->updated_at?->format('d/m/Y H:i') ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Estadísticas</h3>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Citas Programadas</span>
                    <span class="text-lg font-bold text-gray-900">{{ $quirofano->citasQuirurgicas()->count() }}</span>
                </div>
                
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Citas Pendientes</span>
                    <span class="text-lg font-bold text-amber-600">{{ $quirofano->citasQuirurgicas()->where('estado', 'pendiente')->count() }}</span>
                </div>
                
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-gray-700">Citas Completadas</span>
                    <span class="text-lg font-bold text-green-600">{{ $quirofano->citasQuirurgicas()->where('estado', 'completada')->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Historial de Citas -->
    @if($quirofano->citasQuirurgicas()->count() > 0)
    <div class="mt-6 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Historial de Citas</h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-gray-600 text-xs uppercase font-bold">
                    <tr>
                        <th class="px-4 py-3">Fecha</th>
                        <th class="px-4 py-3">Hora</th>
                        <th class="px-4 py-3">Paciente</th>
                        <th class="px-4 py-3">Cirugía</th>
                        <th class="px-4 py-3">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($quirofano->citasQuirurgicas()->orderBy('fecha', 'desc')->take(10)->get() as $cita)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $cita->fecha?->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $cita->hora_inicio_estimada }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $cita->paciente->nombre ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $cita->cirugia->nombre ?? 'N/A' }}</td>
                        <td class="px-4 py-3">
                            @php
                                $citaEstadoColor = match($cita->estado) {
                                    'pendiente' => 'bg-yellow-100 text-yellow-800',
                                    'programada' => 'bg-blue-100 text-blue-800',
                                    'en_curso' => 'bg-orange-100 text-orange-800',
                                    'completada' => 'bg-green-100 text-green-800',
                                    'cancelada' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="px-2 py-1 {{ $citaEstadoColor }} rounded-full text-xs font-medium">{{ ucfirst($cita->estado) }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
