@extends('layouts.app')

@section('title', 'Habitación ' . $habitacion->id . ' - Internación')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Habitación {{ $habitacion->id }}</h1>
                <p class="text-gray-600 mt-1">{{ $habitacion->detalle ?? 'Sin detalle' }}</p>
            </div>
            <div class="flex gap-2">
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $habitacion->estado === 'disponible' ? 'bg-green-100 text-green-800' : ($habitacion->estado === 'ocupada' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                    {{ ucfirst($habitacion->estado) }}
                </span>
                <a href="{{ route('internacion-staff.habitaciones.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Mensajes -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Columna Principal - Camas -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Información General -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Información General</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-3 bg-blue-50 rounded-lg">
                        <p class="text-2xl font-bold text-blue-600">{{ $habitacion->capacidad }}</p>
                        <p class="text-xs text-gray-600">Capacidad Total</p>
                    </div>
                    <div class="text-center p-3 bg-green-50 rounded-lg">
                        <p class="text-2xl font-bold text-green-600">{{ $habitacion->camas->where('disponibilidad', 'disponible')->count() }}</p>
                        <p class="text-xs text-gray-600">Camas Disponibles</p>
                    </div>
                    <div class="text-center p-3 bg-yellow-50 rounded-lg">
                        <p class="text-2xl font-bold text-yellow-600">{{ $habitacion->camas->where('disponibilidad', 'ocupada')->count() }}</p>
                        <p class="text-xs text-gray-600">Camas Ocupadas</p>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <p class="text-2xl font-bold text-gray-600">{{ $habitacion->camas->where('disponibilidad', 'mantenimiento')->count() }}</p>
                        <p class="text-xs text-gray-600">En Mantenimiento</p>
                    </div>
                </div>
            </div>

            <!-- Lista de Camas -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Camas</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($habitacion->camas as $cama)
                        <div class="border rounded-lg p-4 {{ $cama->disponibilidad === 'disponible' ? 'border-green-200 bg-green-50' : ($cama->disponibilidad === 'ocupada' ? 'border-red-200 bg-red-50' : 'border-gray-200 bg-gray-50') }}">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <svg class="w-6 h-6 mr-2 {{ $cama->disponibilidad === 'disponible' ? 'text-green-600' : ($cama->disponibilidad === 'ocupada' ? 'text-red-600' : 'text-gray-600') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01"/>
                                    </svg>
                                    <span class="font-semibold text-gray-900">Cama {{ $cama->nro }}</span>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $cama->disponibilidad === 'disponible' ? 'bg-green-100 text-green-800' : ($cama->disponibilidad === 'ocupada' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($cama->disponibilidad) }}
                                </span>
                            </div>
                            
                            <p class="text-sm text-gray-600 mb-1">Tipo: {{ $cama->tipo }}</p>
                            <p class="text-sm text-gray-600 mb-2">Precio/Día: <span class="font-semibold text-indigo-600">Bs. {{ number_format($cama->precio_por_dia, 2) }}</span></p>

                            @if($cama->disponibilidad === 'ocupada' && $cama->hospitalizacionActiva)
                                <div class="mt-3 p-3 bg-white rounded border">
                                    <p class="text-sm font-medium text-gray-900">Paciente:</p>
                                    <p class="text-sm text-gray-700">{{ $cama->hospitalizacionActiva->paciente->nombre ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">CI: {{ $cama->hospitalizacionActiva->paciente->ci ?? 'N/A' }}</p>
                                    
                                    <form action="{{ route('internacion-staff.camas.liberar', $cama) }}" method="POST" class="mt-2">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition"
                                                onclick="return confirm('¿Liberar esta cama?')">
                                            Liberar Cama
                                        </button>
                                    </form>
                                </div>
                            @elseif($cama->disponibilidad === 'disponible')
                                <div class="mt-3">
                                    <button type="button"
                                            onclick="mostrarModalAsignar({{ $cama->id }})"
                                            class="w-full px-3 py-1 bg-indigo-600 text-white text-xs rounded hover:bg-indigo-700 transition">
                                        Asignar Paciente
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Columna Lateral -->
        <div class="space-y-6">
            <!-- Acciones -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Acciones</h3>
                <div class="space-y-3">
                    <a href="{{ route('internacion-staff.habitaciones.edit', $habitacion) }}" 
                       class="w-full flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Editar Habitación
                    </a>

                    @if($habitacion->estado === 'mantenimiento')
                        <!-- Habitación en mantenimiento - botón para activar -->
                        <form action="{{ route('internacion-staff.habitaciones.destroy', $habitacion) }}" method="POST"
                              onsubmit="return confirm('¿Activar esta habitación?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Activar Habitación
                            </button>
                        </form>
                    @else
                        <!-- Habitación activa - botón para mantenimiento -->
                        <form action="{{ route('internacion-staff.habitaciones.destroy', $habitacion) }}" method="POST"
                              onsubmit="return confirm('¿Marcar esta habitación en mantenimiento?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full flex items-center justify-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                Mantenimiento
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Pacientes sin Habitación -->
            @if($pacientesSinHabitacion->count() > 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-yellow-600 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Pacientes por Asignar
                    </h3>
                    <p class="text-sm text-gray-600 mb-3">{{ $pacientesSinHabitacion->count() }} pacientes esperando habitación</p>
                    
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @foreach($pacientesSinHabitacion as $hosp)
                            <div class="p-2 bg-gray-50 rounded text-sm">
                                <p class="font-medium text-gray-900">{{ $hosp->paciente->nombre ?? 'N/A' }}</p>
                                <p class="text-gray-500">CI: {{ $hosp->paciente->ci ?? 'N/A' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Asignar Paciente -->
<div id="modalAsignar" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
        <div class="bg-indigo-600 text-white p-4 rounded-t-xl">
            <h3 class="text-lg font-bold">Asignar Paciente a Cama</h3>
        </div>
        
        <form action="{{ route('internacion-staff.habitaciones.asignar-paciente', $habitacion) }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="cama_id" id="camaIdInput">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Paciente</label>
                <select name="hospitalizacion_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Seleccione un paciente...</option>
                    @foreach($pacientesSinHabitacion as $hosp)
                        <option value="{{ $hosp->id }}">
                            {{ $hosp->paciente->nombre ?? 'N/A' }} (CI: {{ $hosp->paciente->ci ?? 'N/A' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="cerrarModal()"
                        class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    Cancelar
                </button>
                <button type="submit"
                        class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    Asignar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function mostrarModalAsignar(camaId) {
        document.getElementById('camaIdInput').value = camaId;
        document.getElementById('modalAsignar').classList.remove('hidden');
    }

    function cerrarModal() {
        document.getElementById('modalAsignar').classList.add('hidden');
    }

    // Cerrar modal al hacer clic fuera
    document.getElementById('modalAsignar').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModal();
        }
    });
</script>
@endsection
