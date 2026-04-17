@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50/50 min-h-screen">
    <!-- Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Gestión de Enfermeras</h1>
            <p class="text-sm text-gray-500">Enfermeras del área de emergencia</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('emergency-staff.auditoria') }}" class="flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition-all shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                Ver Auditoría
            </a>
            <a href="{{ route('emergency-staff.enfermeras.create') }}" class="flex items-center px-4 py-2 bg-red-600 text-white font-medium rounded-xl hover:bg-red-700 transition-all shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva Enfermera
            </a>
        </div>
    </div>

    <!-- Alertas -->
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg mb-6">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <p class="text-green-700">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg mb-6">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-red-700">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- Tabla de Enfermeras -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 rounded-tl-lg">Nombre</th>
                        <th scope="col" class="px-6 py-4">CI</th>
                        <th scope="col" class="px-6 py-4">Email</th>
                        <th scope="col" class="px-6 py-4">Teléfono</th>
                        <th scope="col" class="px-6 py-4">Tipo</th>
                        <th scope="col" class="px-6 py-4">Estado</th>
                        <th scope="col" class="px-6 py-4 rounded-tr-lg">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($enfermeras as $enfermera)
                    <tr class="bg-white border-b hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $enfermera->user?->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4">{{ $enfermera->ci }}</td>
                        <td class="px-6 py-4">{{ $enfermera->user?->email ?? 'N/A' }}</td>
                        <td class="px-6 py-4">{{ $enfermera->telefono ?? 'No especificado' }}</td>
                        <td class="px-6 py-4">{{ $enfermera->tipo }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $enfermera->estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $enfermera->estado === 'activo' ? 'Activa' : 'Inactiva' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <a href="{{ route('emergency-staff.enfermeras.actividad', $enfermera) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                    </svg>
                                </a>
                                <a href="{{ route('emergency-staff.enfermeras.edit', $enfermera) }}" class="text-yellow-600 hover:text-yellow-900 bg-yellow-50 hover:bg-yellow-100 px-3 py-1.5 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <button onclick="toggleEstado({{ $enfermera->user_id }}, '{{ $enfermera->estado }}')" class="text-gray-600 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 px-3 py-1.5 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <p class="text-gray-500 text-lg mb-2">No hay enfermeras registradas</p>
                            <a href="{{ route('emergency-staff.enfermeras.create') }}" class="text-red-600 hover:text-red-700 font-medium">
                                Registrar primera enfermera →
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function toggleEstado(userId, estadoActual) {
    const nuevoEstado = estadoActual === 'activo' ? 'inactivo' : 'activo';
    const mensaje = estadoActual === 'activo' 
        ? '¿Estás seguro de desactivar esta enfermera?' 
        : '¿Estás seguro de activar esta enfermera?';
    
    if (confirm(mensaje)) {
        fetch(`/emergency-staff/enfermeras/${userId}/toggle-status`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cambiar el estado');
        });
    }
}
</script>
@endsection
