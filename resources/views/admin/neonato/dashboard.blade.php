@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50/50 min-h-screen">

    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Neonatología — Dashboard</h1>
            <p class="text-sm text-gray-500">Admin · Monitoreo del área neonatal</p>
        </div>
        <a href="{{ route('dashboard') }}"
            class="flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 shadow-sm text-sm">
            ← Volver
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-8">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28">
            <span class="text-gray-500 text-xs font-medium mb-1">Ingresados hoy</span>
            <span class="text-2xl font-bold text-pink-600">{{ $stats['total_hoy'] }}</span>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28">
            <span class="text-gray-500 text-xs font-medium mb-1">Activos</span>
            <span class="text-2xl font-bold text-blue-600">{{ $stats['activos'] }}</span>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28">
            <span class="text-gray-500 text-xs font-medium mb-1">En Observación</span>
            <span class="text-2xl font-bold text-yellow-600">{{ $stats['en_observacion'] }}</span>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28">
            <span class="text-gray-500 text-xs font-medium mb-1">UTI Neonatal</span>
            <span class="text-2xl font-bold text-orange-600">{{ $stats['uti_neonatal'] }}</span>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28">
            <span class="text-gray-500 text-xs font-medium mb-1">Altas hoy</span>
            <span class="text-2xl font-bold text-green-600">{{ $stats['alta_hoy'] }}</span>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28">
            <span class="text-gray-500 text-xs font-medium mb-1">Cunas activas</span>
            <span class="text-2xl font-bold text-gray-700">{{ $stats['cunas_activas'] }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Recientes --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-800 text-sm">Últimos Ingresos</h3>
                <a href="{{ route('admin.neonato.recien-nacidos') }}" class="text-xs text-pink-600 hover:underline">Ver todos →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recientes as $rn)
                    <div class="px-6 py-3 flex items-center justify-between hover:bg-gray-50/50">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $rn->nombre_display }}</p>
                            <p class="text-xs text-gray-400">
                                {{ $rn->code }}
                                @if($rn->madre_nombre) · Madre: {{ $rn->madre_nombre }} @endif
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                bg-{{ $rn->status_color }}-100 text-{{ $rn->status_color }}-700 border border-{{ $rn->status_color }}-200">
                                {{ $rn->status_label }}
                            </span>
                            <a href="{{ route('admin.neonato.recien-nacidos.show', $rn->id) }}"
                                class="text-xs text-gray-400 hover:text-pink-600">Ver</a>
                        </div>
                    </div>
                @empty
                    <p class="px-6 py-8 text-center text-sm text-gray-400">Sin ingresos hoy</p>
                @endforelse
            </div>
        </div>

        {{-- Evaluaciones hoy --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-800 text-sm">Evaluaciones de hoy</h3>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($evaluaciones as $ev)
                    <div class="px-6 py-3">
                        <p class="text-sm text-gray-700">
                            <span class="font-medium">{{ $ev->paciente?->temp_code ?? $ev->paciente?->ci ?? '—' }}</span>
                            <span class="text-gray-400 text-xs ml-2">por {{ $ev->user?->name }}</span>
                        </p>
                        <p class="text-xs text-gray-400">{{ $ev->created_at->setTimezone('America/La_Paz')->format('H:i') }}</p>
                    </div>
                @empty
                    <p class="px-6 py-8 text-center text-sm text-gray-400">Sin evaluaciones hoy</p>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
