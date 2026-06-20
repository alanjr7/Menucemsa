@extends('layouts.app')

@section('content')
@php $vencida = $proforma->fecha_vencimiento && $proforma->fecha_vencimiento->isPast(); @endphp
<div class="max-w-4xl mx-auto">

    {{-- Barra de acciones --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('proformas.index') }}" class="p-2 rounded-lg text-slate-500 hover:bg-slate-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Proforma {{ $proforma->numero }}</h1>
                <p class="text-sm text-slate-500">Creada el {{ $proforma->created_at->format('d/m/Y H:i') }} por {{ $proforma->user->name ?? '—' }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('proformas.edit', $proforma) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-sm font-semibold rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Editar
            </a>
            <a href="{{ route('proformas.imprimir', $proforma) }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Imprimir
            </a>
        </div>
    </div>

    {{-- Tarjeta de detalle --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase">Paciente</p>
                <p class="text-lg font-bold text-slate-800">{{ $proforma->paciente_nombre }}</p>
                <p class="text-sm text-slate-500">
                    @if($proforma->paciente_documento) CI/NIT: {{ $proforma->paciente_documento }} @endif
                    @if($proforma->paciente_telefono) · Tel: {{ $proforma->paciente_telefono }} @endif
                </p>
            </div>
            <div class="text-right">
                @if($vencida)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-500">Vencida</span>
                @else
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Vigente</span>
                @endif
                <p class="text-xs text-slate-400 mt-1">Válida hasta {{ optional($proforma->fecha_vencimiento)->format('d/m/Y') }}</p>
            </div>
        </div>

        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-5 py-2.5 text-left font-semibold">Concepto</th>
                    <th class="px-3 py-2.5 text-center font-semibold">Cant.</th>
                    <th class="px-3 py-2.5 text-right font-semibold">P. Unit.</th>
                    <th class="px-5 py-2.5 text-right font-semibold">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($proforma->items as $item)
                    <tr>
                        <td class="px-5 py-3">
                            <div class="text-slate-800">{{ $item->descripcion }}</div>
                            @if($item->codigo_item)<div class="text-[11px] font-mono text-slate-400">{{ $item->codigo_item }}</div>@endif
                        </td>
                        <td class="px-3 py-3 text-center text-slate-600">{{ rtrim(rtrim(number_format($item->cantidad, 2), '0'), '.') }}</td>
                        <td class="px-3 py-3 text-right text-slate-600 tabular-nums">{{ number_format($item->precio_unitario, 2) }}</td>
                        <td class="px-5 py-3 text-right font-semibold text-slate-800 tabular-nums">{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="p-5 bg-slate-50 border-t border-slate-100">
            <div class="ml-auto max-w-xs space-y-1.5 text-sm">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal</span>
                    <span class="tabular-nums">Bs {{ number_format($proforma->items->sum('subtotal'), 2) }}</span>
                </div>
                @if($proforma->descuento > 0)
                    <div class="flex justify-between text-slate-600">
                        <span>Descuento</span>
                        <span class="tabular-nums">− Bs {{ number_format($proforma->descuento, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between pt-2 border-t border-slate-200">
                    <span class="text-base font-bold text-slate-800">TOTAL</span>
                    <span class="text-lg font-extrabold text-emerald-700 tabular-nums">Bs {{ number_format($proforma->total, 2) }}</span>
                </div>
            </div>
        </div>

        @if($proforma->observaciones)
            <div class="p-5 border-t border-slate-100">
                <p class="text-xs font-semibold text-slate-400 uppercase mb-1">Observaciones</p>
                <p class="text-sm text-slate-600 whitespace-pre-line">{{ $proforma->observaciones }}</p>
            </div>
        @endif
    </div>

    <p class="text-center text-xs text-slate-400 mt-4">
        Documento informativo. No constituye factura ni compromiso de pago. Los precios pueden variar.
    </p>
</div>
@endsection
