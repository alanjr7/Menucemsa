@extends('layouts.app')

@section('title', 'Nueva Habitación - Internación')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Nueva Habitación</h1>
                <p class="text-gray-600 mt-1">Crear habitación con sus camas para internación</p>
            </div>
            <a href="{{ route('internacion-staff.habitaciones.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </a>
        </div>
    </div>

    <!-- Mensajes de Error -->
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Formulario -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('internacion-staff.habitaciones.store') }}" method="POST" id="habitacionForm">
            @csrf

            <!-- Información de la Habitación -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">Información de la Habitación</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Código de Habitación -->
                    <div>
                        <label for="id" class="block text-sm font-medium text-gray-700 mb-1">
                            Código/Número <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="id" id="id" value="{{ old('id') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Ej: 101, A-1, UTI-01">
                        <p class="text-xs text-gray-500 mt-1">Identificador único de la habitación</p>
                    </div>

                    <!-- Capacidad -->
                    <div>
                        <label for="capacidad" class="block text-sm font-medium text-gray-700 mb-1">
                            Capacidad (N° de camas) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="capacidad" id="capacidad" value="{{ old('capacidad', 2) }}" required 
                               min="1" max="10" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="2">
                        <p class="text-xs text-gray-500 mt-1">Máximo 10 camas por habitación</p>
                    </div>

                    <!-- Detalle -->
                    <div>
                        <label for="detalle" class="block text-sm font-medium text-gray-700 mb-1">
                            Detalle/Descripción
                        </label>
                        <input type="text" name="detalle" id="detalle" value="{{ old('detalle') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Ej: Habitación individual con baño">
                    </div>
                </div>
            </div>

            <!-- Configuración de Camas -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">Configuración de Camas</h2>
                    <button type="button" onclick="actualizarCamas()" 
                            class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                        Actualizar según capacidad
                    </button>
                </div>

                <div id="camasContainer" class="space-y-4">
                    <!-- Las camas se generarán dinámicamente aquí -->
                </div>
            </div>

            <!-- Botones -->
            <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('internacion-staff.habitaciones.index') }}" 
                   class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition-colors">
                    Crear Habitación
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const tiposCama = @json($tiposCama);

    function generarFilaCama(index) {
        return `
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 bg-gray-50 rounded-lg">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        N° Cama <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="camas[${index}][nro]" required min="1"
                           value="${index + 1}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tipo de Cama <span class="text-red-500">*</span>
                    </label>
                    <select name="camas[${index}][tipo]" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        ${Object.entries(tiposCama).map(([value, label]) => 
                            `<option value="${value}">${label}</option>`
                        ).join('')}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Precio por Día (Bs) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="camas[${index}][precio_por_dia]" required min="0" step="0.01"
                           value="150.00"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="flex items-end">
                    <span class="text-sm text-gray-500">Cama ${index + 1} de <span class="totalCamas">${document.getElementById('capacidad').value}</span></span>
                </div>
            </div>
        `;
    }

    function actualizarCamas() {
        const capacidad = parseInt(document.getElementById('capacidad').value) || 2;
        const container = document.getElementById('camasContainer');
        
        let html = '';
        for (let i = 0; i < capacidad; i++) {
            html += generarFilaCama(i);
        }
        
        container.innerHTML = html;
    }

    // Inicializar al cargar
    document.addEventListener('DOMContentLoaded', actualizarCamas);

    // Actualizar cuando cambia la capacidad
    document.getElementById('capacidad').addEventListener('change', actualizarCamas);
</script>
@endsection
