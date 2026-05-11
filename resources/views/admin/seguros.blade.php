@extends('layouts.app')

@section('content')
    <div class="w-full p-8 bg-[#f8fafc] min-h-screen font-sans antialiased">

        <!-- Header -->
        <div class="flex justify-between items-start mb-8">
            <div>
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                stroke-width="2" />
                        </svg>
                    </div>
                    <h1 class="text-[26px] font-black text-slate-800 tracking-tight">Seguros y Preautorizaciones</h1>
                </div>
                <p class="text-slate-500 text-[15px] font-medium mt-1 ml-11">Gestión de autorizaciones, convenios y
                    afiliados</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.seguros.historial') }}"
                    class="bg-white hover:bg-gray-50 text-slate-700 px-6 py-2.5 rounded-xl flex items-center gap-2 text-sm font-bold shadow-sm border border-slate-200 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" />
                    </svg>
                    Historial
                </a>
                <button onclick="abrirModalNuevoSeguro()"
                    class="bg-[#0061df] hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl flex items-center gap-2 text-sm font-bold shadow-md transition-all">
                    <span class="text-lg">+</span> Nuevo Seguro
                </button>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 text-center">
                <p class="text-slate-400 text-[13px] font-medium mb-1">Pendientes</p>
                <p class="text-[#e67e22] text-[32px] font-black tracking-tighter">{{ $stats['pendientes'] ?? 0 }}</p>
            </div>
            <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 text-center">
                <p class="text-slate-400 text-[13px] font-medium mb-1">Rechazadas</p>
                <p class="text-[#e03131] text-[32px] font-black tracking-tighter">{{ $stats['rechazadas'] ?? 0 }}</p>
            </div>
            <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 text-center">
                <p class="text-slate-400 text-[13px] font-medium mb-1">Aprobadas</p>
                <p class="text-[#0ca678] text-[32px] font-black tracking-tighter">{{ $stats['aprobadas'] ?? 0 }}</p>
            </div>
            <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 text-center">
                <p class="text-slate-400 text-[13px] font-medium mb-1">Monto Total</p>
                <p class="text-slate-800 text-[32px] font-black tracking-tighter">Bs.
                    {{ number_format($stats['monto_total'] ?? 0, 2) }}</p>
            </div>
        </div>

        <!-- Filtros de Preautorizaciones -->
        <div class="bg-white p-5 rounded-[20px] border border-slate-100 shadow-sm mb-6 flex items-center gap-4">
            <div class="relative flex-1">
                <input type="text" id="buscarPreautorizacion" onkeyup="filtrarPreautorizaciones()"
                    placeholder="Buscar por número, paciente, seguro, estado..."
                    class="w-full pl-12 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-50 outline-none transition-all placeholder:text-slate-300">
                <svg class="w-5 h-5 absolute left-4 top-3.5 text-slate-300" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" />
                </svg>
            </div>
        </div>

        <!-- Tabla Preautorizaciones -->
        <div class="bg-white rounded-[24px] border border-slate-100 shadow-sm overflow-hidden mb-8">
            <div class="p-8 border-b border-slate-50 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 text-lg">Preautorizaciones</h3>
                <span class="text-sm text-slate-500" id="conteoRegistros">{{ count($preautorizaciones) }} registros</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left" id="tablaPreautorizaciones">
                    <thead class="text-slate-400 text-[11px] uppercase font-bold tracking-widest border-b border-slate-50">
                        <tr>
                            <th class="px-10 py-5">Número</th>
                            <th class="px-10 py-5">Fecha</th>
                            <th class="px-10 py-5">Paciente</th>
                            <th class="px-10 py-5">Seguro</th>
                            <th class="px-10 py-5">Servicio</th>
                            <th class="px-10 py-5">Monto</th>
                            <th class="px-10 py-5">Estado</th>
                            <th class="px-10 py-5 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-[14px] divide-y divide-slate-50" id="cuerpoTabla">
                        @forelse($preautorizaciones as $pre)
                            <tr class="hover:bg-slate-50/50 transition-all pre-row" data-estado="{{ $pre['estado'] }}"
                                data-search="{{ strtolower($pre['numero'] . ' ' . $pre['fecha'] . ' ' . $pre['paciente'] . ' ' . $pre['seguro'] . ' ' . $pre['servicio'] . ' ' . $pre['estado_label']) }}">
                                <td class="px-10 py-6 font-bold text-slate-800">{{ $pre['numero'] }}</td>
                                <td class="px-10 py-6 text-slate-500">{{ $pre['fecha'] }}</td>
                                <td class="px-10 py-6 text-slate-600 font-medium">{{ $pre['paciente'] }}</td>
                                <td class="px-10 py-6 text-slate-600">{{ $pre['seguro'] }}</td>
                                <td class="px-10 py-6 text-slate-600">{{ $pre['servicio'] }}</td>
                                <td class="px-10 py-6 font-medium text-slate-800">Bs. {{ number_format($pre['monto'], 2) }}
                                </td>
                                <td class="px-10 py-6">
                                    <span
                                        class="bg-yellow-50 text-yellow-600 border border-yellow-100 px-3 py-1.5 rounded-lg text-[12px] font-bold flex items-center gap-1.5 w-fit">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2.5" />
                                        </svg> {{ $pre['estado_label'] }}
                                    </span>
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <button
                                        onclick="abrirModalAutorizacion('{{ $pre['id'] }}', '{{ $pre['paciente'] }}', '{{ $pre['seguro'] }}', '{{ number_format($pre['monto'], 2) }}', '{{ $pre['tipo_cobertura'] }}')"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-[12px] font-bold shadow-sm transition-colors">
                                        Autorizar
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-10 py-12 text-center text-slate-400 italic">No hay
                                    preautorizaciones pendientes</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Paginación -->
            <div class="p-6 border-t border-slate-50 flex items-center justify-between">
                <div class="flex gap-2" id="paginationButtons">
                    <!-- Los botones se generarán con JavaScript -->
                </div>
                <span class="text-sm text-slate-500" id="infoFiltro">Mostrando todos los registros</span>
            </div>
        </div>

        <!-- Convenios Activos / Filtro de Seguros -->
        <div class="bg-white rounded-[24px] border border-slate-100 shadow-sm p-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-slate-800 text-lg">Convenios Registrados</h3>
                <!-- Selector de Filtro Activo/Inactivo -->
                <div class="flex bg-slate-100 p-1 rounded-xl">
                    <button onclick="filtrarSegurosPorEstado('activo')" id="btn-seg-activos"
                        class="px-6 py-2 text-xs font-bold rounded-lg transition-all bg-white text-blue-600 shadow-sm">
                        ACTIVOS
                    </button>
                    <button onclick="filtrarSegurosPorEstado('inactivo')" id="btn-seg-inactivos"
                        class="px-6 py-2 text-xs font-bold rounded-lg transition-all text-slate-400">
                        INACTIVOS
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6" id="gridSeguros">
                @forelse($seguros as $seguro)
                    <div class="seguro-card p-6 rounded-[20px] border border-slate-100 bg-white hover:shadow-md transition-all relative group"
                        data-estado="{{ $seguro->estado }}"
                        data-search="{{ strtolower($seguro->nombre_empresa . ' ' . $seguro->tipo . ' ' . $seguro->estado) }}">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="font-bold text-slate-800 text-[16px]">{{ $seguro->nombre_empresa }}</h4>
                                    <span
                                        class="text-[10px] font-bold px-2 py-0.5 rounded uppercase {{ $seguro->estado == 'activo' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $seguro->estado }}
                                    </span>
                                </div>
                                <p class="text-slate-400 text-[13px] font-medium mt-1">
                                    {{ $totalAfiliados[$seguro->id] ?? 0 }} pacientes afiliados
                                </p>
                            </div>
                            <div class="{{ $seguro->estado == 'activo' ? 'text-blue-500' : 'text-slate-300' }}">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                        stroke-width="1.5" />
                                </svg>
                            </div>
                        </div>
                        <div class="flex justify-between items-center pt-4 border-t border-slate-50">
                            <p class="text-slate-500 text-[12px]">Tipo: <span
                                    class="font-semibold">{{ $seguro->tipo }}</span></p>
                            <div class="flex gap-3">
                                <!-- Botón Cambiar Estado (Rayo) -->
                                <button onclick="toggleEstadoSeguro({{ $seguro->id }})" title="Cambiar Estado"
                                    class="p-1.5 {{ $seguro->estado == 'activo' ? 'text-emerald-500 hover:bg-emerald-50' : 'text-slate-400 hover:bg-slate-50' }} rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2"
                                            fill="{{ $seguro->estado == 'activo' ? 'currentColor' : 'none' }}" />
                                    </svg>
                                </button>
                                <button onclick="editarSeguro({{ $seguro->id }})"
                                    class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                            stroke-width="2" />
                                    </svg>
                                </button>
                                <button onclick="eliminarSeguro({{ $seguro->id }})"
                                    class="p-2 text-slate-300 hover:text-red-600 transition-colors" title="Eliminar">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                            stroke-width="2" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-8 text-slate-500">
                        No hay seguros registrados. <button onclick="abrirModalNuevoSeguro()"
                            class="text-blue-600 font-bold">Crear uno</button>
                    </div>
                @endforelse
            </div>
            <div class="mt-6 flex items-center justify-between gap-4 flex-wrap">
                <span class="text-sm text-slate-500" id="infoSeguros"></span>
                <div class="flex gap-2 flex-wrap" id="paginationSegurosButtons"></div>
            </div>
        </div>

    </div>

    <!-- Modal Nuevo Seguro -->
    <div id="modalSeguro" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-[24px] p-8 w-full max-w-lg mx-4 shadow-2xl max-h-[90vh] overflow-y-auto">
            <h3 id="modalTitulo" class="text-xl font-bold text-slate-800 mb-6">Nuevo Seguro</h3>
            <form id="formSeguro" onsubmit="guardarSeguro(event)">
                <input type="hidden" id="seguroId" name="id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Nombre de la Empresa *</label>
                        <input type="text" name="nombre_empresa" id="seguroNombre" required
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tipo *</label>
                        <select name="tipo" id="seguroTipo" required
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 outline-none">
                            <option value="">Seleccionar...</option>
                            <option value="Particular">Particular</option>
                            <option value="Convenio">Convenio</option>
                            <option value="EPS">EPS</option>
                            <option value="SOAT">SOAT</option>
                            <option value="Seguro">Seguro Médico</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tipo de Cobertura *</label>
                        <select name="tipo_cobertura" id="seguroTipoCobertura" required
                            onchange="mostrarCamposCobertura()"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 outline-none">
                            <option value="porcentaje">Porcentaje (ej: 80% cobertura, 20% copago)</option>
                            <option value="solo_consulta">Solo consulta (cubre 100% consulta)</option>
                            <option value="tope_monto">Tope de monto máximo</option>
                        </select>
                    </div>
                    <div id="camposPorcentaje" class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">% Cobertura</label>
                            <input type="number" name="cobertura_porcentaje" id="seguroCoberturaPorcentaje"
                                min="0" max="100" step="0.01"
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">% Copago Paciente</label>
                            <input type="number" name="copago_porcentaje" id="seguroCopagoPorcentaje" min="0"
                                max="100" step="0.01"
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 outline-none">
                        </div>
                    </div>
                    <div id="campoTopeMonto" class="hidden">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tope de Monto (Bs)</label>
                            <input type="number" name="tope_monto" id="seguroTopeMonto" min="0" step="0.01"
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Teléfono</label>
                        <input type="text" name="telefono" id="seguroTelefono"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Formulario/Tipo Atención</label>
                        <input type="text" name="formulario" id="seguroFormulario"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 outline-none">
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="cerrarModalSeguro()"
                        class="flex-1 px-4 py-3 border border-slate-200 text-slate-700 rounded-xl font-bold hover:bg-slate-50">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-3 bg-[#0061df] text-white rounded-xl font-bold hover:bg-blue-700">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Autorizar/Rechazar Seguro -->
    <div id="modalAutorizacion" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-[24px] p-8 w-full max-w-md mx-4 shadow-2xl">
            <h3 class="text-xl font-bold text-slate-800 mb-2">Autorización de Seguro</h3>
            <p class="text-sm text-slate-500 mb-6" id="autorizacionInfo"></p>
            <form id="formAutorizacion" onsubmit="procesarAutorizacion(event)">
                <input type="hidden" id="autorizacionCuentaId" name="cuenta_id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Decisión</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label
                                class="relative flex flex-col p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-green-400 transition-all has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
                                <input type="radio" name="estado" value="autorizado" class="sr-only peer" required>
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="font-semibold text-gray-800">Autorizar</span>
                                </div>
                            </label>
                            <label
                                class="relative flex flex-col p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-red-400 transition-all has-[:checked]:border-red-500 has-[:checked]:bg-red-50">
                                <input type="radio" name="estado" value="rechazado" class="sr-only peer">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    <span class="font-semibold text-gray-800">Rechazar</span>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div id="calculoCobertura" class="hidden bg-blue-50 rounded-xl p-4">
                        <p class="text-sm font-medium text-blue-800" id="textoCobertura"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Observaciones</label>
                        <textarea name="observaciones" rows="3"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 outline-none resize-none"
                            placeholder="Motivo de autorización o rechazo..."></textarea>
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button type="button" onclick="cerrarModalAutorizacion()"
                        class="flex-1 px-4 py-3 border border-slate-200 text-slate-700 rounded-xl font-bold hover:bg-slate-50">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-3 bg-[#0061df] text-white rounded-xl font-bold hover:bg-blue-700">
                        Confirmar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let seguroEditando = null;
        const PREAUTORIZACIONES_POR_PAGINA = 15;
        const SEGUROS_POR_PAGINA = 9;
        let paginaPreautorizaciones = 1;
        let paginaSeguros = 1;
        let filtroSegurosEstado = 'activo';

        // --- FILTRADO DE TABLA PREAUTORIZACIONES EN TIEMPO REAL ---
        function filtrarPreautorizaciones() {
            paginaPreautorizaciones = 1;
            renderPreautorizaciones();
        }
        // NUEVO: Filtrado de seguros por estado (Activo/Inactivo)
        function filtrarSegurosPorEstado(estado) {
            filtroSegurosEstado = estado;
            paginaSeguros = 1;
            const btnActivos = document.getElementById('btn-seg-activos');
            const btnInactivos = document.getElementById('btn-seg-inactivos');

            if (estado === 'activo') {
                btnActivos.className =
                    'px-6 py-2 text-xs font-bold rounded-lg transition-all bg-white text-blue-600 shadow-sm';
                btnInactivos.className = 'px-6 py-2 text-xs font-bold rounded-lg transition-all text-slate-400';
            } else {
                btnInactivos.className =
                    'px-6 py-2 text-xs font-bold rounded-lg transition-all bg-slate-800 text-white shadow-md';
                btnActivos.className = 'px-6 py-2 text-xs font-bold rounded-lg transition-all text-slate-400';
            }

            renderSeguros();
        }

        // NUEVO: Cambiar estado (Activo/Inactivo) mediante AJAX
        async function toggleEstadoSeguro(id) {
            if (!confirm('¿Desea cambiar el estado de este seguro?')) return;

            try {
                const response = await fetch(`/admin/seguros/${id}/estado`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    location.reload(); // Recargamos para actualizar vista y estadísticas
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Error al actualizar estado.');
            }
        }

        // Al cargar, mostramos solo activos por defecto
        document.addEventListener('DOMContentLoaded', () => {
            filtrarSegurosPorEstado('activo');
            renderPreautorizaciones();
        });

        function renderPreautorizaciones() {
            const filtro = document.getElementById('buscarPreautorizacion').value.toLowerCase().trim();
            const filas = Array.from(document.querySelectorAll('#tablaPreautorizaciones tbody tr.pre-row'));
            const visibles = filas.filter(fila => (fila.dataset.search || fila.textContent.toLowerCase()).includes(filtro));
            const total = visibles.length;
            const totalPaginas = Math.max(1, Math.ceil(total / PREAUTORIZACIONES_POR_PAGINA));
            paginaPreautorizaciones = Math.min(paginaPreautorizaciones, totalPaginas);
            const inicio = (paginaPreautorizaciones - 1) * PREAUTORIZACIONES_POR_PAGINA;
            const fin = inicio + PREAUTORIZACIONES_POR_PAGINA;

            filas.forEach(fila => fila.style.display = 'none');
            visibles.slice(inicio, fin).forEach(fila => fila.style.display = '');

            const conteo = document.getElementById('conteoRegistros');
            if (conteo) conteo.textContent = `${total} registros`;
            const info = document.getElementById('infoFiltro');
            if (info) {
                info.textContent = total
                    ? `Mostrando ${inicio + 1}-${Math.min(fin, total)} de ${total} registros`
                    : 'No hay registros';
            }

            renderPaginationButtons('paginationButtons', totalPaginas, paginaPreautorizaciones, (pagina) => {
                paginaPreautorizaciones = pagina;
                renderPreautorizaciones();
            });
        }

        function renderSeguros() {
            const cards = Array.from(document.querySelectorAll('.seguro-card'));
            const visibles = cards.filter(card => card.dataset.estado === filtroSegurosEstado);
            const total = visibles.length;
            const totalPaginas = Math.max(1, Math.ceil(total / SEGUROS_POR_PAGINA));
            paginaSeguros = Math.min(paginaSeguros, totalPaginas);
            const inicio = (paginaSeguros - 1) * SEGUROS_POR_PAGINA;
            const fin = inicio + SEGUROS_POR_PAGINA;

            cards.forEach(card => card.style.display = 'none');
            visibles.slice(inicio, fin).forEach(card => card.style.display = 'block');

            const info = document.getElementById('infoSeguros');
            if (info) {
                info.textContent = total
                    ? `Mostrando ${inicio + 1}-${Math.min(fin, total)} de ${total} convenios ${filtroSegurosEstado}`
                    : 'No hay convenios para mostrar';
            }

            renderPaginationButtons('paginationSegurosButtons', totalPaginas, paginaSeguros, (pagina) => {
                paginaSeguros = pagina;
                renderSeguros();
            });
        }

        function renderPaginationButtons(containerId, totalPaginas, paginaActual, onClick) {
            const container = document.getElementById(containerId);
            if (!container) return;
            container.innerHTML = '';
            if (totalPaginas <= 1) return;

            const addButton = (label, page, disabled = false, active = false) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = label;
                button.className = active
                    ? 'px-3 py-2 rounded-lg bg-[#0061df] text-white text-sm font-bold'
                    : 'px-3 py-2 rounded-lg border border-slate-200 text-slate-600 text-sm font-bold hover:bg-slate-50';
                button.disabled = disabled;
                if (!disabled) button.addEventListener('click', () => onClick(page));
                container.appendChild(button);
            };

            addButton('‹', Math.max(1, paginaActual - 1), paginaActual === 1);
            for (let i = 1; i <= totalPaginas; i++) {
                addButton(String(i), i, false, i === paginaActual);
            }
            addButton('›', Math.min(totalPaginas, paginaActual + 1), paginaActual === totalPaginas);
        }

        function abrirModalNuevoSeguro() {
            seguroEditando = null;
            document.getElementById('modalTitulo').textContent = 'Nuevo Seguro';
            document.getElementById('formSeguro').reset();
            document.getElementById('seguroId').value = '';
            document.getElementById('modalSeguro').classList.remove('hidden');
            document.getElementById('modalSeguro').classList.add('flex');
        }

        function cerrarModalSeguro() {
            document.getElementById('modalSeguro').classList.add('hidden');
            document.getElementById('modalSeguro').classList.remove('flex');
        }

        async function guardarSeguro(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);

            const id = document.getElementById('seguroId').value;
            const url = id ? `/admin/seguros/${id}` : '/admin/seguros';
            const method = id ? 'PUT' : 'POST';

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Error al guardar.');
            }
        }

        // --- ELIMINAR SEGURO AJAX ---
        async function eliminarSeguro(id) {
            if (!confirm("¿Está seguro de eliminar este seguro permanentemente? Esta acción no se puede deshacer."))
                return;

            try {
                const response = await fetch(`/admin/seguros/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const res = await response.json();
                if (res.success) {
                    location.reload();
                } else {
                    alert(res.message || "No se puede eliminar el seguro (posiblemente tiene afiliados).");
                }
            } catch (e) {
                alert("Error al intentar eliminar el seguro.");
            }
        }

        function mostrarCamposCobertura() {
            const tipo = document.getElementById('seguroTipoCobertura').value;
            document.getElementById('camposPorcentaje').classList.toggle('hidden', tipo !== 'porcentaje');
            document.getElementById('campoTopeMonto').classList.toggle('hidden', tipo !== 'tope_monto');
        }

        async function editarSeguro(id) {
            try {
                const response = await fetch(`/admin/api/seguros/${id}`);
                const result = await response.json();

                if (result.success) {
                    seguroEditando = id;
                    document.getElementById('modalTitulo').textContent = 'Editar Seguro';
                    document.getElementById('seguroId').value = id;
                    document.getElementById('seguroNombre').value = result.seguro.nombre_empresa;
                    document.getElementById('seguroTipo').value = result.seguro.tipo;
                    document.getElementById('seguroTipoCobertura').value = result.seguro.tipo_cobertura || 'porcentaje';
                    document.getElementById('seguroCoberturaPorcentaje').value = result.seguro.cobertura_porcentaje ||
                        '';
                    document.getElementById('seguroCopagoPorcentaje').value = result.seguro.copago_porcentaje || '';
                    document.getElementById('seguroTopeMonto').value = result.seguro.tope_monto || '';
                    document.getElementById('seguroTelefono').value = result.seguro.telefono || '';
                    document.getElementById('seguroFormulario').value = result.seguro.formulario || '';

                    mostrarCamposCobertura();

                    document.getElementById('modalSeguro').classList.remove('hidden');
                    document.getElementById('modalSeguro').classList.add('flex');
                }
            } catch (error) {
                alert('Error al cargar seguro.');
            }
        }

        function abrirModalAutorizacion(cuentaId, paciente, seguro, monto, tipoCobertura) {
            document.getElementById('autorizacionCuentaId').value = cuentaId;
            document.getElementById('autorizacionInfo').textContent = `${paciente} - ${seguro} - Bs. ${monto}`;
            document.getElementById('formAutorizacion').reset();
            document.getElementById('calculoCobertura').classList.add('hidden');

            document.getElementById('modalAutorizacion').classList.remove('hidden');
            document.getElementById('modalAutorizacion').classList.add('flex');
        }

        function cerrarModalAutorizacion() {
            document.getElementById('modalAutorizacion').classList.add('hidden');
            document.getElementById('modalAutorizacion').classList.remove('flex');
        }

        async function procesarAutorizacion(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);
            const cuentaId = document.getElementById('autorizacionCuentaId').value;

            try {
                const response = await fetch(`/admin/api/preautorizaciones/${cuentaId}/estado`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    location.reload();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Error al procesar autorización.');
            }
        }
    </script>
@endsection
