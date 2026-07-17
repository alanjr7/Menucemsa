@extends('layouts.app')

@section('content')
    <div class="w-full p-8 bg-[#f8fafc] min-h-screen font-sans antialiased">

        <!-- Header -->
        <div class="flex justify-between items-start mb-8">
            <div>
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-50 rounded-lg text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"
                                stroke-width="2" />
                        </svg>
                    </div>
                    <h1 class="text-[26px] font-black text-slate-800 tracking-tight">Cobranza a Seguros</h1>
                </div>
                <p class="text-slate-500 text-[15px] font-medium mt-1 ml-11">Cuentas por cobrar a aseguradoras, antigüedad
                    de la deuda y liquidación</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.seguros') }}"
                    class="bg-white hover:bg-gray-50 text-slate-700 px-6 py-2.5 rounded-xl flex items-center gap-2 text-sm font-bold shadow-sm border border-slate-200 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="2" />
                    </svg>
                    Volver
                </a>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 text-center">
                <p class="text-slate-400 text-[13px] font-medium mb-1">Por cobrar (total)</p>
                <p class="text-[#e67e22] text-[30px] font-black tracking-tighter">Bs.
                    {{ number_format((float) $stats['total_por_cobrar'], 2) }}</p>
            </div>
            <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 text-center">
                <p class="text-slate-400 text-[13px] font-medium mb-1">Coberturas pendientes</p>
                <p class="text-slate-800 text-[30px] font-black tracking-tighter">{{ $stats['cantidad_pendientes'] }}</p>
            </div>
            <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 text-center">
                <p class="text-slate-400 text-[13px] font-medium mb-1">Cobrado (período)</p>
                <p class="text-[#0ca678] text-[30px] font-black tracking-tighter">Bs.
                    {{ number_format((float) $stats['total_cobrado'], 2) }}</p>
            </div>
            <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 text-center">
                <p class="text-slate-400 text-[13px] font-medium mb-1">Aseguradoras con deuda</p>
                <p class="text-slate-800 text-[30px] font-black tracking-tighter">{{ $stats['aseguradoras_con_deuda'] }}</p>
            </div>
        </div>

        <!-- Antigüedad de la deuda (aging) -->
        <div class="bg-white p-6 rounded-[20px] shadow-sm border border-slate-100 mb-6">
            <h3 class="font-bold text-slate-800 text-sm mb-4">Antigüedad de la deuda (días)</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $agingColors = ['0-30' => 'text-emerald-600', '31-60' => 'text-yellow-600', '61-90' => 'text-orange-600', '90+' => 'text-red-600'];
                @endphp
                @foreach ($aging as $rango => $monto)
                    <div class="border border-slate-100 rounded-xl p-4 text-center">
                        <p class="text-slate-400 text-[12px] font-bold uppercase tracking-wider mb-1">{{ $rango }} días</p>
                        <p class="{{ $agingColors[$rango] }} text-[20px] font-black tracking-tight">Bs.
                            {{ number_format((float) $monto, 2) }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Resumen por aseguradora -->
        @if ($porAseguradora->isNotEmpty())
            <div class="bg-white rounded-[24px] border border-slate-100 shadow-sm overflow-hidden mb-6">
                <div class="p-6 border-b border-slate-50">
                    <h3 class="font-bold text-slate-800 text-lg">Pendiente por aseguradora</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="text-slate-400 text-[11px] uppercase font-bold tracking-widest border-b border-slate-50">
                            <tr>
                                <th class="px-6 py-4">Aseguradora</th>
                                <th class="px-6 py-4">Coberturas</th>
                                <th class="px-6 py-4">Mora máx.</th>
                                <th class="px-6 py-4 text-right">Total por cobrar</th>
                                <th class="px-6 py-4 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="text-[14px] divide-y divide-slate-50">
                            @foreach ($porAseguradora as $a)
                                <tr class="hover:bg-slate-50/50 transition-all">
                                    <td class="px-6 py-4 font-bold text-slate-700">{{ $a['nombre'] }}</td>
                                    <td class="px-6 py-4 text-slate-500">{{ $a['cantidad'] }}</td>
                                    <td class="px-6 py-4">
                                        <span class="{{ $a['dias_max'] > 90 ? 'text-red-600' : ($a['dias_max'] > 30 ? 'text-orange-600' : 'text-slate-500') }} font-bold">{{ $a['dias_max'] }} días</span>
                                    </td>
                                    <td class="px-6 py-4 text-right font-black text-slate-800">Bs. {{ number_format((float) $a['total'], 2) }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <button onclick="abrirLote({{ $a['seguro_id'] }}, '{{ addslashes($a['nombre']) }}', '{{ number_format((float) $a['total'], 2) }}', {{ $a['cantidad'] }})"
                                            class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-[12px] font-bold transition-colors">
                                            Liquidar lote
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Filtros -->
        <div class="bg-white p-5 rounded-[20px] border border-slate-100 shadow-sm mb-6">
            <form method="GET" action="{{ route('admin.seguros.cobranza') }}" class="flex flex-wrap items-end gap-4">
                <div class="flex-1 min-w-[180px]">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Aseguradora</label>
                    <select name="seguro_id"
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-50 outline-none">
                        <option value="">Todas</option>
                        @foreach ($seguros as $seguro)
                            <option value="{{ $seguro->id }}" {{ request('seguro_id') == $seguro->id ? 'selected' : '' }}>
                                {{ $seguro->nombre_empresa }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Estado</label>
                    <select name="estado"
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-50 outline-none">
                        <option value="">Todos</option>
                        <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Por cobrar</option>
                        <option value="cobrado" {{ request('estado') == 'cobrado' ? 'selected' : '' }}>Cobrado</option>
                        <option value="anulado" {{ request('estado') == 'anulado' ? 'selected' : '' }}>Anulado</option>
                    </select>
                </div>
                <div class="flex-1 min-w-[160px]">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Desde</label>
                    <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}"
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-50 outline-none">
                </div>
                <div class="flex-1 min-w-[160px]">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Hasta</label>
                    <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}"
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-emerald-50 outline-none">
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="bg-[#0061df] hover:bg-blue-700 text-white px-6 py-3 rounded-xl text-sm font-bold shadow-sm transition-all">Filtrar</button>
                    <a href="{{ route('admin.seguros.cobranza') }}"
                        class="bg-gray-100 hover:bg-gray-200 text-slate-700 px-6 py-3 rounded-xl text-sm font-bold transition-all">Limpiar</a>
                </div>
            </form>
        </div>

        <!-- Tabla de coberturas -->
        <div class="bg-white rounded-[24px] border border-slate-100 shadow-sm overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="text-slate-400 text-[11px] uppercase font-bold tracking-widest border-b border-slate-50">
                        <tr>
                            <th class="px-6 py-5">N° / Fecha</th>
                            <th class="px-6 py-5">Aseguradora</th>
                            <th class="px-6 py-5">Paciente</th>
                            <th class="px-6 py-5">Cuenta</th>
                            <th class="px-6 py-5 text-right">Monto</th>
                            <th class="px-6 py-5 text-right">Débito IVA</th>
                            <th class="px-6 py-5 text-center">Antigüedad</th>
                            <th class="px-6 py-5 text-center">Estado</th>
                            <th class="px-6 py-5 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-[14px] divide-y divide-slate-50">
                        @forelse ($cobros as $c)
                            <tr class="hover:bg-slate-50/50 transition-all">
                                <td class="px-6 py-4">
                                    <p class="font-bold text-slate-700">{{ $c->id }}</p>
                                    <p class="text-slate-400 text-xs">{{ $c->created_at->format('d/m/Y H:i') }}</p>
                                </td>
                                <td class="px-6 py-4 text-slate-600">{{ $c->seguro?->nombre_empresa ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $c->cuentaCobro?->paciente?->nombre ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-slate-400 text-xs">{{ $c->cuenta_cobro_id }}</td>
                                <td class="px-6 py-4 text-right font-black text-slate-800">Bs. {{ number_format($c->monto, 2) }}</td>
                                <td class="px-6 py-4 text-right text-slate-500">Bs. {{ number_format($c->debito_fiscal, 2) }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if ($c->estado === 'pendiente')
                                        <span class="text-slate-500 text-xs font-bold">{{ (int) $c->created_at->diffInDays(now()) }} días</span>
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($c->estado === 'pendiente')
                                        <span class="bg-orange-50 text-orange-600 border border-orange-100 px-3 py-1.5 rounded-lg text-[12px] font-bold">Por cobrar</span>
                                    @elseif ($c->estado === 'cobrado')
                                        <span class="bg-emerald-50 text-emerald-600 border border-emerald-100 px-3 py-1.5 rounded-lg text-[12px] font-bold">Cobrado</span>
                                    @else
                                        <span class="bg-red-50 text-red-600 border border-red-100 px-3 py-1.5 rounded-lg text-[12px] font-bold">Anulado</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    @if ($c->estado === 'pendiente')
                                        <button onclick="abrirLiquidar('{{ $c->id }}', 'Bs. {{ number_format($c->monto, 2) }}')"
                                            class="text-emerald-600 hover:text-emerald-800 text-xs font-bold mr-3">Liquidar</button>
                                        <button onclick="abrirAnular('{{ $c->id }}')"
                                            class="text-red-500 hover:text-red-700 text-xs font-bold">Anular</button>
                                    @elseif ($c->estado === 'cobrado')
                                        <span class="text-slate-400 text-xs">{{ $c->liquidado_en?->format('d/m/Y') }}{{ $c->liquidado_referencia ? ' · ' . $c->liquidado_referencia : '' }}</span>
                                    @else
                                        <span class="text-slate-300 text-xs">{{ $c->anulado_motivo }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-10 text-center text-slate-500">No hay coberturas de seguro registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">{{ $cobros->links() }}</div>
        </div>
    </div>

    <!-- Modal liquidar -->
    <div id="modalLiquidar" class="fixed inset-0 bg-black/50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-[24px] p-8 w-full max-w-md shadow-2xl">
                <h3 class="text-xl font-bold text-slate-800 mb-1">Registrar cobro</h3>
                <p class="text-slate-500 text-sm mb-5">Confirma que la aseguradora pagó <span id="liqMonto" class="font-bold text-slate-700"></span>.</p>
                <label class="block text-sm font-medium text-slate-700 mb-1">Referencia (opcional)</label>
                <input id="liqReferencia" type="text" placeholder="N° de transferencia / liquidación"
                    class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm mb-5 outline-none focus:ring-2 focus:ring-emerald-50">
                <div class="flex justify-end gap-2">
                    <button onclick="cerrar('modalLiquidar')" class="px-5 py-2.5 rounded-xl text-sm font-bold bg-gray-100 hover:bg-gray-200 text-slate-700">Cancelar</button>
                    <button id="liqConfirm" class="px-5 py-2.5 rounded-xl text-sm font-bold bg-emerald-600 hover:bg-emerald-700 text-white">Confirmar cobro</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal liquidar lote -->
    <div id="modalLote" class="fixed inset-0 bg-black/50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-[24px] p-8 w-full max-w-md shadow-2xl">
                <h3 class="text-xl font-bold text-slate-800 mb-1">Liquidar lote</h3>
                <p class="text-slate-500 text-sm mb-5"><span id="loteResumen" class="font-bold text-slate-700"></span></p>
                <label class="block text-sm font-medium text-slate-700 mb-1">Referencia de la liquidación (opcional)</label>
                <input id="loteReferencia" type="text" placeholder="N° de liquidación / lote"
                    class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm mb-5 outline-none focus:ring-2 focus:ring-emerald-50">
                <div class="flex justify-end gap-2">
                    <button onclick="cerrar('modalLote')" class="px-5 py-2.5 rounded-xl text-sm font-bold bg-gray-100 hover:bg-gray-200 text-slate-700">Cancelar</button>
                    <button id="loteConfirm" class="px-5 py-2.5 rounded-xl text-sm font-bold bg-emerald-600 hover:bg-emerald-700 text-white">Liquidar todo</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal anular -->
    <div id="modalAnular" class="fixed inset-0 bg-black/50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="bg-white rounded-[24px] p-8 w-full max-w-md shadow-2xl">
                <h3 class="text-xl font-bold text-slate-800 mb-1">Anular cobertura</h3>
                <p class="text-slate-500 text-sm mb-5">Revierte la venta a la aseguradora (queda registrada como anulada).</p>
                <label class="block text-sm font-medium text-slate-700 mb-1">Motivo</label>
                <input id="anuMotivo" type="text" placeholder="Motivo de la anulación"
                    class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm mb-5 outline-none focus:ring-2 focus:ring-red-50">
                <div class="flex justify-end gap-2">
                    <button onclick="cerrar('modalAnular')" class="px-5 py-2.5 rounded-xl text-sm font-bold bg-gray-100 hover:bg-gray-200 text-slate-700">Cancelar</button>
                    <button id="anuConfirm" class="px-5 py-2.5 rounded-xl text-sm font-bold bg-red-600 hover:bg-red-700 text-white">Anular</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;
        let liqId = null, anuId = null, loteSeguroId = null;

        function cerrar(id) { document.getElementById(id).classList.add('hidden'); }

        function abrirLiquidar(id, monto) {
            liqId = id;
            document.getElementById('liqMonto').textContent = monto;
            document.getElementById('liqReferencia').value = '';
            document.getElementById('modalLiquidar').classList.remove('hidden');
        }

        function abrirAnular(id) {
            anuId = id;
            document.getElementById('anuMotivo').value = '';
            document.getElementById('modalAnular').classList.remove('hidden');
        }

        function abrirLote(seguroId, nombre, total, cantidad) {
            loteSeguroId = seguroId;
            document.getElementById('loteResumen').textContent = `${nombre}: ${cantidad} coberturas por Bs. ${total}`;
            document.getElementById('loteReferencia').value = '';
            document.getElementById('modalLote').classList.remove('hidden');
        }

        async function post(url, body, btn) {
            btn.disabled = true;
            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    body: JSON.stringify(body),
                });
                const data = await res.json();
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'No se pudo completar la operación.');
                    btn.disabled = false;
                }
            } catch (e) {
                alert('Error de red.');
                btn.disabled = false;
            }
        }

        document.getElementById('liqConfirm').addEventListener('click', function () {
            post(`/admin/seguros/cobranza/${liqId}/liquidar`, { referencia: document.getElementById('liqReferencia').value }, this);
        });
        document.getElementById('loteConfirm').addEventListener('click', function () {
            post(`/admin/seguros/cobranza/aseguradora/${loteSeguroId}/liquidar-lote`, { referencia: document.getElementById('loteReferencia').value }, this);
        });
        document.getElementById('anuConfirm').addEventListener('click', function () {
            const motivo = document.getElementById('anuMotivo').value.trim();
            if (!motivo) { alert('Indica el motivo.'); return; }
            post(`/admin/seguros/cobranza/${anuId}/anular`, { motivo }, this);
        });
    </script>
@endsection
