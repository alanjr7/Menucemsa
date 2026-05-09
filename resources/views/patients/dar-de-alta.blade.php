@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen">

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm shadow-sm">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 flex items-center gap-3 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm shadow-sm">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Page Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dar de Alta a Pacientes</h1>
            <p class="text-sm text-gray-500">Gestión de altas médicas — solo pacientes sin cuentas pendientes pueden ser dados de alta</p>
        </div>
        <div class="flex gap-3">
            @if(in_array(auth()->user()->role, ['admin', 'administrador']))
                <a href="{{ route('patients.historial-altas') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 font-medium transition-colors shadow-sm text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Historial de Altas
                </a>
            @endif
            <a href="{{ route('patients.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 border border-gray-200 text-gray-600 bg-white rounded-xl hover:bg-gray-50 font-medium transition-colors shadow-sm text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al Maestro
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Pacientes Activos</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                </div>
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Hospitalizados</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['hospitalizados'] }}</p>
                </div>
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Emergencias</p>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['emergencias'] }}</p>
                </div>
                <div class="p-2 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <form method="GET" class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6 flex flex-col md:flex-row gap-4">
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 sm:text-sm transition-colors"
                   placeholder="Buscar por nombre, documento o código de registro...">
        </div>
        <select name="estado" class="px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 bg-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 sm:text-sm">
            <option value="">Todos los estados</option>
            <option value="hospitalizado" {{ request('estado') == 'hospitalizado' ? 'selected' : '' }}>Hospitalizados</option>
            <option value="emergencia" {{ request('estado') == 'emergencia' ? 'selected' : '' }}>Emergencias</option>
        </select>
        <button type="submit" class="flex items-center justify-center px-4 py-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 font-medium transition-colors shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            Buscar
        </button>
        @if(request('search') || request('estado'))
            <a href="{{ route('patients.dar-de-alta.index') }}" class="flex items-center justify-center px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 bg-white hover:bg-gray-50 font-medium transition-colors shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Limpiar
            </a>
        @endif
    </form>

    <!-- Info Banner -->
    <div class="mb-4 flex items-start gap-3 px-4 py-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm">
        <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>El sistema verificará automáticamente si el paciente tiene cuentas pendientes antes de procesar el alta. Los pacientes con deudas no podrán ser dados de alta.</span>
    </div>

    <!-- Patients Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-white flex justify-between items-center">
            <h3 class="text-gray-800 font-bold text-sm">Pacientes Disponibles para Alta ({{ $pacientes->total() }})</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Código</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nombre Completo</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Carnet</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Seguro</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Ingreso</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Cuentas</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($pacientes as $paciente)
                        @php
                            $tienePendientes = $paciente->cuentasPendientes->count() > 0;
                            $cajaId = $paciente->consultas->first()?->caja?->id;
                            $tipoIngreso = $paciente->tipo_ingreso ?? 'otro';
                            $ingresoColor = match($tipoIngreso) {
                                'enfermeria'      => 'purple',
                                'consulta_externa'=> 'green',
                                'emergencia'      => 'red',
                                'internacion'     => 'yellow',
                                default           => 'gray',
                            };
                            $ingresoLabel = match($tipoIngreso) {
                                'enfermeria'      => 'Enfermería',
                                'consulta_externa'=> 'Consulta',
                                'emergencia'      => 'Emergencia',
                                'internacion'     => 'Internación',
                                default           => 'Otro',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors {{ $tienePendientes ? 'bg-red-50/20' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                {{ $cajaId ?? $paciente->registro_codigo }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                {{ $paciente->nombre }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">{{ $paciente->ci }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $paciente->seguro->nombre_empresa ?? 'Particular' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $ingresoColor }}-100 text-{{ $ingresoColor }}-800 border border-{{ $ingresoColor }}-200">
                                    <span class="w-1.5 h-1.5 bg-{{ $ingresoColor }}-500 rounded-full mr-1.5 {{ $ingresoColor === 'red' ? 'animate-pulse' : '' }}"></span>
                                    {{ $ingresoLabel }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($tienePendientes)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 border border-red-200">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $paciente->cuentasPendientes->count() }} pendiente(s)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 border border-green-200">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Sin deudas
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('patients.show', $paciente->ci) }}"
                                       class="inline-flex items-center px-3 py-1.5 border border-gray-200 shadow-sm text-xs font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-all">
                                        Datos
                                    </a>
                                    @if(!$tienePendientes)
                                        <button type="button"
                                                onclick="abrirModalAlta('{{ $paciente->ci }}', '{{ addslashes($paciente->nombre) }}')"
                                                class="inline-flex items-center px-3 py-1.5 border border-emerald-200 shadow-sm text-xs font-medium rounded-lg text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition-all">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Dar de Alta
                                        </button>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 border border-gray-100 text-xs font-medium rounded-lg text-gray-400 bg-gray-50 cursor-not-allowed" title="Tiene cuentas pendientes">
                                            Dar de Alta
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-600 mb-2">No se encontraron pacientes</p>
                                    <p class="text-sm text-gray-400">No hay pacientes registrados que cumplan con los criterios de búsqueda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pacientes->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
                {{ $pacientes->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Dar de Alta -->
<div id="modal-dar-alta" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-modal="true">
    <div class="flex min-h-full items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="cerrarModalAlta()"></div>

        <!-- Modal Panel -->
        <div class="relative z-10 bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-100 rounded-xl">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-gray-800">Confirmar Alta Médica</h3>
                        <p id="modal-nombre-paciente" class="text-xs text-gray-500"></p>
                    </div>
                </div>
                <button onclick="cerrarModalAlta()" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Form -->
            <form id="form-dar-alta" method="POST" action="">
                @csrf
                <div class="px-6 py-5 space-y-4">
                    <!-- Warning -->
                    <div class="flex items-start gap-3 p-3 bg-amber-50 border border-amber-200 rounded-xl text-amber-800 text-sm">
                        <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p>Esta acción dará de alta al paciente del sistema. El paciente dejará de aparecer en el Maestro de Pacientes activos.</p>
                    </div>

                    <!-- Motivo de Alta -->
                    <div>
                        <label for="motivo_alta" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Motivo de Alta <span class="text-red-500">*</span>
                        </label>
                        <select id="motivo_alta" name="motivo_alta" required
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-gray-700 bg-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm">
                            <option value="alta_medica">Alta Médica</option>
                            <option value="voluntaria">Alta Voluntaria</option>
                            <option value="traslado">Traslado</option>
                            <option value="fallecimiento">Fallecimiento</option>
                        </select>
                    </div>

                    <!-- Observaciones -->
                    <div>
                        <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1.5">Observaciones</label>
                        <textarea id="observaciones" name="observaciones" rows="3"
                                  class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-gray-700 bg-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm resize-none"
                                  placeholder="Notas adicionales sobre el alta (opcional)..."></textarea>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50/50 rounded-b-2xl">
                    <button type="button" onclick="cerrarModalAlta()"
                            class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 rounded-xl hover:bg-gray-100 font-medium transition-colors text-sm">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 font-medium transition-colors text-sm shadow-sm">
                        Confirmar Alta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function abrirModalAlta(ci, nombre) {
    document.getElementById('modal-nombre-paciente').textContent = 'Paciente: ' + nombre;
    document.getElementById('form-dar-alta').action = '/patients/' + ci + '/dar-de-alta';
    document.getElementById('modal-dar-alta').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function cerrarModalAlta() {
    document.getElementById('modal-dar-alta').classList.add('hidden');
    document.body.style.overflow = '';
}
</script>
@endsection
