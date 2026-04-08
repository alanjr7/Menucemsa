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
                        <span class="font-semibold text-sm">${{ number_format($cita->costo_base, 2) }}</span>
                    </div>
                    @if($cita->costo_final)
                        <div class="pt-3 border-t border-gray-200">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Costo Final</span>
                                <span class="font-bold text-lg text-green-600">${{ number_format($cita->costo_final, 2) }}</span>
                            </div>
                            @if($cita->costo_final > $cita->costo_base)
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-sm text-gray-600">Diferencia</span>
                                    <span class="font-semibold text-amber-600 text-sm">+${{ number_format($cita->costo_final - $cita->costo_base, 2) }}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

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
                        <span class="font-semibold text-gray-900">${{ number_format($cita->costo_base, 2) }}</span>
                    </div>
                    @if($cita->costo_final)
                        <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                            <span class="text-sm text-gray-600">Costo Final</span>
                            <span class="font-bold text-lg text-green-600">${{ number_format($cita->costo_final, 2) }}</span>
                        </div>
                        @if($cita->costo_final > $cita->costo_base)
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Diferencia</span>
                                <span class="font-semibold text-amber-600">+${{ number_format($cita->costo_final - $cita->costo_base, 2) }}</span>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

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
</script>
@endsection
