<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial Clínico - {{ $paciente->nombre }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body class="bg-white text-gray-900">

    <div class="max-w-4xl mx-auto p-8">

        <div class="no-print fixed top-4 right-4 flex gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium shadow-lg">
                Imprimir / Guardar PDF
            </button>
            <button onclick="window.close()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-medium shadow-lg">
                Cerrar
            </button>
        </div>

        <div class="border-b-2 border-blue-600 pb-4 mb-6">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">HISTORIAL CLÍNICO</h1>
                        <p class="text-sm text-gray-600">Documento Oficial de Historia Médica</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600">Fecha de Impresión:</p>
                    <p class="text-sm font-medium">{{ $fechaImpresion->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
            <h2 class="text-lg font-bold text-gray-800 mb-3 border-b border-gray-300 pb-2">DATOS DEL PACIENTE</h2>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-600 font-medium">Nombre Completo:</span>
                    <span class="text-gray-900 ml-1">{{ $paciente->nombre }}</span>
                </div>
                <div>
                    <span class="text-gray-600 font-medium">Cédula de Identidad:</span>
                    <span class="text-gray-900 ml-1">{{ $paciente->ci }}</span>
                </div>
                <div>
                    <span class="text-gray-600 font-medium">Sexo:</span>
                    <span class="text-gray-900 ml-1">{{ $paciente->sexo }}</span>
                </div>
                <div>
                    <span class="text-gray-600 font-medium">Teléfono:</span>
                    <span class="text-gray-900 ml-1">{{ $paciente->telefono ?? 'No registrado' }}</span>
                </div>
                <div>
                    <span class="text-gray-600 font-medium">Seguro:</span>
                    <span class="text-gray-900 ml-1">{{ $paciente->seguro->nombre_empresa ?? 'Particular' }}</span>
                </div>
                <div>
                    <span class="text-gray-600 font-medium">Triage:</span>
                    <span class="text-gray-900 ml-1">{{ $paciente->triage ? 'Nivel ' . $paciente->triage->nivel : 'No asignado' }}</span>
                </div>
                <div class="col-span-2">
                    <span class="text-gray-600 font-medium">Dirección:</span>
                    <span class="text-gray-900 ml-1">{{ $paciente->direccion ?? 'No registrada' }}</span>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <h3 class="text-base font-bold text-gray-800 mb-2 bg-blue-50 p-2 rounded">RESUMEN DE ATENCIONES</h3>
            <div class="grid grid-cols-4 gap-4 text-center text-sm">
                <div class="bg-gray-50 p-3 rounded border">
                    <p class="text-gray-600">Consultas</p>
                    <p class="text-xl font-bold text-blue-600">{{ $paciente->consultas->count() }}</p>
                </div>
                <div class="bg-gray-50 p-3 rounded border">
                    <p class="text-gray-600">Emergencias</p>
                    <p class="text-xl font-bold text-red-600">{{ $paciente->emergencies->count() }}</p>
                </div>
                <div class="bg-gray-50 p-3 rounded border">
                    <p class="text-gray-600">Hospitalizaciones</p>
                    <p class="text-xl font-bold text-yellow-600">{{ $paciente->hospitalizaciones->count() }}</p>
                </div>
                <div class="bg-gray-50 p-3 rounded border">
                    <p class="text-gray-600">Cirugías</p>
                    <p class="text-xl font-bold text-purple-600">{{ $cirugiasHistorial->count() }}</p>
                </div>
            </div>
        </div>

        @if($paciente->consultas->count() > 0)
        <div class="page-break"></div>
        <div class="mb-6">
            <h3 class="text-base font-bold text-gray-800 mb-3 bg-blue-50 p-2 rounded flex items-center">
                <span class="w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs mr-2">1</span>
                CONSULTAS EXTERNAS
            </h3>
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-3 py-2 text-left font-medium">Fecha</th>
                        <th class="border border-gray-300 px-3 py-2 text-left font-medium">Médico</th>
                        <th class="border border-gray-300 px-3 py-2 text-left font-medium">Especialidad</th>
                        <th class="border border-gray-300 px-3 py-2 text-left font-medium">Motivo</th>
                        <th class="border border-gray-300 px-3 py-2 text-left font-medium">Diagnóstico</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($paciente->consultas as $consulta)
                    <tr>
                        <td class="border border-gray-300 px-3 py-2">{{ $consulta->fecha?->format('d/m/Y') ?? '-' }}</td>
                        <td class="border border-gray-300 px-3 py-2">{{ $consulta->medico->usuario->name ?? '-' }}</td>
                        <td class="border border-gray-300 px-3 py-2">{{ $consulta->especialidad->nombre ?? '-' }}</td>
                        <td class="border border-gray-300 px-3 py-2">{{ $consulta->motivo ?? 'No registrado' }}</td>
                        <td class="border border-gray-300 px-3 py-2">{{ $consulta->diagnostico ?? 'Sin diagnóstico' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @if($paciente->emergencies->count() > 0)
        <div class="page-break"></div>
        <div class="mb-6">
            <h3 class="text-base font-bold text-gray-800 mb-3 bg-red-50 p-2 rounded flex items-center">
                <span class="w-6 h-6 bg-red-600 text-white rounded-full flex items-center justify-center text-xs mr-2">2</span>
                EMERGENCIAS
            </h3>
            @foreach($paciente->emergencies as $emergencia)
            <div class="border border-gray-300 rounded mb-3 p-3">
                <div class="flex justify-between items-start mb-2 pb-2 border-b border-gray-200">
                    <div>
                        <span class="font-medium">Código: {{ $emergencia->code }}</span>
                        <span class="ml-2 px-2 py-0.5 rounded text-xs {{ $emergencia->status !== 'alta' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                            {{ ucfirst(str_replace('_', ' ', $emergencia->status)) }}
                        </span>
                    </div>
                    <span class="text-sm text-gray-600">{{ $emergencia->admission_date?->format('d/m/Y H:i') ?? '-' }}</span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div><span class="text-gray-600">Motivo:</span> <span>{{ $emergencia->symptoms ?? $emergencia->initial_assessment ?? 'No registrado' }}</span></div>
                    <div><span class="text-gray-600">Atendido por:</span> <span>{{ $emergencia->user->name ?? '-' }}</span></div>
                    @if($emergencia->discharge_date)
                    <div><span class="text-gray-600">Fecha de Alta:</span> <span>{{ $emergencia->discharge_date?->format('d/m/Y H:i') }}</span></div>
                    @endif
                    @if($emergencia->ubicacion_actual)
                    <div><span class="text-gray-600">Ubicación Final:</span> <span>{{ ucfirst($emergencia->ubicacion_actual) }}</span></div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif

        @if($paciente->hospitalizaciones->count() > 0)
        <div class="page-break"></div>
        <div class="mb-6">
            <h3 class="text-base font-bold text-gray-800 mb-3 bg-yellow-50 p-2 rounded flex items-center">
                <span class="w-6 h-6 bg-yellow-600 text-white rounded-full flex items-center justify-center text-xs mr-2">3</span>
                HOSPITALIZACIONES / INTERNACIONES
            </h3>
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 px-3 py-2 text-left font-medium">Fecha Ingreso</th>
                        <th class="border border-gray-300 px-3 py-2 text-left font-medium">Fecha Alta</th>
                        <th class="border border-gray-300 px-3 py-2 text-left font-medium">Servicio</th>
                        <th class="border border-gray-300 px-3 py-2 text-left font-medium">Médico</th>
                        <th class="border border-gray-300 px-3 py-2 text-left font-medium">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($paciente->hospitalizaciones as $hospitalizacion)
                    <tr>
                        <td class="border border-gray-300 px-3 py-2">{{ $hospitalizacion->fecha_ingreso?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td class="border border-gray-300 px-3 py-2">{{ $hospitalizacion->fecha_alta?->format('d/m/Y H:i') ?? 'Activo' }}</td>
                        <td class="border border-gray-300 px-3 py-2">{{ $hospitalizacion->servicio->nombre ?? '-' }}</td>
                        <td class="border border-gray-300 px-3 py-2">{{ $hospitalizacion->medico->usuario->name ?? '-' }}</td>
                        <td class="border border-gray-300 px-3 py-2">
                            <span class="px-2 py-0.5 rounded text-xs {{ $hospitalizacion->estado === 'Activo' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                {{ $hospitalizacion->estado }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @if($utiHistorial->count() > 0)
        <div class="page-break"></div>
        <div class="mb-6">
            <h3 class="text-base font-bold text-gray-800 mb-3 bg-cyan-50 p-2 rounded flex items-center">
                <span class="w-6 h-6 bg-cyan-600 text-white rounded-full flex items-center justify-center text-xs mr-2">4</span>
                UNIDAD DE TERAPIA INTENSIVA (UTI)
            </h3>
            @foreach($utiHistorial as $uti)
            <div class="border border-gray-300 rounded mb-3 p-3">
                <div class="flex justify-between items-start mb-2 pb-2 border-b border-gray-200">
                    <div>
                        <span class="font-medium">Nro. Ingreso: {{ $uti->nro_ingreso }}</span>
                        <span class="ml-2 px-2 py-0.5 rounded text-xs {{ $uti->estado === 'activo' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($uti->estado) }}
                        </span>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-2 text-sm">
                    <div><span class="text-gray-600">Fecha Ingreso:</span> <span>{{ $uti->fecha_ingreso?->format('d/m/Y H:i') ?? '-' }}</span></div>
                    <div><span class="text-gray-600">Fecha Alta:</span> <span>{{ $uti->fecha_alta?->format('d/m/Y H:i') ?? 'Sin alta' }}</span></div>
                    <div><span class="text-gray-600">Cama:</span> <span>{{ $uti->bed->bed_number ?? '-' }}</span></div>
                    <div class="col-span-3"><span class="text-gray-600">Diagnóstico Principal:</span> <span>{{ $uti->diagnostico_principal ?? 'No registrado' }}</span></div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        @if($cirugiasHistorial->count() > 0)
        <div class="page-break"></div>
        <div class="mb-6">
            <h3 class="text-base font-bold text-gray-800 mb-3 bg-purple-50 p-2 rounded flex items-center">
                <span class="w-6 h-6 bg-purple-600 text-white rounded-full flex items-center justify-center text-xs mr-2">5</span>
                CIRUGÍAS
            </h3>
            @foreach($cirugiasHistorial as $cirugia)
            <div class="border border-gray-300 rounded mb-3 p-3">
                <div class="flex justify-between items-start mb-2 pb-2 border-b border-gray-200">
                    <div>
                        <span class="font-medium">Tipo: {{ $cirugia->tipo_cirugia ?? 'No especificado' }}</span>
                        <span class="ml-2 px-2 py-0.5 rounded text-xs {{ $cirugia->estado === 'completada' ? 'bg-green-100 text-green-800' : ($cirugia->estado === 'en_curso' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst(str_replace('_', ' ', $cirugia->estado ?? 'pendiente')) }}
                        </span>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-2 text-sm">
                    <div><span class="text-gray-600">Fecha:</span> <span>{{ $cirugia->fecha_cirugia?->format('d/m/Y H:i') ?? '-' }}</span></div>
                    <div><span class="text-gray-600">Cirujano:</span> <span>{{ $cirugia->medico->usuario->name ?? '-' }}</span></div>
                    <div><span class="text-gray-600">Quirófano:</span> <span>{{ $cirugia->quirofano->nombre ?? '-' }}</span></div>
                    <div class="col-span-3"><span class="text-gray-600">Procedimiento:</span> <span>{{ $cirugia->procedimiento ?? 'No registrado' }}</span></div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <div class="mt-8 pt-4 border-t-2 border-gray-300">
            <div class="flex justify-between items-end">
                <div class="text-xs text-gray-500">
                    <p>Este documento es un resumen del historial clínico del paciente.</p>
                    <p>Generado el {{ $fechaImpresion->format('d/m/Y') }} a las {{ $fechaImpresion->format('H:i') }}</p>
                </div>
                <div class="text-center">
                    <div class="w-48 border-b border-gray-400 mb-1"></div>
                    <p class="text-xs text-gray-600">Firma y Sello Médico</p>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
