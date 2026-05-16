@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen">

    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Neonatología — Recién Nacidos</h1>
            <p class="text-sm text-gray-500">Ingresos activos en el área</p>
        </div>
        <a href="{{ route('neonato.create') }}"
            class="flex items-center gap-2 px-4 py-2 bg-pink-600 text-white rounded-xl text-sm hover:bg-pink-700 shadow-sm font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Añadir RN
        </a>
    </div>

    {{-- Stats rápidas --}}
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-10 h-10 bg-pink-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">Ingresados hoy</p>
                <p class="text-xl font-bold text-pink-600">{{ $statsHoy['ingresados_hoy'] }}</p>
            </div>
        </div>
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500">Activos</p>
                <p class="text-xl font-bold text-blue-600">{{ $statsHoy['activos'] }}</p>
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <form method="GET" class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6 flex flex-wrap gap-4">
        <div class="flex-1 min-w-48 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}"
                class="w-full pl-9 pr-3 py-2 border border-gray-200 rounded-xl text-sm bg-gray-50 placeholder-gray-400 focus:outline-none focus:border-pink-400"
                placeholder="Código, nombre, madre...">
        </div>
        <select name="status" class="border border-gray-200 rounded-xl px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:border-pink-400">
            <option value="">Activos (todos)</option>
            @foreach($statuses as $val => $label)
                <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-pink-600 text-white rounded-xl text-sm hover:bg-pink-700">Buscar</button>
        @if(request('search') || request('status'))
            <a href="{{ route('neonato.index') }}" class="px-4 py-2 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50">Limpiar</a>
        @endif
    </form>

    {{-- Tabla --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800 text-sm">{{ $neonatos->total() }} resultado(s)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Código</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Recién Nacido</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Madre</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Apgar 1'/5'</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($neonatos as $rn)
                        <tr class="hover:bg-pink-50/20 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-xs font-mono font-bold text-pink-700 bg-pink-50 px-2 py-1 rounded">{{ $rn->code }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-gray-800">{{ $rn->nombre_display }}</p>
                                <p class="text-xs text-gray-400">{{ $rn->identificador }} · {{ $rn->sexo === 'M' ? 'Masculino' : ($rn->sexo === 'F' ? 'Femenino' : 'Sin definir') }}</p>
                                @if($rn->peso)
                                    <p class="text-xs text-gray-400">{{ $rn->peso }} g · {{ $rn->talla }} cm</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-700">{{ $rn->madre_nombre ?? '—' }}</p>
                                @if($rn->madre_ci)
                                    <p class="text-xs text-gray-400">CI: {{ $rn->madre_ci }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-mono text-gray-700">{{ $rn->apgar1 ?? '—' }} / {{ $rn->apgar5 ?? '—' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    bg-{{ $rn->status_color }}-100 text-{{ $rn->status_color }}-700 border border-{{ $rn->status_color }}-200">
                                    {{ $rn->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('neonato.evaluar', $rn->id) }}"
                                        class="px-3 py-1.5 bg-pink-600 text-white rounded-lg text-xs hover:bg-pink-700 font-medium">
                                        Evaluar
                                    </a>
                                    <a href="{{ route('neonato.historial', $rn->id) }}"
                                        class="px-3 py-1.5 border border-gray-200 rounded-lg text-xs text-gray-600 hover:bg-gray-50">
                                        Historial
                                    </a>
                                    <a href="{{ route('neonato.show', $rn->id) }}"
                                        class="px-3 py-1.5 border border-gray-200 rounded-lg text-xs text-gray-600 hover:bg-gray-50">
                                        Datos
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <svg class="w-12 h-12 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <p class="text-gray-400 text-sm">Sin recién nacidos activos.</p>
                                <a href="{{ route('neonato.create') }}" class="text-pink-600 text-sm hover:underline mt-1 inline-block">Registrar uno</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($neonatos->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">{{ $neonatos->links() }}</div>
        @endif
    </div>
</div>
@endsection
