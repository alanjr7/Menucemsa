@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50/50 min-h-screen">

    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Recién Nacidos</h1>
            <p class="text-sm text-gray-500">Registro histórico completo</p>
        </div>
        <a href="{{ route('admin.neonato.dashboard') }}"
            class="px-4 py-2 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50">← Volver</a>
    </div>

    {{-- Filtros --}}
    <form method="GET" class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6 flex flex-wrap gap-4">
        <div class="flex-1 min-w-48">
            <input type="text" name="search" value="{{ request('search') }}"
                class="w-full border border-gray-200 rounded-xl px-4 py-2 text-sm bg-gray-50 focus:outline-none focus:border-pink-400"
                placeholder="Código, nombre, madre...">
        </div>
        <div>
            <select name="status" class="border border-gray-200 rounded-xl px-4 py-2 text-sm bg-gray-50 focus:outline-none focus:border-pink-400">
                <option value="">Todos los estados</option>
                <option value="recibido"       {{ request('status') === 'recibido'       ? 'selected' : '' }}>Recibido</option>
                <option value="en_observacion" {{ request('status') === 'en_observacion' ? 'selected' : '' }}>En Observación</option>
                <option value="estable"        {{ request('status') === 'estable'        ? 'selected' : '' }}>Estable</option>
                <option value="uti_neonatal"   {{ request('status') === 'uti_neonatal'   ? 'selected' : '' }}>UTI Neonatal</option>
                <option value="alta"           {{ request('status') === 'alta'           ? 'selected' : '' }}>Alta</option>
                <option value="fallecido"      {{ request('status') === 'fallecido'      ? 'selected' : '' }}>Fallecido</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-pink-600 text-white rounded-xl text-sm hover:bg-pink-700">Buscar</button>
        @if(request('search') || request('status'))
            <a href="{{ route('admin.neonato.recien-nacidos') }}" class="px-4 py-2 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50">Limpiar</a>
        @endif
    </form>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800 text-sm">{{ $neonatos->total() }} registros</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Código</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">RN / Temp ID</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Madre</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Nacimiento</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Apgar</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($neonatos as $rn)
                        <tr class="hover:bg-gray-50/30" x-data="{ editStatus: false }">
                            <td class="px-6 py-3 text-xs font-mono font-bold text-pink-700">{{ $rn->code }}</td>
                            <td class="px-6 py-3">
                                <p class="text-sm font-medium text-gray-800">{{ $rn->nombre_display }}</p>
                                <p class="text-xs text-gray-400">{{ $rn->paciente?->temp_code }} · {{ $rn->sexo === 'M' ? 'Masculino' : ($rn->sexo === 'F' ? 'Femenino' : '—') }}</p>
                            </td>
                            <td class="px-6 py-3">
                                <p class="text-sm text-gray-700">{{ $rn->madre_nombre ?? '—' }}</p>
                                <p class="text-xs text-gray-400">CI: {{ $rn->madre_ci ?? '—' }}</p>
                            </td>
                            <td class="px-6 py-3 text-sm text-gray-600">
                                {{ $rn->fecha_hora_nacimiento?->setTimezone('America/La_Paz')->format('d/m/Y H:i') ?? '—' }}
                            </td>
                            <td class="px-6 py-3 text-center text-sm">
                                <span class="text-gray-700">{{ $rn->apgar1 ?? '—' }} / {{ $rn->apgar5 ?? '—' }}</span>
                            </td>
                            <td class="px-6 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    bg-{{ $rn->status_color }}-100 text-{{ $rn->status_color }}-700">
                                    {{ $rn->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <div class="flex justify-end items-start gap-2" x-data>
                                    <a href="{{ route('neonato.historial', $rn->id) }}"
                                        class="px-3 py-1 text-xs border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 whitespace-nowrap">
                                        Historial
                                    </a>
                                    <a href="{{ route('neonato.show', $rn->id) }}"
                                        class="px-3 py-1 text-xs border border-gray-200 rounded-lg text-gray-600 hover:bg-gray-50 whitespace-nowrap">
                                        Datos
                                    </a>
                                    <button type="button"
                                        @click="$dispatch('open-status-modal', { id: {{ $rn->id }}, nombre: '{{ addslashes($rn->nombre_display) }}', status: '{{ $rn->status }}' })"
                                        class="px-3 py-1 text-xs border border-pink-200 rounded-lg text-pink-600 hover:bg-pink-50 whitespace-nowrap">
                                        Estado
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">Sin registros</td>
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

{{-- Modal cambiar estado --}}
<div x-data="{
        show: false,
        id: null,
        nombre: '',
        status: '',
        statuses: {
            recibido: 'Recibido',
            en_observacion: 'En Observación',
            estable: 'Estable',
            uti_neonatal: 'UTI Neonatal',
            alta: 'Alta',
            fallecido: 'Fallecido'
        }
    }"
    @open-status-modal.window="show = true; id = $event.detail.id; nombre = $event.detail.nombre; status = $event.detail.status"
    x-show="show" x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center">

    <div class="absolute inset-0 bg-black/40" @click="show = false"></div>

    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-sm mx-4 p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-base font-bold text-gray-800">Cambiar estado</h3>
            <button @click="show = false" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>

        <p class="text-sm text-gray-500 mb-4" x-text="nombre"></p>

        <form method="POST" :action="'/neonato/' + id + '/status'">
            @csrf
            @method('PATCH')
            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-600 mb-1">Estado</label>
                <select name="status" x-model="status"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-pink-400">
                    <template x-for="(label, val) in statuses" :key="val">
                        <option :value="val" :selected="val === status" x-text="label"></option>
                    </template>
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit"
                    class="flex-1 px-4 py-2 bg-pink-600 text-white rounded-xl text-sm hover:bg-pink-700 font-medium">
                    Actualizar
                </button>
                <button type="button" @click="show = false"
                    class="flex-1 px-4 py-2 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
