@extends('layouts.app')
@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Detalle de Enfermera</h1>
        <a href="{{ route('emergency-staff.enfermeras.index') }}" class="px-4 py-2 border rounded-lg text-gray-600 hover:bg-gray-50">← Volver</a>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <p class="text-gray-700"><strong>Nombre:</strong> {{ $enfermera->nombre_completo }}</p>
        <p class="text-gray-700 mt-2"><strong>CI:</strong> {{ $enfermera->ci }}</p>
        <p class="text-gray-700 mt-2"><strong>Área:</strong> {{ ucfirst($enfermera->area) }}</p>
        <p class="text-gray-700 mt-2"><strong>Turno:</strong> {{ $enfermera->turno_label }}</p>
        <p class="text-gray-700 mt-2"><strong>Estado:</strong> {{ ucfirst($enfermera->estado) }}</p>

        <h2 class="text-lg font-bold text-gray-800 mt-6 mb-3">Permisos Asignados</h2>
        <div class="flex flex-wrap gap-2">
            @foreach($enfermera->permissions as $perm)
                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">
                    {{ \App\Models\EnfermeraPermission::getPermissionLabel($perm->permission_key) }}
                </span>
            @endforeach
        </div>
    </div>
</div>
@endsection
