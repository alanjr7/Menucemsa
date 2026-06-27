@extends('layouts.app')

@section('content')
@php
    // Los precios/costos del historial clínico solo los ven admin o administrador.
    // El personal médico ve el historial sin importes (pantalla e impresión).
    $puedeVerPrecios = in_array(auth()->user()->role, ['admin', 'administrador'], true);
@endphp
<style>
    /* === ESTILOS PARA PANTALLA === */
    @media screen {
        .print-only { display: none !important; }
    }

    /* === IMPRESIÓN: documento serio y minimalista (solo líneas, sin color) === */
    @media print {
        @page {
            size: letter;
            margin: 14mm;
        }

        /* A prueba de balas: colapsa el contenido de pantalla (evita páginas en blanco)
           y oculta el chrome del layout (header/sidebar), independiente del layout.
           Solo el documento de impresión queda visible. */
        .historial-screen { display: none !important; }

        body * { visibility: hidden; }

        .print-only {
            display: block !important;
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        .print-only,
        .print-only * { visibility: visible; }
    }

    /* === DOCUMENTO TIPOGRÁFICO (negrita / normal, líneas horizontales) === */
    .hx-doc {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 10.5pt;
        line-height: 1.45;
        color: #000;
        max-width: 180mm;
        margin: 0 auto;
        padding: 4mm;
    }

    .hx-title {
        font-size: 15pt;
        font-weight: 700;
        letter-spacing: 4px;
        text-align: center;
        margin: 0;
    }

    .hx-subtitle {
        font-size: 9.5pt;
        font-weight: 400;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        text-align: center;
        margin: 3px 0 0;
    }

    .hx-rule {
        border: 0;
        border-top: 1.5px solid #000;
        margin: 10px 0;
    }

    .hx-hairline {
        border: 0;
        border-top: 1px solid #c8c8c8;
        margin: 8px 0;
    }

    .hx-meta {
        width: 100%;
        border-collapse: collapse;
        font-size: 10pt;
        margin: 8px 0;
    }

    .hx-meta td {
        padding: 1px 0;
        vertical-align: top;
    }

    .hx-section {
        font-size: 11pt;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        text-align: center;
        margin: 18px 0 4px;
    }

    .hx-block {
        margin: 10px 0;
        page-break-inside: avoid;
    }

    .hx-block-head { font-weight: 700; font-size: 10.5pt; }
    .hx-block-sub { font-size: 9.5pt; margin-top: 1px; }

    .hx-group {
        font-weight: 700;
        font-size: 8.5pt;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 8px 0 2px;
    }

    .hx-items {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed; /* rejilla fija: todas las sub-tablas alinean Cant. / Importe */
        font-size: 9.5pt;
        margin-bottom: 4px;
    }

    .hx-items th {
        text-align: left;
        font-weight: 700;
        font-size: 8pt;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #000;
        padding: 2px 0;
    }

    .hx-items td {
        padding: 2px 0;
        border-bottom: 1px solid #dcdcdc;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    /* La 1.ª columna (Detalle) absorbe el resto; las numéricas tienen ancho fijo
       para que Cant. e Importe queden alineadas entre todas las tablas. */
    .hx-items .num {
        text-align: right;
        white-space: nowrap;
        padding-left: 12px;
        width: 80px;
    }

    .hx-items .num-importe { width: 110px; }

    .hx-note { font-size: 9.5pt; margin: 3px 0; }

    .hx-total {
        text-align: right;
        font-weight: 700;
        font-size: 10pt;
        margin: 5px 0 2px;
    }

    .hx-empty {
        text-align: center;
        font-style: italic;
        margin: 14px 0;
    }

    .hx-foot {
        text-align: center;
        font-size: 9pt;
        font-weight: 700;
        letter-spacing: 3px;
        margin-top: 18px;
    }
</style>

<div class="historial-screen p-6 bg-gray-50 min-h-screen">
    <div class="flex items-center justify-between mb-6 no-print">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Historial de Evaluaciones</h1>
            <p class="text-sm text-gray-500">{{ $paciente->nombre }} &bull; CI: {{ $paciente->ci }}</p>
        </div>
        <div class="flex gap-2 no-print">
            <button onclick="window.print()"
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
                                            @if($puedeVerPrecios)
                                            <span class="text-gray-400">Bs. {{ number_format($item->precio_snapshot, 2) }}</span>
                                            @endif
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
                    @if($puedeVerPrecios)
                    <th class="px-4 py-2 text-right text-xs font-bold text-gray-500 uppercase">Costo (Bs.)</th>
                    @endif
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
                    @if($puedeVerPrecios)
                    <td class="px-4 py-2 text-right font-medium">{{ number_format($uso->costo_calculado, 2) }}</td>
                    @endif
                    <td class="px-4 py-2 text-gray-500">{{ $uso->registradoPor->name ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if(isset($cirugias) && $cirugias->isNotEmpty())
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden no-print">
        <div class="px-4 py-3 border-b bg-gray-50">
            <h2 class="text-sm font-semibold text-gray-700">Cirugías</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Fecha</th>
                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Tipo</th>
                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Cirujano</th>
                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Quirófano</th>
                    <th class="px-4 py-2 text-left text-xs font-bold text-gray-500 uppercase">Estado</th>
                    @if($puedeVerPrecios)
                    <th class="px-4 py-2 text-right text-xs font-bold text-gray-500 uppercase">Costo (Bs.)</th>
                    @endif
                    <th class="px-4 py-2 text-right text-xs font-bold text-gray-500 uppercase">Detalle</th>
                </tr>
            </thead>
            @foreach($cirugias as $cir)
            @php
                $cirProc = $cir->cargos->where('tipo_item', 'procedimiento');
                $cirMeds = $cir->cargos->where('tipo_item', 'medicamento');
                $cirInsumos = $cir->cargos->where('tipo_item', 'material');
                $cirEquipos = $cir->cargos->where('tipo_item', 'equipo_medico');
            @endphp
            <tbody x-data="{ open: false }" class="divide-y divide-gray-100">
                <tr>
                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($cir->fecha)->format('d/m/Y') }} {{ \Carbon\Carbon::parse($cir->hora_inicio_estimada)->format('H:i') }}</td>
                    <td class="px-4 py-2 capitalize">{{ $cir->tipo_final ?? $cir->tipo_cirugia }}</td>
                    <td class="px-4 py-2">{{ $cir->cirujano?->nombre ?: $cir->cirujano?->user?->name ?? '—' }}</td>
                    <td class="px-4 py-2 text-gray-500">{{ $cir->quirofano?->nombre ?? '—' }}</td>
                    <td class="px-4 py-2">
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium
                            @if($cir->estado === 'finalizada') bg-green-100 text-green-700
                            @elseif($cir->estado === 'en_curso') bg-yellow-100 text-yellow-700
                            @elseif($cir->estado === 'cancelada') bg-gray-100 text-gray-500
                            @else bg-blue-50 text-blue-600 @endif">
                            {{ ucfirst(str_replace('_', ' ', $cir->estado)) }}
                        </span>
                    </td>
                    @if($puedeVerPrecios)
                    <td class="px-4 py-2 text-right font-medium">{{ number_format($cir->costo_final ?? $cir->costo_base, 2) }}</td>
                    @endif
                    <td class="px-4 py-2 text-right">
                        <button @click="open = !open" class="px-3 py-1 border rounded text-xs text-gray-700">
                            <span x-show="!open">Ver</span><span x-show="open" x-cloak>Ocultar</span>
                        </button>
                    </td>
                </tr>
                <tr x-show="open" x-cloak class="bg-gray-50">
                    <td colspan="{{ $puedeVerPrecios ? 7 : 6 }}" class="px-6 py-4">
                        @if($cir->descripcion_cirugia)
                            <p class="text-xs font-semibold text-gray-500 mb-1">Descripción de la cirugía</p>
                            <p class="text-sm text-gray-700 mb-3">{{ $cir->descripcion_cirugia }}</p>
                        @endif
                        @if($cir->observaciones)
                            <p class="text-xs font-semibold text-gray-500 mb-1">Observaciones</p>
                            <p class="text-sm text-gray-700 mb-3">{{ $cir->observaciones }}</p>
                        @endif
                        @if($cirProc->count())
                            <p class="text-xs font-semibold text-gray-500 mb-1">Procedimiento quirúrgico</p>
                            <ul class="mb-3 space-y-1">
                                @foreach($cirProc as $item)
                                    <li class="text-sm flex justify-between">
                                        <span>{{ $item->descripcion }}</span>
                                        @if($puedeVerPrecios)<span class="text-gray-400">Bs. {{ number_format($item->subtotal, 2) }}</span>@endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        @if($cirMeds->count())
                            <p class="text-xs font-semibold text-gray-500 mb-1">Medicamentos administrados</p>
                            <ul class="mb-3 space-y-1">
                                @foreach($cirMeds as $item)
                                    <li class="text-sm flex justify-between">
                                        <span>{{ $item->descripcion }} &times; {{ (int) $item->cantidad }}</span>
                                        @if($puedeVerPrecios)<span class="text-gray-400">Bs. {{ number_format($item->subtotal, 2) }}</span>@endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        @if($cirInsumos->count())
                            <p class="text-xs font-semibold text-gray-500 mb-1">Insumos utilizados</p>
                            <ul class="mb-3 space-y-1">
                                @foreach($cirInsumos as $item)
                                    <li class="text-sm flex justify-between">
                                        <span>{{ $item->descripcion }} &times; {{ (int) $item->cantidad }}</span>
                                        @if($puedeVerPrecios)<span class="text-gray-400">Bs. {{ number_format($item->subtotal, 2) }}</span>@endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        @if($cirEquipos->count())
                            <p class="text-xs font-semibold text-gray-500 mb-1">Equipos médicos</p>
                            <ul class="mb-3 space-y-1">
                                @foreach($cirEquipos as $item)
                                    <li class="text-sm flex justify-between">
                                        <span>{{ $item->descripcion }} &times; {{ (int) $item->cantidad }}</span>
                                        @if($puedeVerPrecios)<span class="text-gray-400">Bs. {{ number_format($item->subtotal, 2) }}</span>@endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                        @if(!$cir->descripcion_cirugia && !$cir->observaciones && $cir->cargos->isEmpty())
                            <p class="text-sm text-gray-400 italic">Sin detalles registrados para esta cirugía.</p>
                        @endif
                    </td>
                </tr>
            </tbody>
            @endforeach
        </table>
    </div>
    @endif
</div>

<!-- === DOCUMENTO DE IMPRESIÓN (serio / minimalista) === -->
<div class="print-only hx-doc">
    @php
        if (isset($paciente->is_temporal) && $paciente->is_temporal) {
            $codigoPaciente = $paciente->emergency_code ?? $paciente->ci;
        } elseif (isset($paciente->consultas)) {
            $codigoPaciente = $paciente->consultas->first()?->caja?->id ?? ($paciente->registro_codigo ?? '—');
        } else {
            $codigoPaciente = $paciente->ci;
        }
    @endphp

    <div class="hx-title">CEMSA</div>
    <div class="hx-subtitle">Historial de Evaluaciones Clínicas</div>
    <hr class="hx-rule">
    <table class="hx-meta">
        <tr>
            <td>Paciente: <strong>{{ strtoupper($paciente->nombre) }}</strong></td>
            <td style="text-align:right">C.I.: <strong>{{ $paciente->ci }}</strong></td>
        </tr>
        <tr>
            <td>Código: <strong>{{ $codigoPaciente }}</strong></td>
            <td style="text-align:right">Impreso: <strong>{{ now()->format('d/m/Y H:i') }}</strong></td>
        </tr>
    </table>
    <hr class="hx-rule">

    <div class="hx-section">Evaluaciones</div>
    @forelse($evaluaciones as $index => $evaluacion)
        @php
            $gruposEv = [
                'Medicamentos administrados' => $evaluacion->items->where('tipo', 'medicamento'),
                'Insumos utilizados'         => $evaluacion->items->where('tipo', 'insumo'),
                'Procedimientos realizados'  => $evaluacion->items->where('tipo', 'procedimiento'),
            ];
            $svParts = [];
            if (!empty($evaluacion->signos_vitales)) {
                $sv = $evaluacion->signos_vitales;
                if (!empty($sv['presion_arterial']))        $svParts[] = 'PA '.$sv['presion_arterial'].' mmHg';
                if (!empty($sv['frecuencia_cardiaca']))     $svParts[] = 'FC '.$sv['frecuencia_cardiaca'].' lpm';
                if (!empty($sv['frecuencia_respiratoria'])) $svParts[] = 'FR '.$sv['frecuencia_respiratoria'].' rpm';
                if (!empty($sv['temperatura']))             $svParts[] = 'T° '.$sv['temperatura'].' °C';
                if (!empty($sv['saturacion_o2']))           $svParts[] = 'SatO₂ '.$sv['saturacion_o2'].' %';
                if (!empty($sv['glucosa']))                 $svParts[] = 'Glucosa '.$sv['glucosa'].' mg/dL';
                if (!empty($sv['peso']))                    $svParts[] = 'Peso '.$sv['peso'].' kg';
                if (!empty($sv['altura']))                  $svParts[] = 'Talla '.$sv['altura'].' cm';
                if (!empty($sv['imc']))                     $svParts[] = 'IMC '.$sv['imc'];
            }
        @endphp
        <div class="hx-block">
            <div class="hx-block-head">Evaluación #{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }} · {{ $evaluacion->created_at->format('d/m/Y H:i') }}</div>
            <div class="hx-block-sub">Área: <strong>{{ ucfirst($evaluacion->area) }}</strong> · Médico: <strong>{{ $evaluacion->user->name ?? 'N/A' }}</strong></div>
            @if(!empty($svParts))
                <div class="hx-note"><strong>Signos vitales:</strong> {{ implode(' · ', $svParts) }}</div>
            @endif
            @foreach($gruposEv as $titulo => $grupo)
                @if($grupo->count())
                    <div class="hx-group">{{ $titulo }}</div>
                    <table class="hx-items">
                        <thead><tr><th>Detalle</th><th class="num">Cant.</th></tr></thead>
                        <tbody>
                            @foreach($grupo as $item)
                                <tr><td>{{ $item->nombre_snapshot }}</td><td class="num">{{ (int) $item->cantidad }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            @endforeach
            @if($evaluacion->observaciones)
                <div class="hx-note"><strong>Observaciones:</strong> {{ $evaluacion->observaciones }}</div>
            @endif
        </div>
        @if(!$loop->last)<hr class="hx-hairline">@endif
    @empty
        <div class="hx-empty">Sin evaluaciones registradas.</div>
    @endforelse

    @if(isset($camillaUsos) && $camillaUsos->isNotEmpty())
        <div class="hx-section">Usos de Camilla</div>
        <table class="hx-items">
            <thead>
                <tr>
                    <th>Camilla</th>
                    <th class="num">Inicio</th>
                    <th class="num">Fin</th>
                    <th class="num">Hrs</th>
                    @if($puedeVerPrecios)
                    <th class="num">Costo Bs.</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($camillaUsos as $uso)
                    <tr>
                        <td>{{ $uso->camilla->nombre }}</td>
                        <td class="num">{{ $uso->fecha_inicio->format('d/m H:i') }}</td>
                        <td class="num">{{ $uso->fecha_fin?->format('d/m H:i') ?? '—' }}</td>
                        <td class="num">{{ $uso->calcularHoras() }}</td>
                        @if($puedeVerPrecios)
                        <td class="num">{{ number_format($uso->costo_calculado, 2) }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if(isset($cirugias) && $cirugias->isNotEmpty())
        <div class="hx-section">Cirugías</div>
        @foreach($cirugias as $cir)
            @php
                $gruposCir = [
                    'Procedimiento quirúrgico'   => $cir->cargos->where('tipo_item', 'procedimiento'),
                    'Medicamentos administrados' => $cir->cargos->where('tipo_item', 'medicamento'),
                    'Insumos utilizados'         => $cir->cargos->where('tipo_item', 'material'),
                    'Equipos médicos'            => $cir->cargos->where('tipo_item', 'equipo_medico'),
                ];
            @endphp
            <div class="hx-block">
                <div class="hx-block-head">Cirugía {{ ucfirst($cir->tipo_final ?? $cir->tipo_cirugia) }} · {{ \Carbon\Carbon::parse($cir->fecha)->format('d/m/Y') }} · {{ ucfirst(str_replace('_', ' ', $cir->estado)) }}</div>
                <div class="hx-block-sub">Cirujano: <strong>{{ $cir->cirujano?->nombre ?: ($cir->cirujano?->user?->name ?? 'N/A') }}</strong> · Quirófano: <strong>{{ $cir->quirofano?->nombre ?? '—' }}</strong></div>
                @if($cir->descripcion_cirugia)
                    <div class="hx-note"><strong>Descripción:</strong> {{ $cir->descripcion_cirugia }}</div>
                @endif
                @if($cir->observaciones)
                    <div class="hx-note"><strong>Observaciones:</strong> {{ $cir->observaciones }}</div>
                @endif
                @foreach($gruposCir as $titulo => $grupo)
                    @if($grupo->count())
                        <div class="hx-group">{{ $titulo }}</div>
                        <table class="hx-items">
                            <thead><tr><th>Detalle</th><th class="num">Cant.</th>@if($puedeVerPrecios)<th class="num num-importe">Importe Bs.</th>@endif</tr></thead>
                            <tbody>
                                @foreach($grupo as $item)
                                    <tr>
                                        <td>{{ $item->descripcion }}</td>
                                        <td class="num">{{ (int) $item->cantidad }}</td>
                                        @if($puedeVerPrecios)<td class="num num-importe">{{ number_format($item->subtotal, 2) }}</td>@endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                @endforeach
                @if($puedeVerPrecios)
                <div class="hx-total">Costo total cirugía: Bs. {{ number_format($cir->costo_final ?? $cir->costo_base, 2) }}</div>
                @endif
            </div>
            @if(!$loop->last)<hr class="hx-hairline">@endif
        @endforeach
    @endif

    <hr class="hx-rule">
    <div class="hx-foot">FIN DEL HISTORIAL</div>
</div>

@endsection
