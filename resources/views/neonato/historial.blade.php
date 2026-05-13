@extends('layouts.app')

@section('content')
<style>
    @media screen { .print-only { display: none !important; } }
    @media print {
        @page { size: letter; margin: 10mm; }
        * { margin: 0 !important; padding: 0 !important; box-sizing: border-box !important; }
        body { font-family: 'Courier New', monospace !important; font-size: 10pt !important; line-height: 1.2 !important; color: #000 !important; background: #fff !important; }
        .no-print, nav, header, footer { display: none !important; }
        .print-only { display: block !important; width: 100% !important; max-width: 172mm !important; margin: 0 auto !important; }
    }
    .epson-page { font-family: 'Courier New', monospace; font-size: 10pt; line-height: 1.2; color: #000; max-width: 172mm; margin: 0 auto; padding: 10mm; white-space: pre-wrap; }
    .epson-pre { font-family: 'Courier New', monospace; font-size: 10pt; line-height: 1.2; white-space: pre-wrap; word-wrap: break-word; margin: 0; }
</style>

<div class="p-6 bg-gray-50 min-h-screen">
    <div class="flex items-center justify-between mb-6 no-print">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Historial — {{ $neonato->nombre_display }}</h1>
            <p class="text-sm text-gray-500">{{ $neonato->code }} · {{ $neonato->temp_id }} · Madre: {{ $neonato->madre_nombre ?? '—' }}</p>
        </div>
        <div class="flex gap-2 no-print">
            <button onclick="printEpson()"
                class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm hover:bg-gray-800">
                Imprimir Historial
            </button>
            <a href="{{ route('neonato.evaluar', $neonato->id) }}"
                class="px-4 py-2 bg-pink-600 text-white rounded-lg text-sm hover:bg-pink-700 font-medium">Evaluar</a>
            <a href="{{ route('neonato.show', $neonato->id) }}"
                class="px-4 py-2 border rounded-lg text-sm text-gray-700">Datos</a>
            <a href="{{ route('neonato.index') }}"
                class="px-4 py-2 border rounded-lg text-sm text-gray-700">← Volver</a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-green-100 px-4 py-3 text-green-800 text-sm">{{ session('success') }}</div>
    @endif

    {{-- Evaluaciones --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">Fecha</th>
                    <th class="px-4 py-3 text-left">Registrado por</th>
                    <th class="px-4 py-3 text-center">Ítems</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            @forelse($evaluaciones as $ev)
            <tbody x-data="{ open: false }">
                <tr class="border-t">
                    <td class="px-4 py-3">{{ $ev->created_at->setTimezone('America/La_Paz')->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3">{{ $ev->user?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-center">{{ $ev->items->count() }}</td>
                    <td class="px-4 py-3">
                        <div class="flex justify-end gap-2">
                            <button @click="open = !open"
                                class="px-3 py-1 border rounded text-xs text-gray-700 hover:bg-gray-50">
                                Detalle
                            </button>
                            @if(in_array(auth()->user()->role, ['admin', 'administrador']))
                            <form method="POST" action="{{ route('neonato.evaluar.destroy', [$neonato->id, $ev->id]) }}"
                                onsubmit="return confirm('¿Eliminar esta evaluación? Esta acción no se puede deshacer.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="px-3 py-1 bg-red-50 border border-red-200 rounded text-xs text-red-600 hover:bg-red-100">
                                    Eliminar
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                <tr x-show="open" x-cloak class="bg-gray-50 border-t">
                    <td colspan="4" class="px-6 py-4">

                        @if(!empty($ev->signos_vitales))
                        @php $sv = $ev->signos_vitales; @endphp
                        <p class="text-xs font-semibold text-gray-500 mb-2">Signos Vitales</p>
                        <div class="grid grid-cols-3 md:grid-cols-5 gap-3 mb-4">
                            @if(!empty($sv['temperatura']))
                            <div class="bg-white rounded-lg border border-gray-100 px-3 py-2 text-center">
                                <span class="block text-xs text-gray-400">Temperatura</span>
                                <span class="font-semibold text-gray-800">{{ $sv['temperatura'] }}</span>
                                <span class="text-xs text-gray-400"> °C</span>
                            </div>
                            @endif
                            @if(!empty($sv['frecuencia_cardiaca']))
                            <div class="bg-white rounded-lg border border-gray-100 px-3 py-2 text-center">
                                <span class="block text-xs text-gray-400">Frec. Cardíaca</span>
                                <span class="font-semibold text-gray-800">{{ $sv['frecuencia_cardiaca'] }}</span>
                                <span class="text-xs text-gray-400"> lpm</span>
                            </div>
                            @endif
                            @if(!empty($sv['frecuencia_respiratoria']))
                            <div class="bg-white rounded-lg border border-gray-100 px-3 py-2 text-center">
                                <span class="block text-xs text-gray-400">Frec. Respiratoria</span>
                                <span class="font-semibold text-gray-800">{{ $sv['frecuencia_respiratoria'] }}</span>
                                <span class="text-xs text-gray-400"> rpm</span>
                            </div>
                            @endif
                            @if(!empty($sv['saturacion_o2']))
                            <div class="bg-white rounded-lg border border-gray-100 px-3 py-2 text-center">
                                <span class="block text-xs text-gray-400">SpO₂</span>
                                <span class="font-semibold text-gray-800">{{ $sv['saturacion_o2'] }}</span>
                                <span class="text-xs text-gray-400"> %</span>
                            </div>
                            @endif
                            @if(!empty($sv['glucosa']))
                            <div class="bg-white rounded-lg border border-gray-100 px-3 py-2 text-center">
                                <span class="block text-xs text-gray-400">Glucosa</span>
                                <span class="font-semibold text-gray-800">{{ $sv['glucosa'] }}</span>
                                <span class="text-xs text-gray-400"> mg/dL</span>
                            </div>
                            @endif
                            @if(!empty($sv['peso_actual']))
                            <div class="bg-white rounded-lg border border-gray-100 px-3 py-2 text-center">
                                <span class="block text-xs text-gray-400">Peso actual</span>
                                <span class="font-semibold text-gray-800">{{ $sv['peso_actual'] }}</span>
                                <span class="text-xs text-gray-400"> g</span>
                            </div>
                            @endif
                            @if(!empty($sv['color_piel']))
                            <div class="bg-white rounded-lg border border-gray-100 px-3 py-2 text-center">
                                <span class="block text-xs text-gray-400">Color de piel</span>
                                <span class="font-semibold text-gray-800 capitalize">{{ $sv['color_piel'] }}</span>
                            </div>
                            @endif
                            @if(!empty($sv['tono_muscular']))
                            <div class="bg-white rounded-lg border border-gray-100 px-3 py-2 text-center">
                                <span class="block text-xs text-gray-400">Tono muscular</span>
                                <span class="font-semibold text-gray-800 capitalize">{{ $sv['tono_muscular'] }}</span>
                            </div>
                            @endif
                        </div>
                        @endif

                        @if($ev->items->where('tipo','medicamento')->count())
                            <p class="text-xs font-semibold text-gray-500 mb-1">Medicamentos</p>
                            <ul class="mb-3 space-y-1">
                                @foreach($ev->items->where('tipo','medicamento') as $item)
                                    <li class="text-sm">{{ $item->nombre_snapshot }} &times; {{ $item->cantidad }}</li>
                                @endforeach
                            </ul>
                        @endif
                        @if($ev->items->where('tipo','insumo')->count())
                            <p class="text-xs font-semibold text-gray-500 mb-1">Insumos</p>
                            <ul class="mb-3 space-y-1">
                                @foreach($ev->items->where('tipo','insumo') as $item)
                                    <li class="text-sm">{{ $item->nombre_snapshot }} &times; {{ $item->cantidad }}</li>
                                @endforeach
                            </ul>
                        @endif
                        @if($ev->items->where('tipo','procedimiento')->count())
                            <p class="text-xs font-semibold text-gray-500 mb-1">Procedimientos</p>
                            <ul class="mb-3 space-y-1">
                                @foreach($ev->items->where('tipo','procedimiento') as $item)
                                    <li class="text-sm flex justify-between">
                                        <span>{{ $item->nombre_snapshot }} &times; {{ $item->cantidad }}</span>
                                        <span class="text-gray-400">Bs. {{ number_format($item->precio_snapshot, 2) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        @if($ev->observaciones)
                            <p class="text-xs font-semibold text-gray-500 mb-1">Observaciones</p>
                            <p class="text-sm text-gray-700">{{ $ev->observaciones }}</p>
                        @endif
                    </td>
                </tr>
            </tbody>
            @empty
            <tbody>
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                        Sin evaluaciones registradas.
                        <a href="{{ route('neonato.evaluar', $neonato->id) }}" class="text-pink-600 hover:underline ml-1">Registrar primera evaluación</a>
                    </td>
                </tr>
            </tbody>
            @endforelse
        </table>
    </div>

    <div class="mt-4 no-print">{{ $evaluaciones->links() }}</div>

    {{-- Cunas --}}
    @if($usosCunas->isNotEmpty())
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden no-print">
        <div class="px-4 py-3 border-b bg-gray-50">
            <h2 class="text-sm font-semibold text-gray-700">Usos de Cuna ({{ $usosCunas->count() }})</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Cuna</th>
                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Inicio</th>
                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Fin</th>
                    <th class="px-4 py-2 text-right text-xs font-bold text-gray-500 uppercase">Costo (Bs.)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($usosCunas as $uso)
                <tr>
                    <td class="px-4 py-2">{{ $uso->camilla?->nombre }} <span class="text-gray-400">({{ $uso->camilla?->codigo }})</span></td>
                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($uso->fecha_inicio)->setTimezone('America/La_Paz')->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-2">{{ $uso->fecha_fin ? \Carbon\Carbon::parse($uso->fecha_fin)->setTimezone('America/La_Paz')->format('d/m/Y H:i') : '—' }}</td>
                    <td class="px-4 py-2 text-right font-medium">{{ number_format($uso->costo_calculado, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Cambios de estado --}}
    @if(!empty($neonato->status_logs))
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden no-print">
        <div class="px-4 py-3 border-b bg-gray-50">
            <h2 class="text-sm font-semibold text-gray-700">Cambios de estado ({{ count($neonato->status_logs) }})</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Fecha y hora</th>
                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Estado anterior</th>
                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Estado nuevo</th>
                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Registrado por</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach(array_reverse($neonato->status_logs) as $log)
                @php
                    $labels = [
                        'recibido'       => 'Recibido',
                        'en_observacion' => 'En Observación',
                        'estable'        => 'Estable',
                        'uti_neonatal'   => 'UTI Neonatal',
                        'alta'           => 'Alta',
                        'fallecido'      => 'Fallecido',
                    ];
                @endphp
                <tr>
                    <td class="px-4 py-2 text-gray-600">{{ $log['changed_at'] }}</td>
                    <td class="px-4 py-2 text-gray-500">{{ $labels[$log['status_anterior']] ?? $log['status_anterior'] }}</td>
                    <td class="px-4 py-2 font-medium text-gray-800">{{ $labels[$log['status_nuevo']] ?? $log['status_nuevo'] }}</td>
                    <td class="px-4 py-2 text-gray-600">{{ $log['user_name'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

<!-- Impresión Epson -->
<div class="print-only epson-page">
<pre class="epson-pre">
================================================================================
                              C E M S A
                     HISTORIAL NEONATAL - NEONATOLOGIA
================================================================================
RN     : {{ str_pad(strtoupper($neonato->nombre_display), 50, ' ') }}
CODIGO : {{ $neonato->code }}    ID: {{ $neonato->temp_id }}
MADRE  : {{ strtoupper($neonato->madre_nombre ?? 'SIN REGISTRO') }}
FECHA  : {{ now()->setTimezone('America/La_Paz')->format('d/m/Y H:i') }}
================================================================================
@forelse($evaluaciones as $index => $evaluacion)
@php
    $meds   = $evaluacion->items->where('tipo', 'medicamento');
    $insumos = $evaluacion->items->where('tipo', 'insumo');
    $procs  = $evaluacion->items->where('tipo', 'procedimiento');
    $sv     = $evaluacion->signos_vitales ?? [];
@endphp
EVALUACION #{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }} | {{ $evaluacion->created_at->setTimezone('America/La_Paz')->format('d/m/Y H:i') }}
--------------------------------------------------------------------------------
REGISTRADO POR: {{ strtoupper($evaluacion->user?->name ?? 'N/A') }}
@if(!empty($sv))
SIGNOS VITALES:
{{ !empty($sv['temperatura'])           ? '  TEMP: '.str_pad($sv['temperatura'].' C', 14) : '' }}{{ !empty($sv['frecuencia_cardiaca']) ? '  FC: '.str_pad($sv['frecuencia_cardiaca'].' lpm', 12) : '' }}{{ !empty($sv['frecuencia_respiratoria']) ? '  FR: '.$sv['frecuencia_respiratoria'].' rpm' : '' }}
{{ !empty($sv['saturacion_o2'])         ? '  SpO2: '.str_pad($sv['saturacion_o2'].' %', 12) : '' }}{{ !empty($sv['glucosa']) ? '  GLUCOSA: '.str_pad($sv['glucosa'].' mg/dL', 14) : '' }}{{ !empty($sv['peso_actual']) ? '  PESO: '.$sv['peso_actual'].' g' : '' }}
{{ !empty($sv['color_piel'])            ? '  COLOR PIEL: '.$sv['color_piel'] : '' }}{{ !empty($sv['tono_muscular']) ? '   TONO: '.$sv['tono_muscular'] : '' }}
@endif
@if($meds->count())
MEDICAMENTOS:
+----------------------------------------+------+
| NOMBRE                                 | CANT |
+----------------------------------------+------+
@foreach($meds as $item)
| {{ str_pad($item->nombre_snapshot, 38, ' ') }} | {{ str_pad($item->cantidad, 4, ' ') }} |
@endforeach
+----------------------------------------+------+
@endif
@if($insumos->count())
INSUMOS:
+----------------------------------------+------+
| NOMBRE                                 | CANT |
+----------------------------------------+------+
@foreach($insumos as $item)
| {{ str_pad($item->nombre_snapshot, 38, ' ') }} | {{ str_pad($item->cantidad, 4, ' ') }} |
@endforeach
+----------------------------------------+------+
@endif
@if($procs->count())
PROCEDIMIENTOS:
+----------------------------------------+------+----------+
| NOMBRE                                 | CANT | PRECIO   |
+----------------------------------------+------+----------+
@foreach($procs as $item)
| {{ str_pad($item->nombre_snapshot, 38, ' ') }} | {{ str_pad($item->cantidad, 4, ' ') }} | {{ str_pad(number_format($item->precio_snapshot, 2), 8, ' ', STR_PAD_LEFT) }} |
@endforeach
+----------------------------------------+------+----------+
@endif
@if($evaluacion->observaciones)
OBSERVACIONES:
    {{ wordwrap($evaluacion->observaciones, 76, "\n    ") }}
@endif
--------------------------------------------------------------------------------

@empty
                    *** SIN EVALUACIONES REGISTRADAS ***

@endforelse
@if($usosCunas->isNotEmpty())
================================================================================
                            USOS DE CUNA
================================================================================
+--------------------------------+-----------+-----------+-----------+
| CUNA                           | INICIO    | FIN       | COSTO Bs. |
+--------------------------------+-----------+-----------+-----------+
@foreach($usosCunas as $uso)
| {{ str_pad($uso->camilla?->nombre ?? '—', 30, ' ') }} | {{ \Carbon\Carbon::parse($uso->fecha_inicio)->format('d/m H:i') }}  | {{ $uso->fecha_fin ? \Carbon\Carbon::parse($uso->fecha_fin)->format('d/m H:i') : '  --  ' }}  | {{ str_pad(number_format($uso->costo_calculado, 2), 9, ' ', STR_PAD_LEFT) }} |
@endforeach
+--------------------------------+-----------+-----------+-----------+
@endif
@if(!empty($neonato->status_logs))
================================================================================
                        CAMBIOS DE ESTADO
================================================================================
+---------------------+--------------------+--------------------+
| FECHA/HORA          | ANTERIOR           | NUEVO              |
+---------------------+--------------------+--------------------+
@php
$statusLabels = ['recibido'=>'Recibido','en_observacion'=>'En Observacion','estable'=>'Estable','uti_neonatal'=>'UTI Neonatal','alta'=>'Alta','fallecido'=>'Fallecido'];
@endphp
@foreach($neonato->status_logs as $log)
| {{ str_pad($log['changed_at'], 19, ' ') }} | {{ str_pad($statusLabels[$log['status_anterior']] ?? $log['status_anterior'], 18, ' ') }} | {{ str_pad($statusLabels[$log['status_nuevo']] ?? $log['status_nuevo'], 18, ' ') }} |
  REGISTRADO POR: {{ $log['user_name'] }}
@endforeach
+---------------------+--------------------+--------------------+
@endif
================================================================================
                           FIN DEL HISTORIAL
================================================================================




</pre>
</div>

<script>
function printEpson() {
    var printWindow = window.open('', '_blank');
    var content = document.querySelector('.epson-page').innerHTML;
    printWindow.document.write(`
        <!DOCTYPE html><html><head>
        <meta charset="UTF-8">
        <title>HISTORIAL - {{ $neonato->code }}</title>
        <style>
            @page { size: letter; margin: 10mm; }
            body { font-family: 'Courier New', monospace; font-size: 10pt; line-height: 1.2; color: #000; background: #fff; margin: 0; padding: 10mm; width: 172mm; }
            pre { font-family: 'Courier New', monospace; font-size: 10pt; line-height: 1.2; white-space: pre-wrap; word-wrap: break-word; margin: 0; }
        </style></head><body>
        <pre>${content}</pre>
        <script>window.onload=function(){window.print()};window.onafterprint=function(){window.close()}<\/script>
        </body></html>
    `);
    printWindow.document.close();
}
</script>
@endsection
