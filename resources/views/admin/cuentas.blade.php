@extends('layouts.app')

@section('content')
<div class="w-full p-8 bg-[#f8fafc] min-h-screen font-sans antialiased">

    <!-- Header -->
    <div class="flex justify-between items-start mb-8">
        <div>
            <h1 class="text-[26px] font-black text-slate-800 tracking-tight">Cuentas por Cobrar</h1>
            <p class="text-slate-500 text-[15px] font-medium mt-1">Seguimiento de cobros, emergencias y morosidad</p>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 flex flex-col justify-center items-center relative overflow-hidden">
            <p class="text-slate-400 text-[13px] font-medium mb-1">Total por Cobrar</p>
            <div class="flex items-baseline gap-2">
                <p class="text-slate-800 text-[28px] font-black tracking-tighter">Bs. {{ number_format($stats['total_cobrar'] ?? 0, 2) }}</p>
                <span class="text-blue-500 text-2xl font-bold">$</span>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 flex flex-col justify-center items-center">
            <p class="text-slate-400 text-[13px] font-medium mb-1">Vencidas (+30 días)</p>
            <div class="flex items-center gap-3">
                <p class="text-red-600 text-[28px] font-black tracking-tighter">Bs. {{ number_format($stats['vencidas'] ?? 0, 2) }}</p>
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2"/></svg>
            </div>
        </div>
        <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 text-center">
            <p class="text-slate-400 text-[13px] font-medium mb-1">Cuentas Activas</p>
            <p class="text-slate-800 text-[32px] font-black tracking-tighter">{{ $stats['cuentas_activas'] ?? 0 }}</p>
        </div>
        <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 text-center">
            <p class="text-slate-400 text-[13px] font-medium mb-1">Índice Morosidad</p>
            <p class="text-orange-500 text-[32px] font-black tracking-tighter">{{ $stats['morosidad'] ?? 0 }}%</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white p-5 rounded-[20px] border border-slate-100 shadow-sm mb-6 flex items-center gap-4">
        <div class="relative flex-1">
            <input type="text" id="buscarCuenta" placeholder="Buscar por número, paciente o CI..." 
                   class="w-full pl-12 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-100 transition-all">
            <svg class="w-5 h-5 absolute left-4 top-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2"/></svg>
        </div>
        <select id="filtroTipo" class="bg-white border border-slate-200 text-slate-600 px-4 py-2.5 rounded-xl text-sm font-bold outline-none">
            <option value="">Todas</option>
            <option value="emergencia">Emergencias</option>
            <option value="consulta">Consultas</option>
            <option value="hospitalizacion">Hospitalización</option>
        </select>
    </div>

    <!-- Cuentas de Emergencias -->
    @if(count($cuentasEmergencias) > 0)
    <div class="bg-white rounded-[24px] border border-slate-100 shadow-sm overflow-hidden mb-8">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-red-50 rounded-lg text-red-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2"/></svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">Cuentas de Emergencias</h3>
                    <p class="text-sm text-slate-500">Post-pago: acumulan cargos hasta el alta del paciente</p>
                </div>
            </div>
            <span class="text-sm text-slate-500">{{ count($cuentasEmergencias) }} cuentas activas</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left" id="tablaEmergencias">
                <thead class="text-slate-400 text-[11px] uppercase font-bold border-b border-slate-50">
                    <tr>
                        <th class="px-8 py-5">Código Emergencia</th>
                        <th class="px-8 py-5">Paciente</th>
                        <th class="px-8 py-5">Ubicación</th>
                        <th class="px-8 py-5">Total Acumulado</th>
                        <th class="px-8 py-5">Saldo</th>
                        <th class="px-8 py-5">Estado</th>
                        <th class="px-8 py-5 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-[14px] divide-y divide-slate-50">
                    @foreach($cuentasEmergencias as $cuenta)
                    <tr class="hover:bg-slate-50/50 transition-all" data-tipo="emergencia">
                        <td class="px-8 py-6 font-bold text-slate-800">
                            {{ $cuenta['emergency_code'] ?? $cuenta['id'] }}
                            @if($cuenta['es_temporal'])
                                <span class="ml-2 px-2 py-0.5 bg-yellow-100 text-yellow-700 text-xs rounded-full">Temporal</span>
                            @endif
                        </td>
                        <td class="px-8 py-6 text-slate-600">{{ $cuenta['paciente'] }}</td>
                        <td class="px-8 py-6">
                            <span class="px-3 py-1 rounded-lg text-xs font-bold 
                                {{ $cuenta['ubicacion_actual'] === 'emergencia' ? 'bg-red-100 text-red-600' : '' }}
                                {{ $cuenta['ubicacion_actual'] === 'cirugia' ? 'bg-purple-100 text-purple-600' : '' }}
                                {{ $cuenta['ubicacion_actual'] === 'uti' ? 'bg-orange-100 text-orange-600' : '' }}
                                {{ $cuenta['ubicacion_actual'] === 'hospitalizacion' ? 'bg-blue-100 text-blue-600' : '' }}">
                                {{ ucfirst($cuenta['ubicacion_actual'] ?? 'Emergencia') }}
                            </span>
                        </td>
                        <td class="px-8 py-6 font-medium">Bs. {{ number_format($cuenta['total'], 2) }}</td>
                        <td class="px-8 py-6 font-bold text-slate-800">Bs. {{ number_format($cuenta['saldo'], 2) }}</td>
                        <td class="px-8 py-6">
                            @if($cuenta['estado'] === 'pendiente')
                                <span class="bg-red-50 text-red-600 px-3 py-1 rounded-lg text-xs font-bold border border-red-100">Pendiente</span>
                            @elseif($cuenta['estado'] === 'parcial')
                                <span class="bg-orange-50 text-orange-600 px-3 py-1 rounded-lg text-xs font-bold border border-orange-100">Parcial</span>
                            @else
                                <span class="bg-green-50 text-green-600 px-3 py-1 rounded-lg text-xs font-bold border border-green-100">Pagado</span>
                            @endif
                        </td>
                        <td class="px-8 py-6 text-right space-x-2">
                            <button onclick="verDetalleCuenta('{{ $cuenta['id'] }}')" class="border border-slate-200 px-4 py-2 rounded-xl text-xs font-bold hover:bg-slate-50">Ver Detalle</button>
                            <button onclick="registrarPago('{{ $cuenta['id'] }}', {{ $cuenta['saldo'] }})" class="bg-[#0061df] text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-blue-700">Registrar Pago</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Todas las Cuentas -->
    <div class="bg-white rounded-[24px] border border-slate-100 shadow-sm overflow-hidden mb-8">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-800 text-lg">Todas las Cuentas por Cobrar</h3>
            <span class="text-sm text-slate-500">{{ count($cuentas) }} registros</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left" id="tablaCuentas">
                <thead class="text-slate-400 text-[11px] uppercase font-bold border-b border-slate-50">
                    <tr>
                        <th class="px-8 py-5">Número</th>
                        <th class="px-8 py-5">Cliente</th>
                        <th class="px-8 py-5">CI</th>
                        <th class="px-8 py-5">Fecha</th>
                        <th class="px-8 py-5">Tipo</th>
                        <th class="px-8 py-5">Monto Total</th>
                        <th class="px-8 py-5">Saldo</th>
                        <th class="px-8 py-5">Estado</th>
                        <th class="px-8 py-5 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-[14px] divide-y divide-slate-50">
                    @forelse($cuentas as $cuenta)
                    <tr class="hover:bg-slate-50/50 transition-all" data-tipo="{{ $cuenta['es_emergencia'] ? 'emergencia' : $cuenta['tipo_atencion'] }}">
                        <td class="px-8 py-6 font-bold text-slate-800">{{ $cuenta['id'] }}</td>
                        <td class="px-8 py-6 text-slate-600">{{ $cuenta['paciente_nombre'] }}</td>
                        <td class="px-8 py-6 text-slate-500">{{ $cuenta['paciente_ci'] }}</td>
                        <td class="px-8 py-6 text-slate-500">{{ $cuenta['created_at']->format('Y-m-d') }}</td>
                        <td class="px-8 py-6">
                            <span class="px-3 py-1 rounded-lg text-xs font-bold
                                {{ $cuenta['es_emergencia'] ? 'bg-red-50 text-red-600' : 'bg-blue-50 text-blue-600' }}">
                                {{ $cuenta['tipo_atencion'] }}
                            </span>
                        </td>
                        <td class="px-8 py-6 font-medium">Bs. {{ number_format($cuenta['total_calculado'], 2) }}</td>
                        <td class="px-8 py-6 font-bold text-slate-800">Bs. {{ number_format($cuenta['saldo_pendiente'], 2) }}</td>
                        <td class="px-8 py-6">
                            <span class="bg-{{ $cuenta['estado_color'] }}-50 text-{{ $cuenta['estado_color'] }}-600 px-3 py-1 rounded-lg text-xs font-bold border border-{{ $cuenta['estado_color'] }}-100">
                                {{ $cuenta['estado_label'] }}
                            </span>
                        </td>
                        <td class="px-8 py-6 text-right space-x-2">
                            <button onclick="verDetalleCuenta('{{ $cuenta['id'] }}')" class="border border-slate-200 px-4 py-2 rounded-xl text-xs font-bold hover:bg-slate-50">Ver</button>
                            @if($cuenta['saldo_pendiente'] > 0)
                            <button onclick="registrarPago('{{ $cuenta['id'] }}', {{ $cuenta['saldo_pendiente'] }})" class="bg-[#0061df] text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-blue-700">Pagar</button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-8 py-8 text-center text-slate-500">
                            No hay cuentas por cobrar registradas
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Alertas de Morosidad -->
    <div class="bg-white rounded-[24px] border border-slate-100 shadow-sm p-8">
        <div class="flex items-center gap-3 mb-6">
            <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2"/></svg>
            <h3 class="font-bold text-slate-800 text-lg">Alertas de Morosidad</h3>
        </div>
        <div class="space-y-4" id="alertasMorosidad">
            <div class="bg-yellow-50 border border-yellow-100 p-5 rounded-2xl">
                <p class="text-yellow-800 font-medium text-sm">Las cuentas de emergencia se cobran al finalizar la atención médica (post-pago).</p>
            </div>
        </div>
    </div>

</div>

<!-- Modal Registrar Pago -->
<div id="modalPago" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-[24px] p-8 w-full max-w-md mx-4 shadow-2xl">
        <h3 class="text-xl font-bold text-slate-800 mb-6">Registrar Pago</h3>
        <form id="formPago" onsubmit="guardarPago(event)">
            <input type="hidden" id="pagoCuentaId" name="cuenta_id">
            <div class="space-y-4">
                <div class="bg-slate-50 p-4 rounded-xl">
                    <p class="text-sm text-slate-500">Saldo Pendiente</p>
                    <p class="text-2xl font-black text-slate-800" id="saldoPendiente">Bs. 0.00</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Monto a Pagar *</label>
                    <input type="number" name="monto" id="pagoMonto" step="0.01" min="0.01" required
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Método de Pago *</label>
                    <select name="metodo_pago" id="pagoMetodo" required
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 outline-none">
                        <option value="">Seleccionar...</option>
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="tarjeta">Tarjeta</option>
                        <option value="qr">QR</option>
                        <option value="cheque">Cheque</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Referencia (opcional)</label>
                    <input type="text" name="referencia" id="pagoReferencia"
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 outline-none">
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button type="button" onclick="cerrarModalPago()" class="flex-1 px-4 py-3 border border-slate-200 text-slate-700 rounded-xl font-bold hover:bg-slate-50">
                    Cancelar
                </button>
                <button type="submit" class="flex-1 px-4 py-3 bg-[#0061df] text-white rounded-xl font-bold hover:bg-blue-700">
                    Registrar Pago
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Detalle Cuenta -->
<div id="modalDetalle" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-[24px] p-8 w-full max-w-2xl mx-4 shadow-2xl max-h-[80vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-slate-800">Detalle de Cuenta</h3>
            <button onclick="cerrarModalDetalle()" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2"/></svg>
            </button>
        </div>
        <div id="detalleContenido" class="space-y-4">
            <!-- Se llena dinámicamente -->
        </div>
    </div>
</div>

<script>
let cuentaPagoActual = null;
let saldoActual = 0;

function registrarPago(cuentaId, saldo) {
    cuentaPagoActual = cuentaId;
    saldoActual = saldo;
    document.getElementById('pagoCuentaId').value = cuentaId;
    document.getElementById('saldoPendiente').textContent = 'Bs. ' + saldo.toFixed(2);
    document.getElementById('pagoMonto').max = saldo;
    document.getElementById('pagoMonto').value = saldo;
    document.getElementById('modalPago').classList.remove('hidden');
    document.getElementById('modalPago').classList.add('flex');
}

function cerrarModalPago() {
    document.getElementById('modalPago').classList.add('hidden');
    document.getElementById('modalPago').classList.remove('flex');
    cuentaPagoActual = null;
}

async function guardarPago(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    
    if (parseFloat(data.monto) > saldoActual) {
        alert('El monto no puede exceder el saldo pendiente');
        return;
    }
    
    try {
        const response = await fetch(`/admin/api/cuentas/${cuentaPagoActual}/pago`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"').content
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
        alert('Error al registrar pago: ' + error.message);
    }
}

async function verDetalleCuenta(id) {
    try {
        const response = await fetch(`/admin/api/cuentas/${id}`);
        const result = await response.json();
        
        if (result.success) {
            const cuenta = result.cuenta;
            let html = `
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-slate-50 p-4 rounded-xl">
                        <p class="text-sm text-slate-500">Paciente</p>
                        <p class="font-bold text-slate-800">${cuenta.paciente.nombre}</p>
                        <p class="text-sm text-slate-500">CI: ${cuenta.paciente.ci}</p>
                    </div>
                    <div class="bg-slate-50 p-4 rounded-xl">
                        <p class="text-sm text-slate-500">Estado</p>
                        <span class="inline-block px-3 py-1 rounded-lg text-xs font-bold mt-1 bg-${cuenta.estado === 'pagado' ? 'green' : (cuenta.estado === 'parcial' ? 'orange' : 'red')}-50 text-${cuenta.estado === 'pagado' ? 'green' : (cuenta.estado === 'parcial' ? 'orange' : 'red')}-600">
                            ${cuenta.estado.toUpperCase()}
                        </span>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="bg-blue-50 p-4 rounded-xl text-center">
                        <p class="text-sm text-slate-500">Total</p>
                        <p class="text-xl font-black text-blue-600">Bs. ${parseFloat(cuenta.total_calculado).toFixed(2)}</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-xl text-center">
                        <p class="text-sm text-slate-500">Pagado</p>
                        <p class="text-xl font-black text-green-600">Bs. ${parseFloat(cuenta.total_pagado).toFixed(2)}</p>
                    </div>
                    <div class="bg-orange-50 p-4 rounded-xl text-center">
                        <p class="text-sm text-slate-500">Saldo</p>
                        <p class="text-xl font-black text-orange-600">Bs. ${parseFloat(cuenta.saldo_pendiente).toFixed(2)}</p>
                    </div>
                </div>
            `;
            
            if (cuenta.es_emergencia && cuenta.emergency) {
                html += `
                    <div class="bg-red-50 border border-red-100 p-4 rounded-xl mb-6">
                        <p class="text-sm font-bold text-red-800 mb-2">Información de Emergencia</p>
                        <p class="text-sm text-red-700">Código: ${cuenta.emergency.code}</p>
                        <p class="text-sm text-red-700">Ubicación: ${cuenta.emergency.ubicacion_actual}</p>
                    </div>
                `;
            }
            
            if (cuenta.detalles && cuenta.detalles.length > 0) {
                html += `
                    <h4 class="font-bold text-slate-800 mb-3">Detalles de Cargo</h4>
                    <div class="bg-slate-50 rounded-xl overflow-hidden mb-6">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-100">
                                <tr>
                                    <th class="px-4 py-2 text-left">Tipo</th>
                                    <th class="px-4 py-2 text-left">Descripción</th>
                                    <th class="px-4 py-2 text-right">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${cuenta.detalles.map(d => `
                                    <tr class="border-t border-slate-200">
                                        <td class="px-4 py-2">${d.tipo}</td>
                                        <td class="px-4 py-2">${d.descripcion}</td>
                                        <td class="px-4 py-2 text-right">Bs. ${parseFloat(d.subtotal).toFixed(2)}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                `;
            }
            
            if (cuenta.pagos && cuenta.pagos.length > 0) {
                html += `
                    <h4 class="font-bold text-slate-800 mb-3">Historial de Pagos</h4>
                    <div class="space-y-2">
                        ${cuenta.pagos.map(p => `
                            <div class="flex justify-between items-center bg-green-50 p-3 rounded-lg">
                                <div>
                                    <p class="font-medium text-green-800">Bs. ${parseFloat(p.monto).toFixed(2)}</p>
                                    <p class="text-xs text-green-600">${p.metodo} ${p.referencia ? '- ' + p.referencia : ''}</p>
                                </div>
                                <p class="text-xs text-green-600">${p.fecha}</p>
                            </div>
                        `).join('')}
                    </div>
                `;
            }
            
            document.getElementById('detalleContenido').innerHTML = html;
            document.getElementById('modalDetalle').classList.remove('hidden');
            document.getElementById('modalDetalle').classList.add('flex');
        }
    } catch (error) {
        alert('Error al cargar detalle: ' + error.message);
    }
}

function cerrarModalDetalle() {
    document.getElementById('modalDetalle').classList.add('hidden');
    document.getElementById('modalDetalle').classList.remove('flex');
}

// Filtros
document.getElementById('buscarCuenta')?.addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#tablaCuentas tbody tr, #tablaEmergencias tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(term) ? '' : 'none';
    });
});

document.getElementById('filtroTipo')?.addEventListener('change', function(e) {
    const tipo = e.target.value;
    const rows = document.querySelectorAll('#tablaCuentas tbody tr[data-tipo]');
    rows.forEach(row => {
        if (!tipo || row.dataset.tipo === tipo) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>
@endsection
