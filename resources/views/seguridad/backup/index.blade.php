@extends('layouts.app')

@section('content')
<div class="p-4 sm:p-8 bg-gray-50/50 min-h-screen">

    {{-- Encabezado --}}
    <div class="mb-6">
        <div class="flex items-center gap-3 text-blue-600 mb-1">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
            </svg>
            <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Respaldos del Sistema</h1>
        </div>
        <p class="text-sm text-gray-500 ml-11 font-medium">
            Genere copias de seguridad, prográmelas automáticamente y restaure desde un respaldo anterior.
        </p>
    </div>

    {{-- Mensajes flash --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6 flex items-center gap-2">
            <svg class="w-5 h-5 text-green-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span class="text-sm font-medium text-green-800">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 flex items-start gap-2">
            <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <span class="text-sm font-medium text-red-800">{{ session('error') }}</span>
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
            <ul class="text-sm font-medium text-red-800 list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ============ COLUMNA PRINCIPAL ============ --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Respaldo manual --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6"
                 x-data="{ generando: false }">
                <div class="flex items-center gap-2 mb-1">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <h2 class="text-lg font-bold text-gray-800">Respaldo manual</h2>
                </div>
                <p class="text-sm text-gray-500 mb-4">Genera una copia completa en este momento y queda disponible para descargar.</p>

                <form method="POST" action="{{ route('seguridad.backup.crear') }}" @submit="generando = true">
                    @csrf
                    <label class="flex items-center gap-2 mb-4 cursor-pointer select-none">
                        <input type="checkbox" name="incluir_archivos" value="1" checked
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Incluir archivos subidos (imágenes, comprobantes)</span>
                    </label>

                    <button type="submit" :disabled="generando"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:opacity-60 disabled:cursor-wait text-white text-sm font-semibold rounded-lg shadow-sm transition">
                        <svg x-show="!generando" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <svg x-show="generando" x-cloak class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span x-text="generando ? 'Generando respaldo...' : 'Generar respaldo ahora'"></span>
                    </button>
                </form>
            </div>

            {{-- Lista de respaldos --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-800">Respaldos disponibles</h2>
                    <span class="text-xs font-semibold text-gray-400 bg-gray-100 px-2.5 py-1 rounded-full">{{ $backups->count() }}</span>
                </div>

                @if($backups->isEmpty())
                    <div class="px-6 py-12 text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                        <p class="text-sm text-gray-400">Todavía no hay respaldos generados.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider bg-gray-50/70">
                                    <th class="px-6 py-3">Fecha</th>
                                    <th class="px-6 py-3">Tipo</th>
                                    <th class="px-6 py-3">Tamaño</th>
                                    <th class="px-6 py-3">Estado</th>
                                    <th class="px-6 py-3 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($backups as $backup)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="px-6 py-3">
                                            <div class="font-medium text-gray-800">{{ $backup->created_at->format('d/m/Y H:i') }}</div>
                                            <div class="text-xs text-gray-400">{{ $backup->autor->name ?? 'Automático' }}</div>
                                        </td>
                                        <td class="px-6 py-3">
                                            @php
                                                $badge = match($backup->type) {
                                                    'automatico' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                                                    'pre_restauracion' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                    default => 'bg-blue-50 text-blue-700 border-blue-200',
                                                };
                                            @endphp
                                            <span class="inline-flex text-xs font-semibold px-2.5 py-1 rounded-full border {{ $badge }}">
                                                {{ $backup->tipo_legible }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 text-gray-600">{{ $backup->tamano_legible }}</td>
                                        <td class="px-6 py-3">
                                            @if($backup->estado === 'completado')
                                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-700">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Completado
                                                </span>
                                            @elseif($backup->estado === 'fallido')
                                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-red-600" title="{{ $backup->error }}">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Fallido
                                                </span>
                                            @else
                                                <span class="text-xs font-semibold text-gray-400">En proceso</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-3">
                                            <div class="flex items-center justify-end gap-1">
                                                @if($backup->estado === 'completado')
                                                    <a href="{{ route('seguridad.backup.descargar', $backup) }}"
                                                       class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Descargar">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                        </svg>
                                                    </a>
                                                @endif
                                                <form method="POST" action="{{ route('seguridad.backup.eliminar', $backup) }}"
                                                      onsubmit="return confirm('¿Eliminar este respaldo? Esta acción no se puede deshacer.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition" title="Eliminar">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- ZONA PELIGROSA: Restaurar --}}
            <div class="bg-white rounded-2xl border-2 border-red-200 shadow-sm overflow-hidden"
                 x-data="restauracion()">
                <div class="px-6 py-4 bg-red-50/70 border-b border-red-100">
                    <div class="flex items-center gap-2 text-red-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <h2 class="text-lg font-bold">Restaurar desde un respaldo</h2>
                    </div>
                    <p class="text-sm text-red-600/90 mt-1">
                        Reemplaza <strong>toda la base de datos actual</strong> por la del archivo que subas.
                        Antes de restaurar, el sistema genera automáticamente un respaldo de seguridad del estado actual.
                    </p>
                </div>

                <form method="POST" action="{{ route('seguridad.backup.restaurar') }}" enctype="multipart/form-data"
                      x-ref="form" @submit="restaurando = true" class="p-6 space-y-4">
                    @csrf
                    {{-- La confirmación tipeada viaja al servidor (segunda barrera server-side) --}}
                    <input type="hidden" name="confirmacion" :value="palabra">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Archivo de respaldo (.zip)</label>
                        <input type="file" name="archivo" accept=".zip" x-ref="archivo"
                               @change="archivoNombre = $refs.archivo.files.length ? $refs.archivo.files[0].name : ''"
                               class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer">
                        <p class="text-xs text-gray-400 mt-1">Solo respaldos generados por este sistema (contienen el manifiesto de validación).</p>
                    </div>

                    <button type="button" @click="abrir()"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Restaurar sistema
                    </button>

                    {{-- ===== Modal de doble confirmación ===== --}}
                    <div x-show="modal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
                         x-transition.opacity>
                        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="cerrar()"></div>

                        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden"
                             @click.stop>
                            {{-- Paso 1: advertencia --}}
                            <template x-if="paso === 1">
                                <div class="p-6">
                                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 rounded-full bg-red-100">
                                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-center text-gray-800 mb-2">¿Restaurar la base de datos?</h3>
                                    <p class="text-sm text-gray-600 text-center mb-1">
                                        Vas a reemplazar <strong>todos los datos actuales</strong> por los del archivo:
                                    </p>
                                    <p class="text-sm font-mono text-center text-red-700 bg-red-50 rounded-lg py-2 px-3 mb-4 break-all" x-text="archivoNombre"></p>
                                    <p class="text-xs text-gray-500 text-center mb-5">
                                        Se generará un respaldo de seguridad del estado actual antes de continuar, por si necesitás revertir.
                                    </p>
                                    <div class="flex gap-3">
                                        <button type="button" @click="cerrar()"
                                                class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition">Cancelar</button>
                                        <button type="button" @click="paso = 2"
                                                class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition">Entiendo, continuar</button>
                                    </div>
                                </div>
                            </template>

                            {{-- Paso 2: confirmación tipeada --}}
                            <template x-if="paso === 2">
                                <div class="p-6">
                                    <h3 class="text-lg font-bold text-center text-gray-800 mb-2">Confirmación final</h3>
                                    <p class="text-sm text-gray-600 text-center mb-4">
                                        Para confirmar, escribí <strong class="text-red-600">RESTAURAR</strong> en mayúsculas:
                                    </p>
                                    <input type="text" x-model="palabra" placeholder="RESTAURAR" autocomplete="off"
                                           @keydown.enter.prevent="palabra === 'RESTAURAR' && ejecutar()"
                                           class="w-full text-center font-mono tracking-widest px-4 py-2.5 border-2 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 mb-4"
                                           :class="palabra === 'RESTAURAR' ? 'border-green-400' : 'border-gray-300'">
                                    <div class="flex gap-3">
                                        <button type="button" @click="cerrar()"
                                                class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition">Cancelar</button>
                                        <button type="button" @click="ejecutar()" :disabled="palabra !== 'RESTAURAR' || restaurando"
                                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-semibold rounded-lg transition">
                                            <svg x-show="restaurando" x-cloak class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                            </svg>
                                            <span x-text="restaurando ? 'Restaurando...' : 'Restaurar definitivamente'"></span>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ============ COLUMNA LATERAL ============ --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- Respaldos automáticos --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center gap-2 mb-1">
                    <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h2 class="text-lg font-bold text-gray-800">Respaldos automáticos</h2>
                </div>
                <p class="text-sm text-gray-500 mb-4">Programá cada cuánto el sistema genera un respaldo por su cuenta.</p>

                <form method="POST" action="{{ route('seguridad.backup.configuracion') }}" class="space-y-4">
                    @csrf

                    <label class="flex items-center justify-between gap-2 cursor-pointer select-none">
                        <span class="text-sm font-medium text-gray-700">Activar respaldo automático</span>
                        <input type="checkbox" name="automatico_activo" value="1" @checked($config->automatico_activo)
                               class="rounded border-gray-300 text-cyan-600 focus:ring-cyan-500 w-5 h-5">
                    </label>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Frecuencia</label>
                        <select name="frecuencia" class="w-full text-sm border-gray-300 rounded-lg focus:ring-cyan-500 focus:border-cyan-500">
                            <option value="diario" @selected($config->frecuencia === 'diario')>Diario</option>
                            <option value="cada_2_dias" @selected($config->frecuencia === 'cada_2_dias')>Cada 2 días</option>
                            <option value="semanal" @selected($config->frecuencia === 'semanal')>Semanal</option>
                            <option value="quincenal" @selected($config->frecuencia === 'quincenal')>Quincenal (15 días)</option>
                            <option value="mensual" @selected($config->frecuencia === 'mensual')>Mensual</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Hora preferida</label>
                        <input type="time" name="hora" value="{{ $config->hora->format('H:i') }}"
                               class="w-full text-sm border-gray-300 rounded-lg focus:ring-cyan-500 focus:border-cyan-500">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Conservar últimos</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="retencion" min="1" max="365" value="{{ $config->retencion }}"
                                   class="w-24 text-sm border-gray-300 rounded-lg focus:ring-cyan-500 focus:border-cyan-500">
                            <span class="text-sm text-gray-500">respaldos automáticos</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Los más antiguos se eliminan solos para no llenar el disco.</p>
                    </div>

                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input type="checkbox" name="incluir_archivos" value="1" @checked($config->incluir_archivos)
                               class="rounded border-gray-300 text-cyan-600 focus:ring-cyan-500">
                        <span class="text-sm text-gray-700">Incluir archivos subidos</span>
                    </label>

                    <button type="submit"
                            class="w-full px-4 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                        Guardar configuración
                    </button>
                </form>

                <div class="mt-4 pt-4 border-t border-gray-100 text-xs text-gray-500 space-y-1">
                    <div class="flex justify-between">
                        <span>Última ejecución:</span>
                        <span class="font-medium text-gray-700">{{ $config->ultima_ejecucion_at?->format('d/m/Y H:i') ?? 'Nunca' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Estado:</span>
                        @if($config->automatico_activo)
                            <span class="font-medium text-green-600">Activo</span>
                        @else
                            <span class="font-medium text-gray-400">Desactivado</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Activar el cron en la nube --}}
            <!-- <div class="bg-amber-50 rounded-2xl border border-amber-200 p-6" x-data="{ copiado: false }">
                <div class="flex items-center gap-2 mb-2 text-amber-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-sm font-bold">Importante: activar el cron</h3>
                </div>
                <p class="text-xs text-amber-700/90 mb-3">
                    Para que los respaldos automáticos funcionen en la nube, agregá <strong>una sola tarea cron</strong>
                    en cPanel (Cron Jobs) que se ejecute cada minuto:
                </p>
                <div class="relative">
                    <code class="block bg-white border border-amber-200 rounded-lg p-3 text-[11px] text-gray-700 break-all pr-10" x-ref="cron">{{ $cron }}</code>
                    <button type="button"
                            @click="navigator.clipboard.writeText($refs.cron.innerText); copiado = true; setTimeout(() => copiado = false, 1500)"
                            class="absolute top-2 right-2 p-1.5 text-amber-600 hover:bg-amber-100 rounded transition" title="Copiar">
                        <svg x-show="!copiado" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        <svg x-show="copiado" x-cloak class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </button>
                </div>
                <p class="text-[11px] text-amber-700/80 mt-2">
                    El cron corre cada minuto, pero el respaldo solo se genera cuando vence la frecuencia configurada.
                </p>
            </div> -->
        </div>
    </div>
</div>

@push('scripts')
<script>
    function restauracion() {
        return {
            modal: false,
            paso: 1,
            palabra: '',
            archivoNombre: '',
            restaurando: false,
            abrir() {
                if (! this.$refs.archivo.files.length) {
                    alert('Primero seleccioná el archivo .zip de respaldo a restaurar.');
                    return;
                }
                this.archivoNombre = this.$refs.archivo.files[0].name;
                this.palabra = '';
                this.paso = 1;
                this.modal = true;
            },
            cerrar() {
                if (this.restaurando) return;
                this.modal = false;
                this.paso = 1;
                this.palabra = '';
            },
            ejecutar() {
                if (this.palabra !== 'RESTAURAR' || this.restaurando) return;
                this.restaurando = true;
                this.$refs.form.submit();
            },
        };
    }
</script>
@endpush
@endsection
