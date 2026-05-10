@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen">

        <!-- Page Header -->
        <div class="flex justify-between items-end mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Maestro Único de Pacientes</h1>
                <p class="text-sm text-gray-500">Pacientes registrados y pagados en el sistema</p>
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
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Pacientes</p>
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

        <!-- Search and Filter Bar -->
        <form method="GET" class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6 flex flex-col md:flex-row gap-4">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" 
                       name="search"
                       value="{{ request('search') }}"
                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:placeholder-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:text-sm transition-colors" 
                       placeholder="Buscar por nombre, documento o código de registro...">
            </div>
            
            <select name="estado" class="px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:text-sm">
                <option value="">Todos los estados</option>
                <option value="hospitalizado" {{ request('estado') == 'hospitalizado' ? 'selected' : '' }}>Hospitalizados</option>
                <option value="emergencia" {{ request('estado') == 'emergencia' ? 'selected' : '' }}>Emergencias</option>
            </select>
            
            <button type="submit" class="flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-medium transition-colors shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Buscar
            </button>
            
            @if(request('search') || request('estado'))
                <a href="{{ route('patients.index') }}" class="flex items-center justify-center px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 bg-white hover:bg-gray-50 font-medium transition-colors shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Limpiar
                </a>
            @endif
        </form>

        <!-- Patients Table Container -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Table Header Title -->
            <div class="px-6 py-4 border-b border-gray-100 bg-white flex justify-between items-center">
                <h3 class="text-gray-800 font-bold text-sm">Pacientes Registrados ({{ $pacientes->total() }})</h3>
                <div class="flex gap-2">
                    <!-- <button onclick="window.print()" class="p-2 text-gray-400 hover:text-gray-600 transition-colors" title="Imprimir">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                    </button> -->
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Código</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nombre Completo</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Carnet</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Seguro</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Ingreso</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($pacientes as $paciente)
                            <tr class="hover:bg-gray-50/50 transition-colors {{ isset($paciente->is_temporal) && $paciente->is_temporal ? 'bg-red-50/30' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                    @if(isset($paciente->is_temporal) && $paciente->is_temporal)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                            {{ $paciente->emergency_code }}
                                        </span>
                                    @else
                                        @php $cajaId = $paciente->consultas->first()?->caja?->id @endphp
                                        {{ $cajaId ?? $paciente->registro_codigo }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    <div class="flex items-center">
                                        @if(isset($paciente->is_temporal) && $paciente->is_temporal)
                                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2 animate-pulse"></span>
                                            <span class="font-medium text-red-700">{{ $paciente->nombre }}</span>
                                        @else
                                            {{ $paciente->nombre }}
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">{{ $paciente->ci }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if(isset($paciente->is_temporal) && $paciente->is_temporal)
                                        <span class="text-xs text-red-500">Emergencia - {{ $paciente->tipo_ingreso }}</span>
                                    @else
                                        {{ $paciente->seguro->nombre_empresa ?? 'Particular' }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if(isset($paciente->is_temporal) && $paciente->is_temporal)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5 animate-pulse"></span>
                                            Emergencia
                                        </span>
                                    @else
                                        @php
                                            $tipoIngreso = $paciente->tipo_ingreso ?? 'otro';
                                            $ingresoColor = 'gray';
                                            $ingresoLabel = 'Otro';
                                            
                                            switch($tipoIngreso) {
                                                case 'enfermeria':
                                                    $ingresoColor = 'purple';
                                                    $ingresoLabel = 'Enfermería';
                                                    break;
                                                case 'consulta_externa':
                                                    $ingresoColor = 'green';
                                                    $ingresoLabel = 'Consulta';
                                                    break;
                                                case 'emergencia':
                                                    $ingresoColor = 'red';
                                                    $ingresoLabel = 'Emergencia';
                                                    break;
                                                case 'internacion':
                                                    $ingresoColor = 'yellow';
                                                    $ingresoLabel = 'Internación';
                                                    break;
                                            }
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $ingresoColor }}-100 text-{{ $ingresoColor }}-800 border border-{{ $ingresoColor }}-200">
                                            <span class="w-1.5 h-1.5 bg-{{ $ingresoColor }}-500 rounded-full mr-1.5 {{ $ingresoColor === 'red' ? 'animate-pulse' : '' }}"></span>
                                            {{ $ingresoLabel }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @php
                                        $roleAreaRouteMap = [
                                            'emergencia'            => 'evaluacion.emergencia',
                                            'enfermera-emergencia'  => 'evaluacion.emergencia',
                                            'uti'                   => 'evaluacion.uti',
                                            'internacion'           => 'evaluacion.internacion',
                                            'enfermera-internacion' => 'evaluacion.internacion',
                                            ];
                                        $evalRoute = $roleAreaRouteMap[auth()->user()->role] ?? null;
                                    @endphp
                                    <div class="flex justify-end gap-2">
                                        @if($evalRoute)
                                            <a href="{{ route($evalRoute, $paciente->ci) }}"
                                                class="inline-flex items-center px-3 py-1.5 border border-blue-200 shadow-sm text-xs font-medium rounded-lg text-blue-700 bg-blue-50 hover:bg-blue-100 transition-all">
                                                Evaluar
                                            </a>
                                        @endif

                                        @if(isset($paciente->is_temporal) && $paciente->is_temporal)
                                            <a href="{{ route('emergency-staff.historial', $paciente->emergency_id) }}"
                                                class="inline-flex items-center px-3 py-1.5 border border-gray-200 shadow-sm text-xs font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-all">
                                                Historial
                                            </a>
                                            <a href="{{ route('reception.emergencia.comprobante', $paciente->emergency_id) }}"
                                                class="inline-flex items-center px-3 py-1.5 border border-gray-200 shadow-sm text-xs font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-all">
                                                Datos
                                            </a>
                                        @else
                                            <a href="{{ route('evaluacion.historial', $paciente->ci) }}"
                                                class="inline-flex items-center px-3 py-1.5 border border-gray-200 shadow-sm text-xs font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-all">
                                                Historial
                                            </a>
                                            @php
                                                $datosRoute = route('patients.show', $paciente->ci);
                                                if ($tipoIngreso === 'internacion' && $paciente->hospitalizaciones->isNotEmpty()) {
                                                    $datosRoute = route('reception.hospitalizacion.comprobante', $paciente->hospitalizaciones->first()->id);
                                                } elseif ($tipoIngreso === 'emergencia' && $paciente->emergencias->isNotEmpty()) {
                                                    $datosRoute = route('reception.emergencia.comprobante', $paciente->emergencias->first()->id);
                                                } elseif (in_array($tipoIngreso, ['consulta_externa', 'enfermeria', 'otro']) && $paciente->registro_codigo) {
                                                    $datosRoute = route('reception.confirmacion-registro', $paciente->registro_codigo);
                                                }
                                            @endphp
                                            <a href="{{ $datosRoute }}"
                                                class="inline-flex items-center px-3 py-1.5 border border-gray-200 shadow-sm text-xs font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-all">
                                                Datos
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
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
            
            <!-- Pagination -->
            @if($pacientes->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
                    {{ $pacientes->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection