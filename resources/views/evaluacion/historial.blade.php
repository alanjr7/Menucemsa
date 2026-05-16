@extends('layouts.app')

@section('content')
<style>
    /* === ESTILOS PARA PANTALLA === */
    @media screen {
        .print-only { display: none !important; }
    }

    /* === ESTILOS PARA IMPRESION EPSON MATRICIAL === */
    @media print {
        @page {
            size: letter;
            margin: 10mm;
        }

        * {
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box !important;
        }

        body {
            font-family: 'Courier New', 'Courier', monospace !important;
            font-size: 10pt !important;
            line-height: 1.2 !important;
            color: #000 !important;
            background: #fff !important;
            width: 100% !important;
        }

        .no-print,
        .p-6,
        .bg-gray-50,
        .rounded-xl,
        .shadow-sm,
        .border,
        .mt-4,
        nav,
        header,
        footer {
            display: none !important;
        }

        .print-only {
            display: block !important;
            width: 100% !important;
            max-width: 172mm !important; /* 80 columnas a 10 CPI */
            margin: 0 auto !important;
            padding: 0 !important;
        }
    }

    /* === FORMATO EPSON ASCII PURO === */
    .epson-page {
        font-family: 'Courier New', 'Courier', monospace;
        font-size: 10pt;
        line-height: 1.2;
        color: #000;
        max-width: 172mm; /* Ancho para 80 columnas */
        margin: 0 auto;
        padding: 10mm;
        white-space: pre-wrap;
    }

    .epson-header {
        text-align: center;
        margin-bottom: 12px;
    }

    .epson-line {
        border: none;
        border-top: 1px dashed #666;
        margin: 8px 0;
    }

    .epson-double-line {
        border: none;
        border-top: 2px solid #000;
        margin: 8px 0;
    }

    .epson-section {
        margin-bottom: 10px;
        page-break-inside: avoid;
    }

    .epson-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 9pt;
        margin: 6px 0;
    }

    .epson-table th,
    .epson-table td {
        border: 1px solid #000;
        padding: 2px 4px;
        text-align: left;
        font-family: 'Courier New', 'Courier', monospace;
    }

    .epson-table th {
        background: #e0e0e0;
        font-weight: bold;
    }

    .epson-totals {
        margin-top: 15px;
        border-top: 2px solid #000;
        padding-top: 8px;
    }

    .epson-footer {
        text-align: center;
        margin-top: 20px;
        font-size: 9pt;
    }

    .epson-pre {
        font-family: 'Courier New', 'Courier', monospace;
        font-size: 10pt;
        line-height: 1.2;
        white-space: pre-wrap;
        word-wrap: break-word;
        margin: 0;
    }
</style>

<div class="p-6 bg-gray-50 min-h-screen">
    <div class="flex items-center justify-between mb-6 no-print">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Historial de Evaluaciones</h1>
            <p class="text-sm text-gray-500">{{ $paciente->nombre }} &bull; CI: {{ $paciente->ci }}</p>
        </div>
        <div class="flex gap-2 no-print">
            <button onclick="printEpson()"
                class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm hover:bg-gray-800">
                Imprimir Historial
            </button>
            <a href="{{ route('patients.index') }}" class="px-4 py-2 border rounded-lg text-sm text-gray-700">Volver</a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">Fecha</th>
                    <th class="px-4 py-3 text-left">Área</th>
                    <th class="px-4 py-3 text-left">Evaluado por</th>
                    <th class="px-4 py-3 text-center">Items</th>
                    <th class="px-4 py-3 text-right">Acciones</th>
                </tr>
            </thead>
            @forelse($evaluaciones as $ev)
            <tbody x-data="{ open: false }">
                    <tr class="border-t">
                        <td class="px-4 py-3">{{ $ev->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 capitalize">{{ $ev->area }}</td>
                        <td class="px-4 py-3">{{ $ev->user->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">{{ $ev->items->count() }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                <button @click="open = !open" class="px-3 py-1 border rounded text-xs text-gray-700">
                                    Detalle
                                </button>
                                <a href="{{ route('evaluacion.print', [$paciente->id, $ev->id]) }}" target="_blank"
                                    class="px-3 py-1 bg-gray-100 rounded text-xs text-gray-700">Imprimir</a>
                                @if(in_array(auth()->user()->role, ['admin', 'administrador']))
                                <form method="POST" action="{{ route('evaluacion.destroy', [$paciente->id, $ev->id]) }}"
                                    onsubmit="return confirm('¿Eliminar esta evaluación del {{ $ev->created_at->format('d/m/Y H:i') }}? Esta acción no se puede deshacer.')">
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
                        <td colspan="5" class="px-6 py-4">
                            @if(!empty($ev->signos_vitales))
                            @php $sv = $ev->signos_vitales; @endphp
                            <p class="text-xs font-semibold text-gray-500 mb-2">Signos Vitales</p>
                            <div class="grid grid-cols-3 md:grid-cols-5 gap-3 mb-4">
                                @if(!empty($sv['presion_arterial']))
                                <div class="bg-white rounded-lg border border-gray-100 px-3 py-2 text-center">
                                    <span class="block text-xs text-gray-400">Presión Arterial</span>
                                    <span class="font-semibold text-gray-800">{{ $sv['presion_arterial'] }}</span>
                                    <span class="text-xs text-gray-400"> mmHg</span>
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
                                @if(!empty($sv['temperatura']))
                                <div class="bg-white rounded-lg border border-gray-100 px-3 py-2 text-center">
                                    <span class="block text-xs text-gray-400">Temperatura</span>
                                    <span class="font-semibold text-gray-800">{{ $sv['temperatura'] }}</span>
                                    <span class="text-xs text-gray-400"> °C</span>
                                </div>
                                @endif
                                @if(!empty($sv['saturacion_o2']))
                                <div class="bg-white rounded-lg border border-gray-100 px-3 py-2 text-center">
                                    <span class="block text-xs text-gray-400">Saturación O₂</span>
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
                                @if(!empty($sv['peso']))
                                <div class="bg-white rounded-lg border border-gray-100 px-3 py-2 text-center">
                                    <span class="block text-xs text-gray-400">Peso</span>
                                    <span class="font-semibold text-gray-800">{{ $sv['peso'] }}</span>
                                    <span class="text-xs text-gray-400"> kg</span>
                                </div>
                                @endif
                                @if(!empty($sv['altura']))
                                <div class="bg-white rounded-lg border border-gray-100 px-3 py-2 text-center">
                                    <span class="block text-xs text-gray-400">Altura</span>
                                    <span class="font-semibold text-gray-800">{{ $sv['altura'] }}</span>
                                    <span class="text-xs text-gray-400"> cm</span>
                                </div>
                                @endif
                                @if(!empty($sv['imc']))
                                <div class="bg-white rounded-lg border border-gray-100 px-3 py-2 text-center">
                                    <span class="block text-xs text-gray-400">IMC</span>
                                    <span class="font-semibold text-gray-800">{{ $sv['imc'] }}</span>
                                    <span class="text-xs text-gray-400"> kg/m²</span>
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
                    <td colspan="5" class="px-4 py-6 text-center text-gray-500">Sin evaluaciones registradas.</td>
                </tr>
            </tbody>
            @endforelse
        </table>
    </div>

    <div class="mt-4 no-print">{{ $evaluaciones->links() }}</div>

    @if(isset($camillaUsos) && $camillaUsos->isNotEmpty())
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden no-print">
        <div class="px-4 py-3 border-b bg-gray-50">
            <h2 class="text-sm font-semibold text-gray-700">Usos de Camilla</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Camilla</th>
                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Área</th>
                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Inicio</th>
                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Fin</th>
                    <th class="px-4 py-2 text-right text-xs font-bold text-gray-500 uppercase">Horas</th>
                    <th class="px-4 py-2 text-right text-xs font-bold text-gray-500 uppercase">Costo (Bs.)</th>
                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Registrado por</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($camillaUsos as $uso)
                <tr>
                    <td class="px-4 py-2">{{ $uso->camilla->nombre }} <span class="text-gray-400">({{ $uso->camilla->codigo }})</span></td>
                    <td class="px-4 py-2">{{ $uso->camilla->area_label }}</td>
                    <td class="px-4 py-2">{{ $uso->fecha_inicio->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-2">{{ $uso->fecha_fin?->format('d/m/Y H:i') ?? '—' }}</td>
                    <td class="px-4 py-2 text-right">{{ $uso->calcularHoras() }}</td>
                    <td class="px-4 py-2 text-right font-medium">{{ number_format($uso->costo_calculado, 2) }}</td>
                    <td class="px-4 py-2 text-gray-500">{{ $uso->registradoPor->name ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

<!-- === SECCION DE IMPRESION EPSON MATRICIAL === -->
<div class="print-only epson-page">
<pre class="epson-pre">
================================================================================
                              C E M S A
                    HISTORIAL DE EVALUACIONES CLINICAS
================================================================================
@php
    if (isset($paciente->is_temporal) && $paciente->is_temporal) {
        $codigoPaciente = $paciente->emergency_code ?? $paciente->ci;
    } elseif (isset($paciente->consultas)) {
        $codigoPaciente = $paciente->consultas->first()?->caja?->id ?? ($paciente->registro_codigo ?? '—');
    } else {
        $codigoPaciente = $paciente->ci;
    }
@endphp
PACIENTE: {{ str_pad(strtoupper($paciente->nombre), 46, ' ') }}  CI: {{ $paciente->ci }}
CODIGO:   {{ $codigoPaciente }}
FECHA IMP: {{ now()->format('d/m/Y H:i') }}
================================================================================
@php
    $totalMedicamentos = 0;
    $totalInsumos = 0;
    $totalProcedimientos = 0;
@endphp
@forelse($evaluaciones as $index => $evaluacion)
@php
    $meds = $evaluacion->items->where('tipo', 'medicamento');
    $insumos = $evaluacion->items->where('tipo', 'insumo');
    $procs = $evaluacion->items->where('tipo', 'procedimiento');

    $totalMedicamentos += $meds->sum('cantidad');
    $totalInsumos += $insumos->sum('cantidad');
    $totalProcedimientos += $procs->sum('cantidad');
@endphp
EVALUACION #{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }} | {{ $evaluacion->created_at->format('d/m/Y H:i') }}
--------------------------------------------------------------------------------
AREA: {{ strtoupper(str_pad($evaluacion->area, 15, ' ')) }} | MEDICO: {{ strtoupper($evaluacion->user->name ?? 'N/A') }}
@if(!empty($evaluacion->signos_vitales))
@php
    $sv = $evaluacion->signos_vitales;
    $svLinea1 = trim(
        (!empty($sv['presion_arterial'])        ? '  PA: '.str_pad($sv['presion_arterial'].' mmHg', 18) : '') .
        (!empty($sv['frecuencia_cardiaca'])     ? '  FC: '.str_pad($sv['frecuencia_cardiaca'].' lpm', 12) : '') .
        (!empty($sv['frecuencia_respiratoria']) ? '  FR: '.$sv['frecuencia_respiratoria'].' rpm' : '')
    );
    $svLinea2 = trim(
        (!empty($sv['temperatura'])   ? '  TEMP: '.str_pad($sv['temperatura'].' C', 14) : '') .
        (!empty($sv['saturacion_o2']) ? '  SAT O2: '.str_pad($sv['saturacion_o2'].' %', 8) : '') .
        (!empty($sv['glucosa'])       ? '  GLUCOSA: '.$sv['glucosa'].' mg/dL' : '')
    );
    $svLinea3 = trim(
        (!empty($sv['peso'])   ? '  PESO: '.$sv['peso'].' kg' : '') .
        (!empty($sv['altura']) ? '   TALLA: '.$sv['altura'].' cm' : '') .
        (!empty($sv['imc'])    ? '   IMC: '.$sv['imc'].' kg/m2' : '')
    );
@endphp
{{ $svLinea1 ? "\nSIGNOS VITALES:\n".$svLinea1 : '' }}{{ $svLinea2 ? "\n".$svLinea2 : '' }}{{ $svLinea3 ? "\n".$svLinea3 : '' }}
@endif
@if($meds->count())
MEDICAMENTOS ADMINISTRADOS:
+----------------------------------------+------+
| NOMBRE                                 | CANT |
+----------------------------------------+------+
@foreach($meds as $item)
| {{ str_pad($item->nombre_snapshot, 38, ' ') }} | {{ str_pad($item->cantidad, 4, ' ') }} |
@endforeach
+----------------------------------------+------+
@endif
@if($insumos->count())
INSUMOS UTILIZADOS:
+----------------------------------------+------+
| NOMBRE                                 | CANT |
+----------------------------------------+------+
@foreach($insumos as $item)
| {{ str_pad($item->nombre_snapshot, 38, ' ') }} | {{ str_pad($item->cantidad, 4, ' ') }} |
@endforeach
+----------------------------------------+------+
@endif
@if($procs->count())
PROCEDIMIENTOS REALIZADOS:
+----------------------------------------+------+
| NOMBRE                                 | CANT |
+----------------------------------------+------+
@foreach($procs as $item)
| {{ str_pad($item->nombre_snapshot, 38, ' ') }} | {{ str_pad($item->cantidad, 4, ' ') }} |
@endforeach
+----------------------------------------+------+
@endif
@if($evaluacion->observaciones)
OBSERVACIONES MEDICAS:
    {{ wordwrap($evaluacion->observaciones, 76, "\n    ") }}
@endif
--------------------------------------------------------------------------------

@empty
                    *** SIN EVALUACIONES REGISTRADAS ***

@endforelse

@if(isset($camillaUsos) && $camillaUsos->isNotEmpty())
================================================================================
                         USOS DE CAMILLA
================================================================================
+------------------------------+-----------+-----------+------+-----------+
| CAMILLA                      | INICIO    | FIN       | HRS  | COSTO Bs. |
+------------------------------+-----------+-----------+------+-----------+
@foreach($camillaUsos as $uso)
| {{ str_pad($uso->camilla->nombre, 28, ' ') }} | {{ $uso->fecha_inicio->format('d/m H:i') }}  | {{ $uso->fecha_fin?->format('d/m H:i') ?? '  --   ' }}  | {{ str_pad($uso->calcularHoras(), 4, ' ', STR_PAD_LEFT) }} | {{ str_pad(number_format($uso->costo_calculado, 2), 9, ' ', STR_PAD_LEFT) }} |
@endforeach
+------------------------------+-----------+-----------+------+-----------+
@endif
================================================================================
                           FIN DEL HISTORIAL
================================================================================




</pre>
</div>

<script>
function printEpson() {
    // Abrir ventana de impresión optimizada para Epson
    var printWindow = window.open('', '_blank');
    var content = document.querySelector('.epson-page').innerHTML;

    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>HISTORIAL - {{ $paciente->ci }}</title>
            <style>
                @page { size: letter; margin: 10mm; }
                body {
                    font-family: 'Courier New', 'Courier', monospace;
                    font-size: 10pt;
                    line-height: 1.2;
                    color: #000;
                    background: #fff;
                    margin: 0;
                    padding: 10mm;
                    width: 172mm;
                }
                pre {
                    font-family: 'Courier New', 'Courier', monospace;
                    font-size: 10pt;
                    line-height: 1.2;
                    white-space: pre-wrap;
                    word-wrap: break-word;
                    margin: 0;
                }
            </style>
        </head>
        <body>
            <pre>${content}</pre>
            <script>
                window.onload = function() {
                    window.print();
                };
                window.onafterprint = function() {
                    window.close();
                };
            <\/script>
        </body>
        </html>
    `);
    printWindow.document.close();
}
</script>
@endsection
