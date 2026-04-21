@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50/50 min-h-screen">
    <!-- Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Actividad de Enfermera</h1>
            <p class="text-sm text-gray-500">{{ $enfermera->user?->name ?? 'Enfermera' }} - {{ $enfermera->turno_label }}</p>
        </div>
        <a href="{{ route('internacion-staff.enfermeras.index') }}" class="flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all shadow-sm">
            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    <!-- Info Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-800">{{ $enfermera->user?->name ?? 'N/A' }}</h2>
                <p class="text-sm text-gray-500">{{ $enfermera->tipo }}</p>
                <div class="flex gap-2 mt-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        @if($enfermera->turno == 'mañana') bg-yellow-100 text-yellow-800
                        @elseif($enfermera->turno == 'tarde') bg-orange-100 text-orange-800
                        @else bg-indigo-100 text-indigo-800 @endif">
                        {{ $enfermera->turno_label }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $enfermera->estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $enfermera->estado === 'activo' ? 'Activa' : 'Inactiva' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Actividad -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Historial de Actividad</h3>

        @if($actividad->count() > 0)
            <div class="space-y-4">
                @foreach($actividad as $log)
                <div class="flex gap-4 pb-4 border-b border-gray-100 last:border-0">
                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-medium text-gray-900">{{ $log->description }}</p>
                                <p class="text-sm text-gray-500">Acción: {{ $log->action }}</p>
                                @if($log->model_type)
                                    <p class="text-xs text-gray-400 mt-1">Modelo: {{ class_basename($log->model_type) }} #{{ $log->model_id }}</p>
                                @endif
                            </div>
                            <span class="text-sm text-gray-400">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $actividad->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-gray-500">No hay actividad registrada</p>
            </div>
        @endif
    </div>
</div>
@endsection
