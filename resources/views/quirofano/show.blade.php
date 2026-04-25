@extends('layouts.app')

@section('content')
<div class="w-full p-4 bg-gray-50/50 min-h-screen">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Detalles de Cita Quirúrgica</h1>
            <p class="text-sm text-gray-500">Información completa de la intervención quirúrgica</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('quirofano.index') }}" class="flex items-center px-3 py-2 border border-gray-200 rounded-lg text-gray-600 bg-white hover:bg-gray-50 font-medium transition-colors text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span class="hidden sm:inline">Volver</span>
                <span class="sm:hidden">←</span>
            </a>
            @if($cita->estado === 'programada')
                <a href="{{ route('quirofano.edit', $cita) }}" class="flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span class="hidden sm:inline">Editar</span>
                    <span class="sm:hidden">✏️</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Estado de la Cita -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 lg:p-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg lg:text-xl font-bold text-gray-800">{{ $cita->paciente->nombre }}</h2>
                <p class="text-sm text-gray-500">CI: {{ $cita->paciente->ci }} | {{ $cita->fecha->format('d/m/Y') }}</p>
            </div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <span class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium {{ $cita->estado === 'programada' ? 'bg-blue-100 text-blue-800' : ($cita->estado === 'en_curso' ? 'bg-amber-100 text-amber-800' : ($cita->estado === 'finalizada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800')) }}">
                    <span class="w-2 h-2 {{ $cita->estado === 'en_curso' ? 'animate-pulse' : '' }} bg-{{ $cita->estado === 'programada' ? 'blue' : ($cita->estado === 'en_curso' ? 'amber' : ($cita->estado === 'finalizada' ? 'green' : 'red')) }}-500 rounded-full mr-2"></span>
                    {{ ucfirst($cita->estado) }}
                </span>
                
                @if($cita->estado === 'programada')
                    <button onclick="iniciarCirugia({{ $cita->id }})" class="flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition-colors text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Iniciar
                    </button>
                @endif

                @if($cita->estado === 'en_curso')
                    <button onclick="finalizarCirugia({{ $cita->id }})" class="flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition-colors text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/>
                        </svg>
                        Finalizar
                    </button>
                @endif

                @if($cita->estado !== 'finalizada')
                    <button onclick="cancelarCita({{ $cita->id }})" class="flex items-center px-4 py-2 border border-red-300 text-red-700 rounded-lg hover:bg-red-50 font-medium transition-colors text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Cancelar
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="space-y-6">
        
        <!-- Vista Móvil: Tarjetas Apiladas -->
        <div class="lg:hidden space-y-4">
            
            <!-- Información Quirúrgica -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <h3 class="text-base font-bold text-gray-800 mb-4">Información Quirúrgica</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">Tipo</span>
                        <span class="font-semibold capitalize text-sm">{{ $cita->tipo_cirugia }}</span>
                    </div>
                    @if($cita->tipo_final && $cita->tipo_final !== $cita->tipo_cirugia)
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">Tipo Final</span>
                        <span class="font-semibold text-sm text-amber-600">{{ $cita->tipo_final }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">Quirófano</span>
                        <span class="font-semibold text-sm">Q{{ $cita->quirofano->id }} ({{ $cita->quirofano->tipo }})</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">Duración Estimada</span>
                        <span class="font-semibold text-sm">{{ $cita->duracion_estimada }} min</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">Horario Estimado</span>
                        <span class="font-semibold text-sm">{{ $cita->hora_inicio_estimada->format('H:i') }} - {{ $cita->hora_fin_estimada->format('H:i') }}</span>
                    </div>
                    @if($cita->hora_inicio_real)
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">Horario Real</span>
                        <span class="font-semibold text-sm">{{ $cita->hora_inicio_real->format('H:i') }} - {{ $cita->hora_fin_real?->format('H:i') ?? 'En curso' }}</span>
                    </div>
                    @endif
                    @if($cita->duracion_real)
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">Duración Real</span>
                        <span class="font-semibold text-sm">{{ $cita->duracion_real }} min</span>
                    </div>
                    @endif
                </div>
                
                @if($cita->descripcion_cirugia)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Descripción</h4>
                        <p class="text-gray-600 text-sm">{{ $cita->descripcion_cirugia }}</p>
                    </div>
                @endif
            </div>

            <!-- Equipo Quirúrgico -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <h3 class="text-base font-bold text-gray-800 mb-4">Equipo Quirúrgico</h3>
                <div class="space-y-4">
                    <div class="bg-gray-50 rounded-lg p-3">
                        <div class="text-xs text-gray-500 mb-1">Cirujano Responsable</div>
                        <div class="font-semibold text-sm">{{ optional($cita->cirujano->user)->name ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-600">CI: {{ $cita->cirujano->ci }}</div>
                    </div>
                    @if($cita->instrumentista)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="text-xs text-gray-500 mb-1">Instrumentista</div>
                            <div class="font-semibold text-sm">{{ optional($cita->instrumentista->user)->name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-600">CI: {{ $cita->instrumentista->ci }}</div>
                        </div>
                    @else
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="text-xs text-gray-500 mb-1">Instrumentista</div>
                            <div class="text-sm text-gray-400">No asignado</div>
                        </div>
                    @endif
                    @if($cita->anestesiologo)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="text-xs text-gray-500 mb-1">Anestesiólogo</div>
                            <div class="font-semibold text-sm">{{ optional($cita->anestesiologo->user)->name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-600">CI: {{ $cita->anestesiologo->ci }}</div>
                        </div>
                    @else
                        <div class="bg-gray-50 rounded-lg p-3">
                            <div class="text-xs text-gray-500 mb-1">Anestesiólogo</div>
                            <div class="text-sm text-gray-400">No asignado</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Información del Paciente -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <h3 class="text-base font-bold text-gray-800 mb-4">Información del Paciente</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">Nombre</span>
                        <span class="font-semibold text-sm">{{ $cita->paciente->nombre }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">CI</span>
                        <span class="font-semibold text-sm">{{ $cita->paciente->ci }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">Teléfono</span>
                        <span class="text-sm">{{ $cita->paciente->telefono ?? 'No registrado' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">Seguro</span>
                        <span class="text-sm">{{ $cita->paciente->seguro->nombre ?? 'Particular' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-500">Código Registro</span>
                        <span class="font-semibold text-sm">{{ $cita->paciente->codigo_registro }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-500">Registrado por</span>
                        <span class="text-sm">{{ $cita->paciente->registro->usuario->name ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Costos -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <h3 class="text-base font-bold text-gray-800 mb-4">Costos</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Costo Base</span>
                        <span class="font-semibold text-sm">Bs. {{ number_format($cita->costo_base, 2) }}</span>
                    </div>

                    @php
                        $cuentaCobro = \App\Models\CuentaCobro::where('referencia_type', \App\Models\CitaQuirurgica::class)
                            ->where('referencia_id', $cita->id)
                            ->first();
                        $medicamentosCosto = $cuentaCobro ? $cuentaCobro->detalles()->where('tipo_item', 'medicamento')->get() : collect();
                    @endphp

                    @if($medicamentosCosto->count() > 0)
                        <div class="pt-2 border-t border-gray-100">
                            <p class="text-xs font-medium text-gray-500 mb-2">Medicamentos:</p>
                            @foreach($medicamentosCosto as $med)
                                <div class="flex justify-between items-center py-1 text-sm">
                                    <span class="text-gray-600">{{ $med->descripcion }}</span>
                                    <span class="text-gray-700">Bs. {{ number_format($med->subtotal, 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($cita->costo_final)
                        <div class="pt-3 border-t border-gray-200">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Costo Final</span>
                                <span class="font-bold text-lg text-green-600">Bs. {{ number_format($cita->costo_final, 2) }}</span>
                            </div>
                            @if($cita->costo_final > $cita->costo_base)
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-sm text-gray-600">Diferencia</span>
                                    <span class="font-semibold text-amber-600 text-sm">+Bs. {{ number_format($cita->costo_final - $cita->costo_base, 2) }}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            @if(in_array($cita->estado, ['programada', 'en_curso']) && (Auth::user()->isAdmin() || Auth::user()->isCirujano() || Auth::user()->hasRole('administrador')))
            <!-- Medicamentos e Insumos -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                    Medicamentos e Insumos
                </h3>

                <!-- Lista de medicamentos agregados -->
                <div id="lista-medicamentos-usados" class="space-y-2 mb-4">
                    <div class="text-center text-gray-500 text-sm py-4">
                        Cargando medicamentos...
                    </div>
                </div>

                <!-- Formulario para agregar medicamento -->
                <div class="border-t border-gray-200 pt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Agregar Medicamento/Insumo</h4>
                    <div class="space-y-3">
                        <!-- Buscador de medicamentos -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" id="buscar-medicamento"
                                   class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                   placeholder="Buscar medicamento..." autocomplete="off">
                            <!-- Dropdown de resultados -->
                            <div id="resultados-medicamento" class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-auto"></div>
                            <!-- Medicamento seleccionado (oculto) -->
                            <input type="hidden" id="medicamento-id">
                            <input type="hidden" id="medicamento-precio">
                            <input type="hidden" id="medicamento-stock">
                        </div>
                        <!-- Info del medicamento seleccionado -->
                        <div id="info-medicamento" class="hidden bg-green-50 rounded-lg p-2 border border-green-100">
                            <div class="flex items-center justify-between">
                                <span id="nombre-medicamento" class="text-sm font-medium text-green-800"></span>
                                <button onclick="limpiarMedicamento()" class="text-green-600 hover:text-green-800 text-xs">Cambiar</button>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <div class="w-24">
                                <input type="number" id="cantidad-medicamento" min="1" value="1"
                                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                                       placeholder="Cantidad">
                            </div>
                            <button onclick="agregarMedicamentoCirugia()"
                                    class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Agregar a la Cirugía
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Timeline -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <h3 class="text-base font-bold text-gray-800 mb-4">Timeline</h3>
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Programada</p>
                            <p class="text-xs text-gray-500">{{ $cita->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @if($cita->timestamp_inicio)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-amber-500 rounded-full mt-2"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Iniciada</p>
                                <p class="text-xs text-gray-500">{{ $cita->timestamp_inicio->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                    @endif
                    @if($cita->timestamp_fin)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Finalizada</p>
                                <p class="text-xs text-gray-500">{{ $cita->timestamp_fin->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Información de Registro -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                <h3 class="text-base font-bold text-gray-800 mb-4">Información de Registro</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Registrado por</span>
                        <span class="text-sm font-medium">{{ $cita->usuarioRegistro->name }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">Fecha de Registro</span>
                        <span class="text-sm">{{ $cita->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($cita->observaciones)
                        <div class="pt-3 border-t border-gray-200">
                            <div class="text-sm text-gray-500 mb-2">Observaciones</div>
                            <p class="text-gray-700 text-sm">{{ $cita->observaciones }}</p>
                        </div>
                    @endif
                    @if($cita->motivo_cancelacion)
                        <div class="pt-3 border-t border-gray-200">
                            <div class="text-sm text-gray-500 mb-2">Motivo de Cancelación</div>
                            <p class="text-red-700 text-sm">{{ $cita->motivo_cancelacion }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Vista Desktop: Layout Original -->
        <div class="hidden lg:block">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Información Principal -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Información Quirúrgica -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Información Quirúrgica</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Tipo de Cirugía</label>
                            <p class="text-gray-900 font-semibold capitalize">{{ $cita->tipo_cirugia }}</p>
                            @if($cita->tipo_final && $cita->tipo_final !== $cita->tipo_cirugia)
                                <p class="text-sm text-amber-600 mt-1">Tipo final: {{ $cita->tipo_final }}</p>
                            @endif
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Quirófano</label>
                            <p class="text-gray-900 font-semibold">Quirófano {{ $cita->quirofano->id }} ({{ $cita->quirofano->tipo }})</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Duración Estimada</label>
                            <p class="text-gray-900 font-semibold">{{ $cita->duracion_estimada }} minutos</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Horario Estimado</label>
                            <p class="text-gray-900 font-semibold">{{ $cita->hora_inicio_estimada->format('H:i') }} - {{ $cita->hora_fin_estimada->format('H:i') }}</p>
                        </div>
                        @if($cita->hora_inicio_real)
                            <div>
                                <label class="text-sm font-medium text-gray-500">Horario Real</label>
                                <p class="text-gray-900 font-semibold">{{ $cita->hora_inicio_real->format('H:i') }} - {{ $cita->hora_fin_real?->format('H:i') ?? 'En curso' }}</p>
                            </div>
                        @endif
                        @if($cita->duracion_real)
                            <div>
                                <label class="text-sm font-medium text-gray-500">Duración Real</label>
                                <p class="text-gray-900 font-semibold">{{ $cita->duracion_real }} minutos</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                @if($cita->descripcion_cirugia)
                    <div class="mt-6">
                        <label class="text-sm font-medium text-gray-500">Descripción de Cirugía</label>
                        <p class="text-gray-700 mt-2">{{ $cita->descripcion_cirugia }}</p>
                    </div>
                @endif
            </div>

            <!-- Equipo Quirúrgico -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Equipo Quirúrgico</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Cirujano Responsable</label>
                        <p class="text-gray-900 font-semibold">{{ optional($cita->cirujano->user)->name ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-600">CI: {{ $cita->cirujano->ci }}</p>
                    </div>
                    @if($cita->instrumentista)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Instrumentista</label>
                            <p class="text-gray-900 font-semibold">{{ optional($cita->instrumentista->user)->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-600">CI: {{ $cita->instrumentista->ci }}</p>
                        </div>
                    @else
                        <div>
                            <label class="text-sm font-medium text-gray-500">Instrumentista</label>
                            <p class="text-gray-400">No asignado</p>
                        </div>
                    @endif
                    @if($cita->anestesiologo)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Anestesiólogo</label>
                            <p class="text-gray-900 font-semibold">{{ optional($cita->anestesiologo->user)->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-600">CI: {{ $cita->anestesiologo->ci }}</p>
                        </div>
                    @else
                        <div>
                            <label class="text-sm font-medium text-gray-500">Anestesiólogo</label>
                            <p class="text-gray-400">No asignado</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Información del Paciente -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Información del Paciente</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Nombre Completo</label>
                            <p class="text-gray-900 font-semibold">{{ $cita->paciente->nombre }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Cédula de Identidad</label>
                            <p class="text-gray-900 font-semibold">{{ $cita->paciente->ci }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Teléfono</label>
                            <p class="text-gray-900">{{ $cita->paciente->telefono ?? 'No registrado' }}</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Seguro Médico</label>
                            <p class="text-gray-900">{{ $cita->paciente->seguro->nombre ?? 'Particular' }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Código de Registro</label>
                            <p class="text-gray-900 font-semibold">{{ $cita->paciente->codigo_registro }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Registrado por</label>
                            <p class="text-gray-900">{{ $cita->paciente->registro->usuario->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            
            <!-- Costos -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Costos</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Costo Base</span>
                        <span class="font-semibold text-gray-900">Bs. {{ number_format($cita->costo_base, 2) }}</span>
                    </div>

                    @php
                        $cuentaCobroDesktop = \App\Models\CuentaCobro::where('referencia_type', \App\Models\CitaQuirurgica::class)
                            ->where('referencia_id', $cita->id)
                            ->first();
                        $medicamentosCostoDesktop = $cuentaCobroDesktop ? $cuentaCobroDesktop->detalles()->where('tipo_item', 'medicamento')->get() : collect();
                    @endphp

                    @if($medicamentosCostoDesktop->count() > 0)
                        <div class="pt-2 border-t border-gray-100">
                            <p class="text-xs font-medium text-gray-500 mb-2">Medicamentos:</p>
                            @foreach($medicamentosCostoDesktop as $med)
                                <div class="flex justify-between items-center py-1 text-sm">
                                    <span class="text-gray-600">{{ $med->descripcion }}</span>
                                    <span class="text-gray-700">Bs. {{ number_format($med->subtotal, 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($cita->costo_final)
                        <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                            <span class="text-sm text-gray-600">Costo Final</span>
                            <span class="font-bold text-lg text-green-600">Bs. {{ number_format($cita->costo_final, 2) }}</span>
                        </div>
                        @if($cita->costo_final > $cita->costo_base)
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Diferencia</span>
                                <span class="font-semibold text-amber-600">+Bs. {{ number_format($cita->costo_final - $cita->costo_base, 2) }}</span>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            @if(in_array($cita->estado, ['programada', 'en_curso']) && Auth::user()->isAdmin())
            <!-- Medicamentos e Insumos -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                    Medicamentos e Insumos
                </h3>

                <!-- Lista de medicamentos agregados -->
                <div id="lista-medicamentos-usados-desktop" class="space-y-2 mb-4">
                    <div class="text-center text-gray-500 text-sm py-4">
                        Cargando medicamentos...
                    </div>
                </div>

                <!-- Formulario para agregar medicamento -->
                <div class="border-t border-gray-200 pt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Agregar Medicamento</h4>
                    <div class="space-y-3">
                        <!-- Buscador de medicamentos -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" id="buscar-medicamento-desktop"
                                   class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                   placeholder="Buscar medicamento..." autocomplete="off">
                            <!-- Dropdown de resultados -->
                            <div id="resultados-medicamento-desktop" class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-auto"></div>
                            <!-- Medicamento seleccionado (oculto) -->
                            <input type="hidden" id="medicamento-id-desktop">
                            <input type="hidden" id="medicamento-precio-desktop">
                            <input type="hidden" id="medicamento-stock-desktop">
                        </div>
                        <!-- Info del medicamento seleccionado -->
                        <div id="info-medicamento-desktop" class="hidden bg-green-50 rounded-lg p-2 border border-green-100">
                            <div class="flex items-center justify-between">
                                <span id="nombre-medicamento-desktop" class="text-sm font-medium text-green-800"></span>
                                <button onclick="limpiarMedicamentoDesktop()" class="text-green-600 hover:text-green-800 text-xs">Cambiar</button>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <div class="w-24">
                                <input type="number" id="cantidad-medicamento-desktop" min="1" value="1"
                                       class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                                       placeholder="Cantidad">
                            </div>
                            <button onclick="agregarMedicamentoCirugiaDesktop()"
                                    class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Agregar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if(in_array($cita->estado, ['programada', 'en_curso']) && (Auth::user()->isAdmin() || Auth::user()->isCirujano() || Auth::user()->hasRole('administrador')))
            <!-- Equipos Médicos y Procedimientos -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                    </svg>
                    Equipos Médicos y Procedimientos
                </h3>

                <!-- Lista de equipos médicos agregados -->
                <div id="lista-equipos-medicos" class="space-y-2 mb-4">
                    <div class="text-center text-gray-500 text-sm py-4">
                        Cargando equipos médicos...
                    </div>
                </div>

                <!-- Formulario para agregar equipo médico -->
                <div class="border-t border-gray-200 pt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Agregar Equipo/Procedimiento</h4>
                    <div class="space-y-3">
                        <input type="text" id="nombre-equipo-medico" placeholder="Nombre del equipo o procedimiento"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <div class="flex gap-3">
                            <input type="number" id="precio-equipo-medico" placeholder="Precio (Bs.)" min="0" step="0.01"
                                   class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <input type="number" id="cantidad-equipo-medico" placeholder="Cantidad" min="1" value="1"
                                   class="w-24 border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        </div>
                        <button onclick="agregarEquipoMedicoCirugia()"
                                class="w-full bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Agregar Equipo
                        </button>
                    </div>
                </div>
            </div>
            @endif

            <!-- Timeline -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Timeline</h3>
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Programada</p>
                            <p class="text-xs text-gray-500">{{ $cita->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @if($cita->timestamp_inicio)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-amber-500 rounded-full mt-2"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Iniciada</p>
                                <p class="text-xs text-gray-500">{{ $cita->timestamp_inicio->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                    @endif
                    @if($cita->timestamp_fin)
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Finalizada</p>
                                <p class="text-xs text-gray-500">{{ $cita->timestamp_fin->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Información de Registro -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Información de Registro</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Registrado por</label>
                        <p class="text-gray-900">{{ $cita->usuarioRegistro->name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Fecha de Registro</label>
                        <p class="text-gray-900">{{ $cita->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($cita->observaciones)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Observaciones</label>
                            <p class="text-gray-700 text-sm mt-1">{{ $cita->observaciones }}</p>
                        </div>
                    @endif
                    @if($cita->motivo_cancelacion)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Motivo de Cancelación</label>
                            <p class="text-red-700 text-sm mt-1">{{ $cita->motivo_cancelacion }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Cancelación -->
<div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Cancelar Cita Quirúrgica</h3>
        <form id="cancelForm">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Motivo de Cancelación *</label>
                <textarea name="motivo_cancelacion" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required placeholder="Describir el motivo de la cancelación..."></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeCancelModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Confirmar Cancelación
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentCitaId = null;

function iniciarCirugia(citaId) {
    if (confirm('¿Está seguro de iniciar esta cirugía?')) {
        fetch(`/quirofano/${citaId}/iniciar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error al iniciar la cirugía');
        });
    }
}

function finalizarCirugia(citaId) {
    if (confirm('¿Está seguro de finalizar esta cirugía? El sistema calculará automáticamente el tiempo real y los costos.')) {
        fetch(`/quirofano/${citaId}/finalizar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error al finalizar la cirugía');
        });
    }
}

function cancelarCita(citaId) {
    currentCitaId = citaId;
    document.getElementById('cancelModal').style.display = 'flex';
}

function closeCancelModal() {
    document.getElementById('cancelModal').style.display = 'none';
    currentCitaId = null;
}

document.getElementById('cancelForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const motivo = formData.get('motivo_cancelacion');
    
    fetch(`/quirofano/${currentCitaId}/cancelar`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ motivo_cancelacion: motivo })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al cancelar la cita');
    });
});

// Cerrar modal al hacer clic fuera
document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCancelModal();
    }
});

// Funciones para medicamentos en cirugía
let medicamentosDisponibles = [];
let medicamentosDisponiblesDesktop = [];

// Cargar medicamentos disponibles al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    @if(in_array($cita->estado, ['programada', 'en_curso']) && (Auth::user()->isAdmin() || Auth::user()->isCirujano() || Auth::user()->hasRole('administrador')))
    // Cargar para versión móvil
    if (document.getElementById('buscar-medicamento')) {
        cargarMedicamentosDisponibles();
        cargarMedicamentosUsados();
        inicializarBuscadorMedicamentos();
    }
    // Cargar para versión desktop
    if (document.getElementById('buscar-medicamento-desktop')) {
        cargarMedicamentosDisponiblesDesktop();
        cargarMedicamentosUsadosDesktop();
        inicializarBuscadorMedicamentosDesktop();
    }
    @endif
});

async function cargarMedicamentosDisponibles() {
    try {
        const response = await fetch('{{ route("quirofano.medicamentos.disponibles", $cita) }}', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        const data = await response.json();
        
        if (data.success) {
            medicamentosDisponibles = data.medicamentos;
        }
    } catch (error) {
        console.error('Error cargando medicamentos:', error);
    }
}

// Inicializar buscador de medicamentos (móvil)
function inicializarBuscadorMedicamentos() {
    const input = document.getElementById('buscar-medicamento');
    const resultados = document.getElementById('resultados-medicamento');
    
    if (!input) return;
    
    input.addEventListener('input', function() {
        const busqueda = this.value.toLowerCase().trim();
        
        if (busqueda.length === 0) {
            resultados.classList.add('hidden');
            return;
        }
        
        // Filtrar medicamentos
        const filtrados = medicamentosDisponibles.filter(med => 
            med.nombre.toLowerCase().includes(busqueda) ||
            med.tipo.toLowerCase().includes(busqueda)
        );
        
        // Mostrar resultados
        resultados.innerHTML = '';
        if (filtrados.length > 0) {
            filtrados.forEach(med => {
                const div = document.createElement('div');
                div.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm';
                div.innerHTML = `
                    <div class="font-medium">${med.nombre} (${med.tipo})</div>
                    <div class="text-xs text-gray-500">Stock: ${med.cantidad} ${med.unidad_medida || 'unidades'} - $${med.precio || 0}</div>
                `;
                div.onclick = () => seleccionarMedicamento(med);
                resultados.appendChild(div);
            });
            resultados.classList.remove('hidden');
        } else {
            resultados.innerHTML = '<div class="px-4 py-2 text-sm text-gray-500">No se encontraron medicamentos</div>';
            resultados.classList.remove('hidden');
        }
    });
    
    // Cerrar dropdown al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!input.contains(e.target) && !resultados.contains(e.target)) {
            resultados.classList.add('hidden');
        }
    });
}

function seleccionarMedicamento(med) {
    document.getElementById('medicamento-id').value = med.id;
    document.getElementById('medicamento-precio').value = med.precio || 0;
    document.getElementById('medicamento-stock').value = med.cantidad;
    document.getElementById('buscar-medicamento').value = '';
    document.getElementById('resultados-medicamento').classList.add('hidden');
    
    // Mostrar info del medicamento seleccionado
    const infoDiv = document.getElementById('info-medicamento');
    const nombreSpan = document.getElementById('nombre-medicamento');
    infoDiv.classList.remove('hidden');
    nombreSpan.textContent = `${med.nombre} (Stock: ${med.cantidad})`;
}

function limpiarMedicamento() {
    document.getElementById('medicamento-id').value = '';
    document.getElementById('medicamento-precio').value = '';
    document.getElementById('medicamento-stock').value = '';
    document.getElementById('buscar-medicamento').value = '';
    document.getElementById('info-medicamento').classList.add('hidden');
}

async function cargarMedicamentosUsados() {
    try {
        const response = await fetch('{{ route("quirofano.medicamentos.usados", $cita) }}', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        const data = await response.json();
        
        const container = document.getElementById('lista-medicamentos-usados');
        
        if (data.success && data.medicamentos.length > 0) {
            container.innerHTML = '';
            let totalMedicamentos = 0;
            
            data.medicamentos.forEach(med => {
                totalMedicamentos += parseFloat(med.subtotal);
                const div = document.createElement('div');
                div.className = 'flex justify-between items-center bg-gray-50 rounded-lg p-3';
                div.innerHTML = `
                    <div>
                        <p class="font-medium text-sm text-gray-900">${med.descripcion}</p>
                        <p class="text-xs text-gray-500">${med.cantidad} x $${parseFloat(med.precio_unitario).toFixed(2)}</p>
                    </div>
                    <span class="font-semibold text-sm text-gray-900">$${parseFloat(med.subtotal).toFixed(2)}</span>
                `;
                container.appendChild(div);
            });
            
            // Agregar total
            const totalDiv = document.createElement('div');
            totalDiv.className = 'flex justify-between items-center border-t border-gray-200 pt-2 mt-2';
            totalDiv.innerHTML = `
                <span class="font-semibold text-sm text-gray-700">Total Medicamentos:</span>
                <span class="font-bold text-green-600">$${totalMedicamentos.toFixed(2)}</span>
            `;
            container.appendChild(totalDiv);
        } else {
            container.innerHTML = '<div class="text-center text-gray-400 text-sm py-4">No hay medicamentos agregados aún</div>';
        }
    } catch (error) {
        console.error('Error cargando medicamentos usados:', error);
        document.getElementById('lista-medicamentos-usados').innerHTML = '<div class="text-center text-red-400 text-sm py-4">Error al cargar medicamentos</div>';
    }
}

async function agregarMedicamentoCirugia() {
    const medicamentoId = document.getElementById('medicamento-id').value;
    const cantidadInput = document.getElementById('cantidad-medicamento');
    const cantidad = parseInt(cantidadInput.value);
    const stockDisponible = parseInt(document.getElementById('medicamento-stock').value || 0);
    
    if (!medicamentoId) {
        alert('Por favor busque y seleccione un medicamento');
        return;
    }
    
    if (!cantidad || cantidad < 1) {
        alert('Por favor ingrese una cantidad válida');
        return;
    }
    
    if (cantidad > stockDisponible) {
        alert(`Stock insuficiente. Disponible: ${stockDisponible}`);
        return;
    }
    
    try {
        const response = await fetch('{{ route("quirofano.medicamentos.agregar", $cita) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                almacen_medicamento_id: medicamentoId,
                cantidad: cantidad
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Recargar listas
            cargarMedicamentosDisponibles();
            cargarMedicamentosUsados();
            // Resetear formulario
            limpiarMedicamento();
            cantidadInput.value = '1';
        } else {
            alert(data.message || 'Error al agregar medicamento');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al agregar medicamento');
    }
}

// Funciones para versión desktop
async function cargarMedicamentosDisponiblesDesktop() {
    try {
        const response = await fetch('{{ route("quirofano.medicamentos.disponibles", $cita) }}', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        const data = await response.json();
        
        if (data.success) {
            medicamentosDisponiblesDesktop = data.medicamentos;
        }
    } catch (error) {
        console.error('Error cargando medicamentos desktop:', error);
    }
}

// Inicializar buscador de medicamentos (desktop)
function inicializarBuscadorMedicamentosDesktop() {
    const input = document.getElementById('buscar-medicamento-desktop');
    const resultados = document.getElementById('resultados-medicamento-desktop');
    
    if (!input) return;
    
    input.addEventListener('input', function() {
        const busqueda = this.value.toLowerCase().trim();
        
        if (busqueda.length === 0) {
            resultados.classList.add('hidden');
            return;
        }
        
        // Filtrar medicamentos
        const filtrados = medicamentosDisponiblesDesktop.filter(med => 
            med.nombre.toLowerCase().includes(busqueda) ||
            med.tipo.toLowerCase().includes(busqueda)
        );
        
        // Mostrar resultados
        resultados.innerHTML = '';
        if (filtrados.length > 0) {
            filtrados.forEach(med => {
                const div = document.createElement('div');
                div.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm';
                div.innerHTML = `
                    <div class="font-medium">${med.nombre} (${med.tipo})</div>
                    <div class="text-xs text-gray-500">Stock: ${med.cantidad} ${med.unidad_medida || 'unidades'} - $${med.precio || 0}</div>
                `;
                div.onclick = () => seleccionarMedicamentoDesktop(med);
                resultados.appendChild(div);
            });
            resultados.classList.remove('hidden');
        } else {
            resultados.innerHTML = '<div class="px-4 py-2 text-sm text-gray-500">No se encontraron medicamentos</div>';
            resultados.classList.remove('hidden');
        }
    });
    
    // Cerrar dropdown al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!input.contains(e.target) && !resultados.contains(e.target)) {
            resultados.classList.add('hidden');
        }
    });
}

function seleccionarMedicamentoDesktop(med) {
    document.getElementById('medicamento-id-desktop').value = med.id;
    document.getElementById('medicamento-precio-desktop').value = med.precio || 0;
    document.getElementById('medicamento-stock-desktop').value = med.cantidad;
    document.getElementById('buscar-medicamento-desktop').value = '';
    document.getElementById('resultados-medicamento-desktop').classList.add('hidden');
    
    // Mostrar info del medicamento seleccionado
    const infoDiv = document.getElementById('info-medicamento-desktop');
    const nombreSpan = document.getElementById('nombre-medicamento-desktop');
    infoDiv.classList.remove('hidden');
    nombreSpan.textContent = `${med.nombre} (Stock: ${med.cantidad})`;
}

function limpiarMedicamentoDesktop() {
    document.getElementById('medicamento-id-desktop').value = '';
    document.getElementById('medicamento-precio-desktop').value = '';
    document.getElementById('medicamento-stock-desktop').value = '';
    document.getElementById('buscar-medicamento-desktop').value = '';
    document.getElementById('info-medicamento-desktop').classList.add('hidden');
}

async function cargarMedicamentosUsadosDesktop() {
    try {
        const response = await fetch('{{ route("quirofano.medicamentos.usados", $cita) }}', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        const data = await response.json();
        
        const container = document.getElementById('lista-medicamentos-usados-desktop');
        if (!container) return;
        
        if (data.success && data.medicamentos.length > 0) {
            container.innerHTML = '';
            let totalMedicamentos = 0;
            
            data.medicamentos.forEach(med => {
                totalMedicamentos += parseFloat(med.subtotal);
                const div = document.createElement('div');
                div.className = 'flex justify-between items-center bg-gray-50 rounded-lg p-3';
                div.innerHTML = `
                    <div>
                        <p class="font-medium text-sm text-gray-900">${med.descripcion}</p>
                        <p class="text-xs text-gray-500">${med.cantidad} x $${parseFloat(med.precio_unitario).toFixed(2)}</p>
                    </div>
                    <span class="font-semibold text-sm text-gray-900">$${parseFloat(med.subtotal).toFixed(2)}</span>
                `;
                container.appendChild(div);
            });
            
            // Agregar total
            const totalDiv = document.createElement('div');
            totalDiv.className = 'flex justify-between items-center border-t border-gray-200 pt-2 mt-2';
            totalDiv.innerHTML = `
                <span class="font-semibold text-sm text-gray-700">Total Medicamentos:</span>
                <span class="font-bold text-green-600">$${totalMedicamentos.toFixed(2)}</span>
            `;
            container.appendChild(totalDiv);
        } else {
            container.innerHTML = '<div class="text-center text-gray-400 text-sm py-4">No hay medicamentos agregados aún</div>';
        }
    } catch (error) {
        console.error('Error cargando medicamentos usados desktop:', error);
        const container = document.getElementById('lista-medicamentos-usados-desktop');
        if (container) {
            container.innerHTML = '<div class="text-center text-red-400 text-sm py-4">Error al cargar medicamentos</div>';
        }
    }
}

async function agregarMedicamentoCirugiaDesktop() {
    const medicamentoId = document.getElementById('medicamento-id-desktop').value;
    const cantidadInput = document.getElementById('cantidad-medicamento-desktop');
    const cantidad = parseInt(cantidadInput.value);
    const stockDisponible = parseInt(document.getElementById('medicamento-stock-desktop').value || 0);
    
    if (!medicamentoId) {
        alert('Por favor busque y seleccione un medicamento');
        return;
    }
    
    if (!cantidad || cantidad < 1) {
        alert('Por favor ingrese una cantidad válida');
        return;
    }
    
    if (cantidad > stockDisponible) {
        alert(`Stock insuficiente. Disponible: ${stockDisponible}`);
        return;
    }
    
    try {
        const response = await fetch('{{ route("quirofano.medicamentos.agregar", $cita) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                almacen_medicamento_id: medicamentoId,
                cantidad: cantidad
            })
        });
        
        const data = await response.json();

        if (data.success) {
            // Recargar listas
            cargarMedicamentosDisponiblesDesktop();
            cargarMedicamentosUsadosDesktop();
            // Resetear formulario
            limpiarMedicamentoDesktop();
            cantidadInput.value = '1';
        } else {
            alert(data.message || 'Error al agregar medicamento');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al agregar medicamento');
    }
}

// Funciones para equipos médicos en cirugía
async function cargarEquiposMedicos() {
    try {
        const response = await fetch('{{ route("quirofano.equipos-medicos.lista", $cita) }}', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        const data = await response.json();

        const container = document.getElementById('lista-equipos-medicos');
        if (!container) return;

        if (data.success && data.equipos.length > 0) {
            container.innerHTML = '';
            let totalEquipos = 0;

            data.equipos.forEach(equipo => {
                totalEquipos += parseFloat(equipo.subtotal);
                const div = document.createElement('div');
                div.className = 'flex justify-between items-center bg-gray-50 rounded-lg p-3';
                div.innerHTML = `
                    <div>
                        <p class="font-medium text-sm text-gray-900">${equipo.nombre}</p>
                        <p class="text-xs text-gray-500">${equipo.cantidad} x Bs.${parseFloat(equipo.precio_unitario).toFixed(2)}</p>
                    </div>
                    <span class="font-semibold text-sm text-cyan-600">Bs.${parseFloat(equipo.subtotal).toFixed(2)}</span>
                `;
                container.appendChild(div);
            });

            // Agregar total
            const totalDiv = document.createElement('div');
            totalDiv.className = 'flex justify-between items-center border-t border-gray-200 pt-2 mt-2';
            totalDiv.innerHTML = `
                <span class="font-semibold text-sm text-gray-700">Total Equipos:</span>
                <span class="font-bold text-cyan-600">Bs.${totalEquipos.toFixed(2)}</span>
            `;
            container.appendChild(totalDiv);
        } else {
            container.innerHTML = '<div class="text-center text-gray-400 text-sm py-4">No hay equipos médicos agregados aún</div>';
        }
    } catch (error) {
        console.error('Error cargando equipos médicos:', error);
        const container = document.getElementById('lista-equipos-medicos');
        if (container) {
            container.innerHTML = '<div class="text-center text-red-400 text-sm py-4">Error al cargar equipos médicos</div>';
        }
    }
}

async function agregarEquipoMedicoCirugia() {
    const nombreInput = document.getElementById('nombre-equipo-medico');
    const precioInput = document.getElementById('precio-equipo-medico');
    const cantidadInput = document.getElementById('cantidad-equipo-medico');

    const nombre = nombreInput.value.trim();
    const precio = parseFloat(precioInput.value);
    const cantidad = parseInt(cantidadInput.value);

    if (!nombre) {
        alert('Por favor ingrese el nombre del equipo o procedimiento');
        return;
    }

    if (isNaN(precio) || precio < 0) {
        alert('Por favor ingrese un precio válido');
        return;
    }

    if (isNaN(cantidad) || cantidad < 1) {
        alert('Por favor ingrese una cantidad válida');
        return;
    }

    try {
        const response = await fetch('{{ route("quirofano.equipos-medicos.agregar", $cita) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                nombre: nombre,
                precio: precio,
                cantidad: cantidad
            })
        });

        const data = await response.json();

        if (data.success) {
            // Recargar lista
            cargarEquiposMedicos();
            // Resetear formulario
            nombreInput.value = '';
            precioInput.value = '';
            cantidadInput.value = '1';
        } else {
            alert(data.message || 'Error al agregar equipo médico');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al agregar equipo médico');
    }
}

// Cargar equipos médicos al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    @if(in_array($cita->estado, ['programada', 'en_curso']) && (Auth::user()->isAdmin() || Auth::user()->isCirujano() || Auth::user()->hasRole('administrador')))
    if (document.getElementById('lista-equipos-medicos')) {
        cargarEquiposMedicos();
    }
    @endif
});
</script>
@endsection
