@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen">

    <!-- Page Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Ajustes de Paciente</h1>
            <p class="text-sm text-gray-500">Corrección de cargos y cuenta del paciente</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm">
            {{ session('success') }}
        </div>
    @endif

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
            <a href="{{ route('admin.ajustes-pacientes.index') }}" class="flex items-center justify-center px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 bg-white hover:bg-gray-50 font-medium transition-colors shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Limpiar
            </a>
        @endif
    </form>

    <!-- Patients Table Container -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-white flex justify-between items-center">
            <h3 class="text-gray-800 font-bold text-sm">Pacientes Registrados ({{ $pacientes->total() }})</h3>
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
                        <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($pacientes as $paciente)
                        @php
                            $tipoIngreso = $paciente->tipo_ingreso ?? 'otro';
                            $ingreso = [
                                'enfermeria'       => ['purple', 'Enfermería'],
                                'consulta_externa' => ['green',  'Consulta'],
                                'emergencia'       => ['red',    'Emergencia'],
                                'internacion'      => ['yellow', 'Internación'],
                            ][$tipoIngreso] ?? ['gray', 'Otro'];
                            $cajaId = $paciente->consultas->first()?->caja?->id;
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                {{ $cajaId ?? $paciente->registro?->codigo ?? $paciente->temp_code }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $paciente->nombre }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">{{ $paciente->ci ?? $paciente->temp_code }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $paciente->seguro->nombre_empresa ?? 'Particular' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $ingreso[0] }}-100 text-{{ $ingreso[0] }}-800 border border-{{ $ingreso[0] }}-200">
                                    <span class="w-1.5 h-1.5 bg-{{ $ingreso[0] }}-500 rounded-full mr-1.5 {{ $ingreso[0] === 'red' ? 'animate-pulse' : '' }}"></span>
                                    {{ $ingreso[1] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.ajustes-pacientes.correcciones', $paciente->id) }}"
                                   class="inline-flex items-center px-3 py-1.5 border border-purple-200 shadow-sm text-xs font-medium rounded-lg text-purple-700 bg-purple-50 hover:bg-purple-100 transition-all">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Correcciones
                                </a>
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

        @if($pacientes->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
                {{ $pacientes->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
