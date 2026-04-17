@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50/50 min-h-screen">
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('emergency-staff.enfermeras.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center gap-2 mb-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver al listado
        </a>
        <div class="flex justify-between items-end">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Actividad de Enfermera</h1>
                <p class="text-sm text-gray-500">Historial de acciones de {{ $enfermera->user?->name ?? 'la enfermera' }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('emergency-staff.auditoria') }}" class="flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition-all shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    Ver Auditoría General
                </a>
            </div>
        </div>
    </div>

    <!-- Info de la Enfermera -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <p class="text-sm text-gray-500 mb-1">Nombre</p>
                <p class="font-medium text-gray-900">{{ $enfermera->user?->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">CI</p>
                <p class="font-medium text-gray-900">{{ $enfermera->ci }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Email</p>
                <p class="font-medium text-gray-900">{{ $enfermera->user?->email ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Estado</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $enfermera->estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $enfermera->estado === 'activo' ? 'Activa' : 'Inactiva' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Actividad -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-800">Registro de Actividades</h2>
        </div>

        @if($actividad->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4">Fecha y Hora</th>
                        <th scope="col" class="px-6 py-4">Acción</th>
                        <th scope="col" class="px-6 py-4">Descripción</th>
                        <th scope="col" class="px-6 py-4">Paciente/Referencia</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($actividad as $log)
                    <tr class="bg-white border-b hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @switch($log->action)
                                    @case('evaluacion_paciente') bg-blue-100 text-blue-800 @break
                                    @case('cambio_estado_paciente') bg-yellow-100 text-yellow-800 @break
                                    @case('dar_alta_paciente') bg-green-100 text-green-800 @break
                                    @case('derivar_paciente') bg-purple-100 text-purple-800 @break
                                    @case('medicamento_aplicado') bg-orange-100 text-orange-800 @break
                                    @default bg-gray-100 text-gray-800
                                @endswitch
                            ">
                                {{ str_replace('_', ' ', $log->action) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            {{ $log->description }}
                        </td>
                        <td class="px-6 py-4">
                            @if($log->model_type === 'App\Models\Emergency' && $log->model_id)
                                <a href="{{ route('emergency-staff.show', $log->model_id) }}" class="text-blue-600 hover:text-blue-900">
                                    Ver Emergencia #{{ $log->model_id }}
                                </a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $actividad->links() }}
        </div>
        @else
        <div class="p-12 text-center">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-gray-500 text-lg">No hay actividades registradas</p>
            <p class="text-gray-400 text-sm mt-1">Las acciones de la enfermera aparecerán aquí</p>
        </div>
        @endif
    </div>
</div>
@endsection
