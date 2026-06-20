@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- Encabezado --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Proformas
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Cotizaciones de servicios para entregar al paciente.
                @if($esGestor)
                    <span class="font-semibold text-emerald-700">Estás viendo las de todo el personal.</span>
                @else
                    Aquí ves las que tú creaste.
                @endif
            </p>
        </div>
        <a href="{{ route('proformas.create') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva Proforma
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    {{-- Búsqueda --}}
    <form method="GET" class="mb-4">
        <div class="relative max-w-md">
            <svg class="w-5 h-5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Buscar por Nº, paciente o documento…"
                   class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>
    </form>

    {{-- Tabla --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Nº Proforma</th>
                        <th class="px-4 py-3 text-left font-semibold">Fecha</th>
                        <th class="px-4 py-3 text-left font-semibold">Paciente</th>
                        @if($esGestor)<th class="px-4 py-3 text-left font-semibold">Creado por</th>@endif
                        <th class="px-4 py-3 text-right font-semibold">Total (Bs)</th>
                        <th class="px-4 py-3 text-center font-semibold">Vigencia</th>
                        <th class="px-4 py-3 text-right font-semibold">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($proformas as $p)
                        @php $vencida = $p->fecha_vencimiento && $p->fecha_vencimiento->isPast(); @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-4 py-3">
                                <a href="{{ route('proformas.show', $p) }}" class="font-bold text-emerald-700 hover:underline">{{ $p->numero }}</a>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $p->created_at->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-800">{{ $p->paciente_nombre }}</div>
                                @if($p->paciente_documento)<div class="text-xs text-slate-400">{{ $p->paciente_documento }}</div>@endif
                            </td>
                            @if($esGestor)<td class="px-4 py-3 text-slate-600">{{ $p->user->name ?? '—' }}</td>@endif
                            <td class="px-4 py-3 text-right font-semibold text-slate-800">{{ number_format($p->total, 2) }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($vencida)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-500">Vencida</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Vigente</span>
                                @endif
                                <div class="text-[11px] text-slate-400 mt-0.5">hasta {{ optional($p->fecha_vencimiento)->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('proformas.imprimir', $p) }}" target="_blank"
                                       title="Imprimir" class="p-2 rounded-lg text-slate-500 hover:text-emerald-700 hover:bg-emerald-50 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    </a>
                                    <a href="{{ route('proformas.edit', $p) }}"
                                       title="Editar" class="p-2 rounded-lg text-slate-500 hover:text-blue-700 hover:bg-blue-50 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('proformas.destroy', $p) }}" method="POST"
                                          onsubmit="return confirm('¿Eliminar la proforma {{ $p->numero }}? Esta acción no se puede deshacer.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Eliminar" class="p-2 rounded-lg text-slate-500 hover:text-red-700 hover:bg-red-50 transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $esGestor ? 7 : 6 }}" class="px-4 py-16 text-center">
                                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="text-slate-500 font-medium">No hay proformas todavía.</p>
                                <a href="{{ route('proformas.create') }}" class="text-emerald-600 hover:underline text-sm font-semibold">Crear la primera →</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $proformas->links() }}</div>
</div>
@endsection
