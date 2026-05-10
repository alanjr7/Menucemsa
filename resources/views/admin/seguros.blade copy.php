@extends('layouts.app')

@section('content')
<div class="w-full p-4 md:p-8 bg-[#f8fafc] min-h-screen font-sans antialiased">

    <!-- Header con Filtro de Estado -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-blue-600 rounded-xl text-white shadow-lg shadow-blue-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" stroke-width="2.5"/></svg>
                </div>
                <h1 class="text-2xl font-black text-slate-800 tracking-tight">Seguros y Convenios</h1>
            </div>
            <p class="text-slate-500 text-sm mt-1">Gestión de convenios institucionales y preautorizaciones</p>
        </div>

        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <!-- Filtro Activos/Inactivos -->
            <div class="flex bg-white p-1 rounded-xl border border-slate-200 shadow-sm">
                <button onclick="filtrarPorEstado('activo')" id="btn-activos" class="px-4 py-2 text-xs font-bold rounded-lg transition-all bg-blue-50 text-blue-600">
                    ACTIVOS
                </button>
                <button onclick="filtrarPorEstado('inactivo')" id="btn-inactivos" class="px-4 py-2 text-xs font-bold rounded-lg transition-all text-slate-400">
                    INACTIVOS
                </button>
            </div>

            <button onclick="abrirModalNuevoSeguro()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl flex items-center gap-2 text-sm font-bold shadow-md transition-all">
                <span class="text-lg">+</span> Nuevo Seguro
            </button>
        </div>
    </div>

    <!-- Stats Rápidas -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        @php
            $cards = [
                ['label' => 'Pendientes', 'val' => $stats['pendientes'] ?? 0, 'color' => 'orange'],
                ['label' => 'Rechazadas', 'val' => $stats['rechazadas'] ?? 0, 'color' => 'red'],
                ['label' => 'Aprobadas', 'val' => $stats['aprobadas'] ?? 0, 'color' => 'emerald'],
                ['label' => 'Activos', 'val' => $seguros->where('estado', 'activo')->count(), 'color' => 'blue']
            ];
        @endphp
        @foreach($cards as $card)
            <div class="bg-white p-4 md:p-6 rounded-2xl shadow-sm border border-slate-100 transition-transform hover:scale-[1.02]">
                <p class="text-slate-400 text-[11px] font-bold uppercase tracking-wider mb-1">{{ $card['label'] }}</p>
                <p class="text-{{ $card['color'] }}-600 text-2xl md:text-3xl font-black">{{ $card['val'] }}</p>
            </div>
        @endforeach
    </div>

    <!-- Tabla Preautorizaciones -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden mb-8">
        <div class="p-6 border-b border-slate-50 flex flex-col md:flex-row justify-between items-center gap-4 bg-slate-50/30">
            <h3 class="font-bold text-slate-800">Preautorizaciones Pendientes</h3>
            <div class="relative w-full md:w-72">
                <input type="text" id="buscarPre" onkeyup="filtrarTablaPre()" placeholder="Filtrar paciente..." class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-100 outline-none">
                <svg class="w-4 h-4 absolute left-3.5 top-2.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5"/></svg>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left" id="tablaPreautorizaciones">
                <thead class="text-slate-400 text-[10px] uppercase font-bold tracking-widest bg-slate-50/50">
                    <tr>
                        <th class="px-6 py-4">Fecha</th>
                        <th class="px-6 py-4">Paciente</th>
                        <th class="px-6 py-4">Seguro</th>
                        <th class="px-6 py-4 text-right">Monto</th>
                        <th class="px-6 py-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-slate-50">
                    @forelse($preautorizaciones as $pre)
                    <tr class="hover:bg-blue-50/30 transition-colors">
                        <td class="px-6 py-4 text-slate-500 text-xs">{{ $pre['fecha'] }}</td>
                        <td class="px-6 py-4 font-bold text-slate-800">{{ $pre['paciente'] }}</td>
                        <td class="px-6 py-4 text-slate-600 font-medium">{{ $pre['seguro'] }}</td>
                        <td class="px-6 py-4 text-right font-black text-slate-900">Bs. {{ number_format($pre['monto'], 2) }}</td>
                        <td class="px-6 py-4 text-center">
                            <button onclick="abrirModalAutorizacion('{{ $pre['id'] }}', '{{ $pre['paciente'] }}', '{{ $pre['seguro'] }}', '{{ $pre['monto'] }}')"
                                    class="bg-blue-600 text-white px-4 py-1.5 rounded-lg text-[10px] font-black hover:bg-blue-700 transition-all">
                                PROCESAR
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-10 text-center text-slate-400 italic">No hay solicitudes pendientes</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Grid de Seguros -->
    <h3 class="font-bold text-slate-800 text-lg mb-4">Convenios Registrados</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" id="gridSeguros">
        @foreach($seguros as $seguro)
        <div class="seguro-card group bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl transition-all"
             data-estado="{{ $seguro->estado }}" data-nombre="{{ strtolower($seguro->nombre_empresa) }}">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-blue-600 border border-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" stroke-width="2"/></svg>
                </div>
                <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase {{ $seguro->estado == 'activo' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                    {{ $seguro->estado }}
                </span>
            </div>

            <h4 class="font-black text-slate-800 text-lg leading-tight mb-1">{{ $seguro->nombre_empresa }}</h4>
            <div class="text-slate-400 text-[10px] font-bold mb-6 uppercase tracking-widest">{{ $seguro->tipo }}</div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                <div class="flex gap-2">
                    <!-- BOTÓN EDITAR -->
                    <button onclick="editarSeguro({{ json_encode($seguro) }})" class="p-2 text-slate-400 hover:text-blue-600 transition-colors" title="Editar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2"/></svg>
                    </button>
                    <!-- BOTÓN TOGGLE ESTADO -->
                    <button onclick="toggleEstadoSeguro({{ $seguro->id }}, '{{ $seguro->estado }}')"
                            class="p-2 {{ $seguro->estado == 'activo' ? 'text-emerald-500 hover:text-red-500' : 'text-slate-300 hover:text-emerald-500' }} transition-colors" title="Activar/Desactivar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2" fill="{{ $seguro->estado == 'activo' ? 'currentColor' : 'none' }}"/></svg>
                    </button>
                    <!-- BOTÓN ELIMINAR -->
                    <button onclick="eliminarSeguro({{ $seguro->id }})" class="p-2 text-slate-300 hover:text-red-600 transition-colors" title="Eliminar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2"/></svg>
                    </button>
                </div>
                <div class="text-[10px] font-black text-slate-300 uppercase">{{ $totalAfiliados[$seguro->id] ?? 0 }} AFILIADOS</div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Modal Nuevo/Editar Seguro -->
<div id="modalSeguro" class="fixed inset-0 bg-slate-900/60 hidden backdrop-blur-sm items-center justify-center z-50 p-4">
    <div class="bg-white rounded-[32px] p-8 w-full max-w-lg shadow-2xl overflow-y-auto max-h-[90vh]">
        <div class="flex justify-between items-center mb-6">
            <h3 id="modalTitulo" class="text-xl font-black text-slate-800">Gestionar Seguro</h3>
            <button onclick="cerrarModalSeguro()" class="text-slate-400 hover:text-slate-600">✕</button>
        </div>

        <form id="formSeguro" onsubmit="guardarSeguro(event)" class="space-y-5">
            @csrf
            <input type="hidden" id="seguroId" name="id">

            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-1.5 ml-1 tracking-widest">Nombre Comercial</label>
                <input type="text" name="nombre_empresa" id="seguroNombre" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold outline-none focus:ring-2 focus:ring-blue-100">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1.5 ml-1 tracking-widest">Tipo</label>
                    <select name="tipo" id="seguroTipo" required class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold outline-none">
                        <option value="Particular">Particular</option>
                        <option value="Convenio">Convenio</option>
                        <option value="EPS">EPS</option>
                        <option value="Seguro">Seguro Médico</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase mb-1.5 ml-1 tracking-widest">Modalidad</label>
                    <select name="tipo_cobertura" id="seguroTipoCobertura" onchange="mostrarCamposCobertura()" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold outline-none">
                        <option value="porcentaje">Porcentaje %</option>
                        <option value="tope_monto">Monto Fijo</option>
                    </select>
                </div>
            </div>

            <div id="camposPorcentaje" class="grid grid-cols-2 gap-4 bg-blue-50/50 p-4 rounded-2xl">
                <div>
                    <label class="block text-[10px] font-black text-blue-400 uppercase mb-1.5 tracking-widest">Cobertura %</label>
                    <input type="number" name="cobertura_porcentaje" id="seguroCoberturaPorcentaje" min="0" max="100" class="w-full px-4 py-2 bg-white border border-blue-100 rounded-xl text-sm font-black">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-blue-400 uppercase mb-1.5 tracking-widest">Copago %</label>
                    <input type="number" name="copago_porcentaje" id="seguroCopagoPorcentaje" min="0" max="100" class="w-full px-4 py-2 bg-white border border-blue-100 rounded-xl text-sm font-black">
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="cerrarModalSeguro()" class="flex-1 py-3 text-slate-400 font-bold text-xs uppercase tracking-widest">Cancelar</button>
                <button type="submit" class="flex-1 py-3 bg-blue-600 text-white rounded-2xl font-black text-xs uppercase shadow-lg shadow-blue-200 tracking-widest">Guardar Seguro</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Autorización -->
<div id="modalAutorizacion" class="fixed inset-0 bg-slate-900/60 hidden backdrop-blur-sm items-center justify-center z-50 p-4">
    <div class="bg-white rounded-[32px] p-8 w-full max-w-md shadow-2xl">
        <h3 class="text-xl font-black text-slate-800 mb-2">Procesar Autorización</h3>
        <p id="preInfo" class="text-xs text-slate-500 mb-6"></p>
        <form id="formAutorizar" onsubmit="procesarAutorizacion(event)" class="space-y-4">
            <input type="hidden" id="preCuentaId">
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Estado de la Solicitud</label>
                <select id="preEstado" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold outline-none">
                    <option value="autorizado">Autorizar / Aprobar</option>
                    <option value="rechazado">Rechazar Solicitud</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest">Observaciones</label>
                <textarea id="preObservaciones" rows="3" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm outline-none" placeholder="Motivo del rechazo o notas..."></textarea>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="cerrarModalAutorizacion()" class="flex-1 py-3 text-slate-400 font-bold text-xs uppercase">Cerrar</button>
                <button type="submit" class="flex-1 py-3 bg-slate-800 text-white rounded-2xl font-black text-xs uppercase shadow-xl">Confirmar</button>
            </div>
        </form>
    </div>
</div>

<script>
// --- FILTRADO POR ESTADO ---
function filtrarPorEstado(estado) {
    const btnActivos = document.getElementById('btn-activos');
    const btnInactivos = document.getElementById('btn-inactivos');

    if (estado === 'activo') {
        btnActivos.className = 'px-4 py-2 text-xs font-bold rounded-lg transition-all bg-blue-50 text-blue-600';
        btnInactivos.className = 'px-4 py-2 text-xs font-bold rounded-lg transition-all text-slate-400';
    } else {
        btnInactivos.className = 'px-4 py-2 text-xs font-bold rounded-lg transition-all bg-slate-800 text-white shadow-md';
        btnActivos.className = 'px-4 py-2 text-xs font-bold rounded-lg transition-all text-slate-400';
    }

    const cards = document.querySelectorAll('.seguro-card');
    cards.forEach(card => {
        card.style.display = card.getAttribute('data-estado') === estado ? 'block' : 'none';
    });
}

// --- CAMBIO DE ESTADO AJAX ---
async function toggleEstadoSeguro(id, estadoActual) {
    const nuevoEstado = estadoActual === 'activo' ? 'inactivo' : 'activo';
    if(!confirm(`¿Desea marcar este seguro como ${nuevoEstado.toUpperCase()}?`)) return;

    try {
        const response = await fetch(`/admin/seguros/${id}/estado`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ estado: nuevoEstado })
        });

        const res = await response.json();
        if(res.success) {
            location.reload();
        } else {
            alert(res.message || "Error al actualizar.");
        }
    } catch(e) {
        alert("Error de conexión al servidor.");
    }
}

// --- ELIMINAR SEGURO AJAX ---
async function eliminarSeguro(id) {
    if(!confirm("¿Está seguro de eliminar este seguro permanentemente? Esta acción no se puede deshacer.")) return;

    try {
        const response = await fetch(`/admin/seguros/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const res = await response.json();
        if(res.success) {
            location.reload();
        } else {
            alert(res.message || "No se puede eliminar el seguro (posiblemente tiene afiliados).");
        }
    } catch(e) {
        alert("Error al intentar eliminar el seguro.");
    }
}

// --- GESTIÓN DE MODALES SEGURO ---
function abrirModalNuevoSeguro() {
    document.getElementById('modalTitulo').innerText = 'Nuevo Seguro';
    document.getElementById('formSeguro').reset();
    document.getElementById('seguroId').value = '';
    document.getElementById('modalSeguro').classList.remove('hidden');
    document.getElementById('modalSeguro').classList.add('flex');
}

function editarSeguro(seguro) {
    document.getElementById('modalTitulo').innerText = 'Editar Seguro';
    document.getElementById('seguroId').value = seguro.id;
    document.getElementById('seguroNombre').value = seguro.nombre_empresa;
    document.getElementById('seguroTipo').value = seguro.tipo;
    document.getElementById('seguroTipoCobertura').value = seguro.tipo_cobertura || 'porcentaje';
    document.getElementById('seguroCoberturaPorcentaje').value = seguro.cobertura_porcentaje;
    document.getElementById('seguroCopagoPorcentaje').value = seguro.copago_porcentaje;
    mostrarCamposCobertura();
    document.getElementById('modalSeguro').classList.remove('hidden');
    document.getElementById('modalSeguro').classList.add('flex');
}

function cerrarModalSeguro() {
    document.getElementById('modalSeguro').classList.add('hidden');
    document.getElementById('modalSeguro').classList.remove('flex');
}

function mostrarCamposCobertura() {
    const tipo = document.getElementById('seguroTipoCobertura').value;
    document.getElementById('camposPorcentaje').classList.toggle('hidden', tipo !== 'porcentaje');
}

async function guardarSeguro(e) {
    e.preventDefault();
    const id = document.getElementById('seguroId').value;
    const url = id ? `/admin/seguros/${id}` : '/admin/seguros';
    const method = id ? 'PUT' : 'POST';
    const data = Object.fromEntries(new FormData(e.target));

    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        const res = await response.json();
        if(res.success) location.reload();
        else alert(res.message);
    } catch(e) {
        alert("Error de red al guardar.");
    }
}

// --- GESTIÓN PREAUTORIZACIONES ---
function abrirModalAutorizacion(id, paciente, seguro, monto) {
    document.getElementById('preCuentaId').value = id;
    document.getElementById('preInfo').innerText = `${paciente} | ${seguro} | Bs. ${parseFloat(monto).toFixed(2)}`;
    document.getElementById('modalAutorizacion').classList.remove('hidden');
    document.getElementById('modalAutorizacion').classList.add('flex');
}

function cerrarModalAutorizacion() {
    document.getElementById('modalAutorizacion').classList.add('hidden');
    document.getElementById('modalAutorizacion').classList.remove('flex');
}

async function procesarAutorizacion(e) {
    e.preventDefault();
    const id = document.getElementById('preCuentaId').value;
    const data = {
        estado: document.getElementById('preEstado').value,
        observaciones: document.getElementById('preObservaciones').value
    };

    try {
        const response = await fetch(`/admin/api/preautorizaciones/${id}/estado`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        const res = await response.json();
        if(res.success) {
            alert(res.message);
            location.reload();
        }
    } catch(e) { alert("Error al procesar la autorización."); }
}

// --- FILTROS DE TABLA PREAUTORIZACIONES ---
function filtrarTablaPre() {
    const input = document.getElementById("buscarPre").value.toLowerCase();
    const rows = document.querySelectorAll("#tablaPreautorizaciones tbody tr");
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(input) ? "" : "none";
    });
}

// Inicializar vista mostrando activos
document.addEventListener('DOMContentLoaded', () => filtrarPorEstado('activo'));
</script>

<style>
    .seguro-card { animation: slideUp 0.3s ease-out; }
    @keyframes slideUp { from { opacity:0; transform: translateY(10px); } to { opacity:1; transform: translateY(0); } }
</style>
@endsection
