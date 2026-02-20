<x-app-layout>
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-red-100 rounded-full text-red-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800">Servicio de Emergencias</h1>
                </div>
                <p class="text-sm text-gray-500 ml-11">Monitor de pacientes en tiempo real</p>
            </div>
            <button class="bg-red-600 text-white px-4 py-2 rounded-lg font-bold flex items-center gap-2 hover:bg-red-700 shadow-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Código Rojo
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
                <div><p class="text-xs text-gray-400 uppercase font-bold">Críticos</p><p class="text-3xl font-black text-red-600">1</p></div>
                <div class="w-3 h-3 bg-red-400 rounded-full animate-pulse"></div>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
                <div><p class="text-xs text-gray-400 uppercase font-bold">Urgentes</p><p class="text-3xl font-black text-orange-500">1</p></div>
                <div class="w-3 h-3 bg-orange-400 rounded-full"></div>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
                <div><p class="text-xs text-gray-400 uppercase font-bold">No Urgentes</p><p class="text-3xl font-black text-green-500">1</p></div>
                <div class="w-3 h-3 bg-green-400 rounded-full"></div>
            </div>
            <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center">
                <div><p class="text-xs text-gray-400 uppercase font-bold">En Atención</p><p class="text-3xl font-black text-blue-600">3</p></div>
                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
        </div>
<div class="space-y-4">
    <div class="bg-white rounded-2xl shadow-sm border-l-4 border-red-500 p-5 flex items-center justify-between group hover:shadow-md transition font-sans">
        <div class="flex items-center gap-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center text-red-600 font-bold text-xl shadow-inner">R</div>
                <p class="text-[10px] text-gray-400 mt-1 font-bold">5 min</p>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <h4 class="font-bold text-gray-800 text-lg leading-tight">Rodríguez, Pedro - 58 años</h4>
                </div>
                <p class="text-xs text-gray-400 mb-2 ml-6 font-medium">HC-001236</p>
                <p class="text-sm text-gray-700 ml-6"><strong>Motivo:</strong> Dolor torácico intenso</p>
                <p class="text-sm text-gray-700 ml-6"><strong>Ubicación:</strong> Box 1</p>
            </div>
        </div>

        <div class="hidden xl:flex gap-2">
            <div class="bg-blue-50/50 px-3 py-2 rounded-lg border border-blue-100 min-w-[85px] text-center">
                <p class="text-[9px] text-gray-400 uppercase font-extrabold tracking-tighter">P.A.</p>
                <p class="font-black text-gray-700 text-sm">160/100</p>
            </div>
            <div class="bg-blue-50/50 px-3 py-2 rounded-lg border border-blue-100 min-w-[70px] text-center">
                <p class="text-[9px] text-gray-400 uppercase font-extrabold tracking-tighter">F.C.</p>
                <p class="font-black text-gray-700 text-sm">110</p>
            </div>
            <div class="bg-blue-50/50 px-3 py-2 rounded-lg border border-blue-100 min-w-[70px] text-center">
                <p class="text-[9px] text-gray-400 uppercase font-extrabold tracking-tighter">Temp.</p>
                <p class="font-black text-gray-700 text-sm">37.2</p>
            </div>
            <div class="bg-blue-50/50 px-3 py-2 rounded-lg border border-blue-100 min-w-[75px] text-center">
                <p class="text-[9px] text-gray-400 uppercase font-extrabold tracking-tighter">Sat O₂</p>
                <p class="font-black text-gray-700 text-sm">94%</p>
            </div>
        </div>

        <div class="flex flex-col gap-1 w-28">
            <button class="bg-blue-600 text-white py-1.5 rounded-lg text-xs font-bold hover:bg-blue-700 transition shadow-sm">Atender</button>
            <button class="bg-white border border-gray-200 text-gray-600 py-1.5 rounded-lg text-xs font-bold hover:bg-gray-50 transition">Historia</button>
            <button class="bg-white border border-gray-200 text-gray-600 py-1.5 rounded-lg text-xs font-bold hover:bg-gray-50 transition">Órdenes</button>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border-l-4 border-orange-400 p-5 flex items-center justify-between group hover:shadow-md transition">
        <div class="flex items-center gap-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center text-orange-600 font-bold text-xl shadow-inner">A</div>
                <p class="text-[10px] text-gray-400 mt-1 font-bold">20 min</p>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <h4 class="font-bold text-gray-800 text-lg leading-tight">Silva, Carmen - 35 años</h4>
                </div>
                <p class="text-xs text-gray-400 mb-2 ml-6 font-medium">HC-001240</p>
                <p class="text-sm text-gray-700 ml-6"><strong>Motivo:</strong> Fractura de muñeca</p>
                <p class="text-sm text-gray-700 ml-6"><strong>Ubicación:</strong> Box 3</p>
            </div>
        </div>

        <div class="hidden xl:flex gap-2">
            <div class="bg-blue-50/50 px-3 py-2 rounded-lg border border-blue-100 min-w-[85px] text-center">
                <p class="text-[9px] text-gray-400 uppercase font-extrabold tracking-tighter">P.A.</p>
                <p class="font-black text-gray-700 text-sm">120/80</p>
            </div>
            <div class="bg-blue-50/50 px-3 py-2 rounded-lg border border-blue-100 min-w-[70px] text-center">
                <p class="text-[9px] text-gray-400 uppercase font-extrabold tracking-tighter">F.C.</p>
                <p class="font-black text-gray-700 text-sm">85</p>
            </div>
            <div class="bg-blue-50/50 px-3 py-2 rounded-lg border border-blue-100 min-w-[70px] text-center">
                <p class="text-[9px] text-gray-400 uppercase font-extrabold tracking-tighter">Temp.</p>
                <p class="font-black text-gray-700 text-sm">36.8</p>
            </div>
            <div class="bg-blue-50/50 px-3 py-2 rounded-lg border border-blue-100 min-w-[75px] text-center">
                <p class="text-[9px] text-gray-400 uppercase font-extrabold tracking-tighter">Sat O₂</p>
                <p class="font-black text-gray-700 text-sm">98%</p>
            </div>
        </div>

        <div class="flex flex-col gap-1 w-28">
            <button class="bg-blue-600 text-white py-1.5 rounded-lg text-xs font-bold">Atender</button>
            <button class="bg-white border border-gray-200 text-gray-600 py-1.5 rounded-lg text-xs font-bold">Historia</button>
            <button class="bg-white border border-gray-200 text-gray-600 py-1.5 rounded-lg text-xs font-bold">Órdenes</button>
        </div>
    </div>
</div>
</x-app-layout>
