@extends('layouts.app')

@section('title', 'Gestión de Emergencias')

@section('content')
<div class="p-6">
    <div class="max-w-7xl mx-auto">
        <div class="bg-white shadow-lg rounded-lg">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h1 class="text-2xl font-bold text-gray-900">Gestión de Emergencias</h1>
                    <!-- <a href="{{ route('admin.emergencies.create') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 hover:bg-blue-700 px-6 py-3 text-base font-medium text-white shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m0-8l7-7-7-7v8m0 0l-7 7-7v8m-7-7h18"/>
                        </svg>
                        <span class="ml-2">Registrar Nueva Emergencia</span>
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5v11H6V6l7-7z"/>
                        </svg>
                    </a> -->
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="p-6 grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="bg-blue-600 text-white p-4 rounded-lg">
                    <div class="text-center">
                        <h5 class="text-lg font-semibold">Total</h5>
                        <h3 class="text-3xl font-bold">{{ $stats['total'] }}</h3>
                    </div>
                </div>
                <div class="bg-yellow-500 text-white p-4 rounded-lg">
                    <div class="text-center">
                        <h5 class="text-lg font-semibold">Activas</h5>
                        <h3 class="text-3xl font-bold">{{ $stats['active'] }}</h3>
                    </div>
                </div>
                <div class="bg-red-600 text-white p-4 rounded-lg">
                    <div class="text-center">
                        <h5 class="text-lg font-semibold">UTI</h5>
                        <h3 class="text-3xl font-bold">{{ $stats['uti'] }}</h3>
                    </div>
                </div>
                <div class="bg-purple-600 text-white p-4 rounded-lg">
                    <div class="text-center">
                        <h5 class="text-lg font-semibold">Cirugía</h5>
                        <h3 class="text-3xl font-bold">{{ $stats['surgery'] }}</h3>
                    </div>
                </div>
                <div class="bg-green-600 text-white p-4 rounded-lg">
                    <div class="text-center">
                        <h5 class="text-lg font-semibold">Alta</h5>
                        <h3 class="text-3xl font-bold">{{ $stats['discharged'] }}</h3>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <form method="GET" action="{{ route('admin.emergencies.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                            <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Todos</option>
                                <option value="recibido" {{ request('status') == 'recibido' ? 'selected' : '' }}>Recibido</option>
                                <option value="en_evaluacion" {{ request('status') == 'en_evaluacion' ? 'selected' : '' }}>En Evaluación</option>
                                <option value="estabilizado" {{ request('status') == 'estabilizado' ? 'selected' : '' }}>Estabilizado</option>
                                <option value="uti" {{ request('status') == 'uti' ? 'selected' : '' }}>UTI</option>
                                <option value="cirugia" {{ request('status') == 'cirugia' ? 'selected' : '' }}>Cirugía</option>
                                <option value="alta" {{ request('status') == 'alta' ? 'selected' : '' }}>Alta</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha</label>
                            <input type="date" name="date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" value="{{ request('date') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pagado</label>
                            <select name="paid" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Todos</option>
                                <option value="1" {{ request('paid') == '1' ? 'selected' : '' }}>Pagado</option>
                                <option value="0" {{ request('paid') == '0' ? 'selected' : '' }}>No Pagado</option>
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium mr-2">Filtrar</button>
                            <a href="{{ route('admin.emergencies.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md text-sm font-medium">Limpiar</a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tabla de emergencias -->
            <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paciente</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Personal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Costo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pagado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($emergencies as $emergency)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $emergency->code }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $emergency->patient->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $emergency->status_color }}-100 text-{{ $emergency->status_color }}-800">
                                        {{ ucfirst($emergency->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $emergency->user->name ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($emergency->cost, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($emergency->paid)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Pagado</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Pendiente</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $emergency->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.emergencies.show', $emergency) }}" class="text-blue-600 hover:text-blue-900" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.emergencies.edit', $emergency) }}" class="text-yellow-600 hover:text-yellow-900" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if(!$emergency->paid)
                                            <form action="{{ route('admin.emergencies.mark-paid', $emergency) }}" method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900" 
                                                        title="Marcar como pagado"
                                                        onclick="return confirm('¿Marcar como pagado?')">
                                                    <i class="fas fa-dollar-sign"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.emergencies.destroy', $emergency) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" 
                                                    title="Eliminar"
                                                    onclick="return confirm('¿Eliminar esta emergencia?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">No hay emergencias registradas</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                {{ $emergencies->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
