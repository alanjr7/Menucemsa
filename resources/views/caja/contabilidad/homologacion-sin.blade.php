@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-6 lg:p-8 bg-[#f8fafc] min-h-screen font-sans">
    <div class="w-full max-w-4xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-3 mb-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Homologación SIN</h1>
                <p class="text-gray-500 text-sm">Códigos de producto/servicio homologados y dosificación autorizada (preparación para facturación electrónica).</p>
            </div>
            <a href="{{ route('caja.contabilidad.index') }}"
                class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg h-[38px]">
                Volver
            </a>
        </div>

        <div class="rounded-lg bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-800 mb-6">
            <strong>Preparatorio (SFE diferido).</strong> Estos códigos son un punto de partida y deben
            <strong>confirmarse con el Catálogo de Productos y Servicios del SIN</strong> según la actividad
            económica (CAEB) de la clínica. Se editan en <code>app/Support/CodigoSin.php</code>.
        </div>

        {{-- Mapa de homologación --}}
        <div class="bg-white shadow-sm rounded-lg border border-gray-100 overflow-hidden mb-6">
            <div class="px-4 py-3 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-700">Código interno → codigoProductoSin</h3>
            </div>
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Familia interna</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Concepto</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">codigoProductoSin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($mapa as $familia => $codigoSin)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 font-mono text-gray-600">{{ $familia }}</td>
                            <td class="px-4 py-2 text-gray-800">{{ $etiquetas[$familia] ?? '—' }}</td>
                            <td class="px-4 py-2 text-right font-mono font-semibold text-gray-900">{{ $codigoSin }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Dosificación / numeración autorizada --}}
        <div class="bg-white shadow-sm rounded-lg border border-gray-100 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-700">Dosificación vigente</h3>
            </div>
            <div class="p-4">
                @if ($dosificacion)
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm">
                        <div class="flex justify-between"><dt class="text-gray-500">Modalidad</dt><dd class="font-medium text-gray-800">{{ $dosificacion->modalidad_label }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">N° autorización</dt><dd class="font-mono text-gray-800">{{ $dosificacion->numero_autorizacion ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Llave</dt><dd class="font-mono text-gray-800">{{ $dosificacion->llave_dosificacion ?? '—' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Rango</dt><dd class="font-mono text-gray-800">{{ $dosificacion->rango_desde }} – {{ $dosificacion->rango_hasta ?? '∞' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Fecha límite emisión</dt><dd class="text-gray-800">{{ $dosificacion->fecha_limite_emision?->format('d/m/Y') ?? 'Sin límite' }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Estado</dt><dd class="font-medium {{ $dosificacion->vigente ? 'text-green-600' : 'text-red-600' }}">{{ $dosificacion->vigente ? 'Vigente' : 'No vigente' }}</dd></div>
                    </dl>
                    @if ($dosificacion->observaciones)
                        <p class="text-xs text-gray-400 mt-3">{{ $dosificacion->observaciones }}</p>
                    @endif
                @else
                    <p class="text-sm text-gray-400">No hay dosificación registrada. Cargar la dosificación real del SIN al adoptar facturación.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
