<x-app-layout>
    <div class="p-8 bg-[#f0f2f5] min-h-screen font-sans antialiased">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Facturación Electrónica</h1>
                <p class="text-slate-500 text-base mt-1">Sistema de emisión y gestión de comprobantes</p>
            </div>
            <button class="bg-[#0061df] hover:bg-[#0052bd] text-white px-6 py-3 rounded-xl flex items-center gap-2 text-sm font-bold shadow-lg shadow-blue-200 transition-all active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Nueva Factura
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
            @php
                $cards = [
                    ['label' => 'Facturas Hoy', 'val' => '15', 'text' => 'text-slate-700'],
                    ['label' => 'Monto Facturado', 'val' => 'S/ 24,580', 'text' => 'text-[#00a65a]'],
                    ['label' => 'Aceptadas SUNAT', 'val' => '12', 'text' => 'text-[#28a745]'],
                    ['label' => 'Pendientes', 'val' => '3', 'text' => 'text-[#f39c12]']
                ];
            @endphp
            @foreach($cards as $card)
            <div class="bg-white p-8 rounded-[24px] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 text-center">
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-3">{{ $card['label'] }}</p>
                <p class="{{ $card['text'] }} text-4xl font-black tracking-tighter">{{ $card['val'] }}</p>
            </div>
            @endforeach
        </div>

        <div class="bg-white p-5 rounded-[24px] shadow-[0_8px_30px_rgb(0,0,0,0.04)] mb-10 flex items-center gap-4">
            <div class="relative flex-1">
                <input type="text" placeholder="Buscar por número, paciente o RUC..." class="w-full pl-14 pr-6 py-4 bg-[#f8fafc] border-none rounded-2xl text-[15px] focus:ring-2 focus:ring-blue-100 transition-all placeholder:text-slate-400">
                <svg class="w-6 h-6 absolute left-5 top-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5"/></svg>
            </div>
            <div class="text-slate-400 font-medium px-6 text-sm">dd/mm/aaaa</div>
            <button class="bg-white border border-slate-200 text-slate-700 px-10 py-4 rounded-2xl text-sm font-extrabold hover:bg-slate-50 transition-all shadow-sm">Filtrar</button>
        </div>

        <div class="bg-white rounded-[24px] shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden mb-10">
            <div class="p-8 border-b border-slate-50">
                <h3 class="font-black text-slate-800 text-lg">Comprobantes Emitidos</h3>
            </div>
            <table class="w-full">
                <thead class="bg-[#fcfdfe] text-slate-400 text-[11px] uppercase font-black tracking-widest border-b border-slate-50">
                    <tr>
                        <th class="px-10 py-5 text-left">Número</th>
                        <th class="px-10 py-5 text-left">Fecha</th>
                        <th class="px-10 py-5 text-left">Paciente</th>
                        <th class="px-10 py-5 text-left">RUC/DNI</th>
                        <th class="px-10 py-5 text-left">Monto</th>
                        <th class="px-10 py-5 text-center">Estado</th>
                        <th class="px-10 py-5 text-center">SUNAT</th>
                        <th class="px-10 py-5 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">

                    {{-- Paciente 1 --}}
                    <tr class="hover:bg-slate-50/50 transition-all">
                        <td class="px-10 py-6 font-bold text-slate-800">F001-00001234</td>
                        <td class="px-10 py-6 text-slate-500">2026-02-03</td>
                        <td class="px-10 py-6 font-semibold text-slate-700">García, Juan</td>
                        <td class="px-10 py-6 text-slate-500">20123456789</td>
                        <td class="px-10 py-6 font-black text-slate-800">S/ 850.00</td>
                        <td class="px-10 py-6 text-center">
                            <span class="bg-[#e7f9f3] text-[#00a65a] px-4 py-1.5 rounded-full text-[11px] font-black border border-[#d1f2e8] uppercase">Emitida</span>
                        </td>
                        <td class="px-10 py-6 text-center text-[#28a745] font-bold text-xs flex items-center justify-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg> Aceptada
                        </td>
                        <td class="px-10 py-6 text-right space-x-2">
                            <button class="bg-[#f8fafc] text-slate-700 px-4 py-2 rounded-xl text-[11px] font-bold border border-slate-100 shadow-sm flex-inline items-center gap-2">PDF</button>
                            <button class="text-slate-400 px-4 py-2 text-[11px] font-bold uppercase hover:text-slate-600">XML</button>
                        </td>
                    </tr>

                    {{-- Paciente 2 --}}
                    <tr class="hover:bg-slate-50/50 transition-all">
                        <td class="px-10 py-6 font-bold text-slate-800">F001-00001237</td>
                        <td class="px-10 py-6 text-slate-500">2026-02-02</td>
                        <td class="px-10 py-6 font-semibold text-slate-700">Martínez, Ana</td>
                        <td class="px-10 py-6 text-slate-500">10456789012</td>
                        <td class="px-10 py-6 font-black text-red-500">S/ 425.00</td>
                        <td class="px-10 py-6 text-center">
                            <span class="bg-red-50 text-red-600 px-4 py-1.5 rounded-full text-[11px] font-black border border-red-100 uppercase">Anulada</span>
                        </td>
                        <td class="px-10 py-6 text-center text-red-500 font-bold text-xs flex items-center justify-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/></svg> Anulada
                        </td>
                        <td class="px-10 py-6 text-right">
                            <button class="bg-[#f8fafc] text-slate-700 px-4 py-2 rounded-xl text-[11px] font-bold border border-slate-100 shadow-sm">PDF</button>
                        </td>
                    </tr>

                    {{-- Paciente 3 --}}
                    <tr class="hover:bg-slate-50/50 transition-all">
                        <td class="px-10 py-6 font-bold text-slate-800">F001-00001240</td>
                        <td class="px-10 py-6 text-slate-500">2026-02-04</td>
                        <td class="px-10 py-6 font-semibold text-slate-700">Lopez, Carlos</td>
                        <td class="px-10 py-6 text-slate-500">20987654321</td>
                        <td class="px-10 py-6 font-black text-slate-800">S/ 600.00</td>
                        <td class="px-10 py-6 text-center">
                            <span class="bg-[#e7f9f3] text-[#00a65a] px-4 py-1.5 rounded-full text-[11px] font-black border border-[#d1f2e8] uppercase">Emitida</span>
                        </td>
                        <td class="px-10 py-6 text-center text-[#28a745] font-bold text-xs">Aceptada</td>
                        <td class="px-10 py-6 text-right">
                            <button class="bg-[#f8fafc] text-slate-700 px-4 py-2 rounded-xl text-[11px] font-bold border border-slate-100 shadow-sm">PDF</button>
                        </td>
                    </tr>

                    {{-- Paciente 4 --}}
                    <tr class="hover:bg-slate-50/50 transition-all">
                        <td class="px-10 py-6 font-bold text-slate-800">F001-00001241</td>
                        <td class="px-10 py-6 text-slate-500">2026-02-05</td>
                        <td class="px-10 py-6 font-semibold text-slate-700">Perez, Lucia</td>
                        <td class="px-10 py-6 text-slate-500">20888999777</td>
                        <td class="px-10 py-6 font-black text-slate-800">S/ 300.00</td>
                        <td class="px-10 py-6 text-center">
                            <span class="bg-yellow-50 text-yellow-600 px-4 py-1.5 rounded-full text-[11px] font-black border border-yellow-100 uppercase">Pendiente</span>
                        </td>
                        <td class="px-10 py-6 text-center text-yellow-600 font-bold text-xs">En proceso</td>
                        <td class="px-10 py-6 text-right">
                            <button class="bg-[#f8fafc] text-slate-700 px-4 py-2 rounded-xl text-[11px] font-bold border border-slate-100 shadow-sm">PDF</button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

        <div class="bg-white p-8 rounded-[32px] shadow-[0_8px_40px_rgb(0,0,0,0.03)] border border-white">
            <h3 class="font-black text-slate-800 mb-8 text-lg">Estado de Conexión SUNAT</h3>
            <div class="bg-[#f2faf7] p-8 rounded-[24px] flex items-center justify-between border border-[#e0f2ec]">
                <div class="flex items-center gap-6">
                    <div class="bg-white p-3 rounded-full border-2 border-[#20c997] shadow-sm">
                        <svg class="w-7 h-7 text-[#20c997]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div>
                        <p class="text-[#087f5b] font-black text-xl">Conexión Activa</p>
                        <p class="text-[#0ca678] text-[15px] font-medium">Sistema de Facturación Electrónica SUNAT operativo</p>
                    </div>
                </div>
                <button class="bg-white border border-slate-200 text-slate-700 px-8 py-3.5 rounded-[18px] text-sm font-extrabold shadow-sm hover:shadow-md transition-all active:scale-95">
                    Verificar Conexión
                </button>
            </div>
        </div>
    </div>
</x-app-layout>
