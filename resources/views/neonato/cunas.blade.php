@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen" x-data>

    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Cunas — Neonatología</h1>
            <p class="text-sm text-gray-500">Registrar uso de cuna por recién nacido</p>
        </div>
        <a href="{{ route('neonato.index') }}"
            class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">
            ← Volver al panel
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-green-100 px-4 py-3 text-green-800 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-xl bg-red-100 px-4 py-3 text-red-800 text-sm">{{ session('error') }}</div>
    @endif

    @if($cunas->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl px-4 py-3 text-yellow-800 text-sm mb-6">
            No hay cunas activas configuradas. El administrador debe crearlas primero.
        </div>
    @endif

    @php $preciosCunas = json_encode($preciosCunas); @endphp

    {{-- Búsqueda --}}
    <form method="GET" class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6 flex gap-4">
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}"
                class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm bg-gray-50 focus:outline-none focus:border-pink-400"
                placeholder="Buscar por código, nombre o madre...">
        </div>
        <button type="submit" class="px-4 py-2.5 bg-pink-600 text-white rounded-xl hover:bg-pink-700 text-sm font-medium">Buscar</button>
        @if(request('search'))
            <a href="{{ route('neonato.cunas') }}" class="px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 text-sm hover:bg-gray-50">Limpiar</a>
        @endif
    </form>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800 text-sm">Recién Nacidos Activos ({{ $neonatos->total() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Código</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Recién Nacido</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Madre</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($neonatos as $rn)
                        <tr class="hover:bg-pink-50/20 transition-colors"
                            x-data="{
                                open: false,
                                cuna_id: '',
                                fecha_inicio: '',
                                fecha_fin: '',
                                precios: {{ $preciosCunas }},
                                get precio_hora() { return this.cuna_id ? (parseFloat(this.precios[this.cuna_id]) || 0) : 0; },
                                get horas() {
                                    if (!this.fecha_inicio || !this.fecha_fin) return 0;
                                    const diff = (new Date(this.fecha_fin) - new Date(this.fecha_inicio)) / 3600000;
                                    return diff > 0 ? Math.max(0.5, Math.round(diff * 100) / 100) : 0;
                                },
                                get costo() { return (this.horas * this.precio_hora).toFixed(2); }
                            }">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-xs font-mono font-bold text-pink-700 bg-pink-50 px-2 py-1 rounded">{{ $rn->code }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-gray-800">{{ $rn->nombre_display }}</p>
                                <p class="text-xs text-gray-400">{{ $rn->temp_id }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $rn->madre_nombre ?? '—' }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    bg-{{ $rn->status_color }}-100 text-{{ $rn->status_color }}-700">
                                    {{ $rn->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($cunas->isEmpty())
                                    <span class="text-xs text-gray-400">Sin cunas disponibles</span>
                                @else
                                    <button @click="open = !open"
                                        class="inline-flex items-center px-3 py-1.5 border border-pink-200 text-xs font-medium rounded-lg text-pink-700 bg-pink-50 hover:bg-pink-100">
                                        Registrar uso de cuna
                                    </button>
                                @endif

                                {{-- Modal --}}
                                <div x-show="open" x-cloak
                                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
                                    @keydown.escape.window="open = false">
                                    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-6" @click.stop>
                                        <div class="flex justify-between items-center mb-4">
                                            <h3 class="text-base font-semibold text-gray-800">Registrar uso de cuna</h3>
                                            <button @click="open = false" class="text-gray-400 hover:text-gray-600">✕</button>
                                        </div>
                                        <p class="text-sm text-gray-500 mb-4">
                                            RN: <strong>{{ $rn->nombre_display }}</strong> ({{ $rn->code }})
                                            @if($rn->madre_nombre) · Madre: {{ $rn->madre_nombre }} @endif
                                        </p>

                                        <form method="POST" action="{{ route('neonato.cunas.store') }}">
                                            @csrf
                                            <input type="hidden" name="neonato_id" value="{{ $rn->id }}">

                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Cuna <span class="text-red-500">*</span></label>
                                                    <select name="camilla_id" x-model="cuna_id" required
                                                        class="w-full border rounded-lg px-3 py-2 text-sm">
                                                        <option value="">Seleccionar cuna...</option>
                                                        @foreach($cunas as $cuna)
                                                            <option value="{{ $cuna->id }}">
                                                                {{ $cuna->nombre }} ({{ $cuna->codigo }}) — Bs. {{ $cuna->precio_por_hora }}/h
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha y hora de inicio <span class="text-red-500">*</span></label>
                                                    <input type="datetime-local" name="fecha_inicio" x-model="fecha_inicio" required
                                                        class="w-full border rounded-lg px-3 py-2 text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha y hora de fin <span class="text-red-500">*</span></label>
                                                    <input type="datetime-local" name="fecha_fin" x-model="fecha_fin" required
                                                        class="w-full border rounded-lg px-3 py-2 text-sm">
                                                </div>
                                                <div x-show="horas > 0" class="rounded-lg bg-pink-50 border border-pink-100 px-4 py-3 text-sm">
                                                    <div class="flex justify-between text-pink-800">
                                                        <span>Horas: <strong x-text="horas"></strong></span>
                                                        <span>Costo: <strong>Bs. <span x-text="costo"></span></strong></span>
                                                    </div>
                                                    <p class="text-xs text-pink-500 mt-1">Mínimo 0.5 horas · Cargo a cuenta de la madre / familia</p>
                                                </div>
                                            </div>

                                            <div class="mt-6 flex gap-3 justify-end">
                                                <button type="button" @click="open = false"
                                                    class="px-4 py-2 border rounded-lg text-sm text-gray-600">Cancelar</button>
                                                <button type="submit"
                                                    class="px-4 py-2 bg-pink-600 text-white rounded-lg text-sm hover:bg-pink-700">Guardar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                <p>Sin recién nacidos activos</p>
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
