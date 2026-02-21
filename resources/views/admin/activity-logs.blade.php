@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Bitácora de Actividades</h1>
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Registros en tiempo real
                </div>
            </div>
        </div>
        
        <!-- Filtros -->
        <div class="p-6 border-b bg-gray-50">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Usuario</label>
                    <select name="user_id" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">Todos</option>
                        @foreach($users as $id => $name)
                            <option value="{{ $id }}" {{ request('user_id') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Acción</label>
                    <select name="action" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">Todas</option>
                        <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Inicio de sesión</option>
                        <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Cierre de sesión</option>
                        <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Creación</option>
                        <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Actualización</option>
                        <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Eliminación</option>
                        <option value="access" {{ request('action') == 'access' ? 'selected' : '' }}>Acceso</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                           class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                
                <div class="md:col-span-4 flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">
                        Filtrar
                    </button>
                    <a href="{{ route('seguridad.activity-logs.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300">
                        Limpiar
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Lista de logs -->
        <div class="p-6">
            <div class="space-y-3">
                @forelse($logs as $log)
                    <div class="flex items-start space-x-3 p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                        <!-- Icono de acción -->
                        <div class="flex-shrink-0 mt-1">
                            @switch($log->action)
                                @case('login')
                                    <span class="inline-flex items-center justify-center w-8 h-8 bg-green-100 text-green-600 rounded-full">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                        </svg>
                                    </span>
                                    @break
                                @case('logout')
                                    <span class="inline-flex items-center justify-center w-8 h-8 bg-red-100 text-red-600 rounded-full">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                    </span>
                                    @break
                                @case('create')
                                    <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-full">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </span>
                                    @break
                                @case('update')
                                    <span class="inline-flex items-center justify-center w-8 h-8 bg-yellow-100 text-yellow-600 rounded-full">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </span>
                                    @break
                                @case('delete')
                                    <span class="inline-flex items-center justify-center w-8 h-8 bg-red-100 text-red-600 rounded-full">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </span>
                                    @break
                                @default
                                    <span class="inline-flex items-center justify-center w-8 h-8 bg-gray-100 text-gray-600 rounded-full">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </span>
                            @endswitch
                        </div>
                        
                        <!-- Contenido -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-medium text-gray-900">{{ $log->user->name }}</span>
                                        <span class="text-gray-400">•</span>
                                        <span class="text-sm text-gray-600">{{ $log->description }}</span>
                                    </div>
                                    
                                    @if($log->old_values || $log->new_values)
                                        <div class="mt-2 p-3 bg-gray-50 rounded-lg text-sm">
                                            @if($log->action === 'update' && isset($log->old_values['is_active']))
                                                <div class="flex items-center gap-2">
                                                    <span class="font-medium text-gray-700">Estado cambiado:</span>
                                                    <span class="px-2 py-1 rounded text-xs font-medium
                                                        {{ $log->old_values['is_active'] ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                        {{ $log->old_values['is_active'] ? 'Activo' : 'Inactivo' }}
                                                    </span>
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                                    </svg>
                                                    <span class="px-2 py-1 rounded text-xs font-medium
                                                        {{ $log->new_values['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                        {{ $log->new_values['is_active'] ? 'Activo' : 'Inactivo' }}
                                                    </span>
                                                </div>
                                            @else
                                                @if($log->old_values)
                                                    <div class="text-red-600 mb-1">
                                                        <span class="font-medium">Antes:</span> 
                                                        {{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
                                                    </div>
                                                @endif
                                                @if($log->new_values)
                                                    <div class="text-green-600">
                                                        <span class="font-medium">Después:</span> 
                                                        {{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    @endif
                                    
                                    <div class="mt-2 flex items-center gap-4 text-xs text-gray-500">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                            </svg>
                                            {{ $log->ip_address }}
                                        </span>
                                        <span>{{ $log->created_at->format('d/m/Y H:i:s') }}</span>
                                        <span>{{ $log->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p>No se encontraron registros de actividad</p>
                    </div>
                @endforelse
            </div>
            
            <!-- Paginación -->
            <div class="mt-6">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
