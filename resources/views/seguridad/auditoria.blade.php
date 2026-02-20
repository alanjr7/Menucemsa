<x-app-layout>
    <div class="p-6 bg-gray-50/50 min-h-screen">

        <div class="mb-8">
            <div class="flex items-center gap-3 text-blue-600 mb-1">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Auditoría del Sistema</h1>
            </div>
            <p class="text-sm text-gray-500 ml-11 font-medium">Registro de actividades y trazabilidad</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex justify-between items-center">
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Eventos Hoy</p>
                    <p class="text-3xl font-black text-gray-800 mt-1">1,248</p>
                </div>
                <div class="text-blue-500">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex justify-between items-center">
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Usuarios Activos</p>
                    <p class="text-3xl font-black text-gray-800 mt-1">42</p>
                </div>
                <div class="text-green-500">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex justify-between items-center">
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Accesos Hoy</p>
                    <p class="text-3xl font-black text-gray-800 mt-1">358</p>
                </div>
                <div class="text-blue-600">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex justify-between items-center">
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">Registros Modificados</p>
                    <p class="text-3xl font-black text-gray-800 mt-1">87</p>
                </div>
                <div class="text-orange-500">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6 flex flex-wrap gap-4 items-center">
            <div class="relative flex-1 min-w-[200px]">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <input type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-100 rounded-xl text-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Buscar...">
            </div>
<select class="px-4 py-2 border border-gray-100 rounded-xl text-sm text-gray-600 focus:ring-blue-500 min-w-[180px]">
                <option>Módulos</option>
                <option>Consulta Externa</option>
                <option>Farmacia</option>
                <option>Caja</option>
                <option>Usuarios</option>
            </select>

            <select class="px-4 py-2 border border-gray-100 rounded-xl text-sm text-gray-600 focus:ring-blue-500 min-w-[180px]">
                <option>Todos los usuarios</option>
                <option>jramirez</option>
                <option>mgarcia</option>
                <option>clopez</option>
            </select>

            <input type="text" class="px-4 py-2 border border-gray-100 rounded-xl text-sm text-gray-400 focus:ring-blue-500 min-w-[150px]" placeholder="dd/mm/aaaa">
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
            <div class="p-6 border-b border-gray-50">
                <h3 class="font-bold text-gray-800 tracking-tight">Registro de Actividades</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50 text-gray-400 text-[11px] uppercase font-bold tracking-widest">
                        <tr>
                            <th class="px-6 py-4">Fecha y Hora</th>
                            <th class="px-6 py-4">Usuario</th>
                            <th class="px-6 py-4">Módulo</th>
                            <th class="px-6 py-4">Acción</th>
                            <th class="px-6 py-4">Detalle</th>
                            <th class="px-6 py-4 text-right">IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 font-medium text-gray-700">2026-02-03 14:30:25</td>
                            <td class="px-6 py-4 text-blue-600 font-bold italic">jramirez</td>
                            <td class="px-6 py-4 text-gray-600">Consulta Externa</td>
                            <td class="px-6 py-4 text-gray-600 font-medium tracking-tight">Registró consulta médica</td>
                            <td class="px-6 py-4 text-gray-400 italic">Paciente: García, Juan (HC-001234)</td>
                            <td class="px-6 py-4 text-right text-gray-400 font-mono">192.168.1.105</td>
                        </tr>
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 font-medium text-gray-700">2026-02-03 14:15:10</td>
                            <td class="px-6 py-4 text-blue-600 font-bold italic">mgarcia</td>
                            <td class="px-6 py-4 text-gray-600">Enfermería</td>
                            <td class="px-6 py-4 text-gray-600 font-medium tracking-tight">Registró signos vitales</td>
                            <td class="px-6 py-4 text-gray-400 italic">Paciente: García, Juan (H-201)</td>
                            <td class="px-6 py-4 text-right text-gray-400 font-mono">192.168.1.115</td>
                        </tr>
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 font-medium text-gray-700">2026-02-03 13:45:30</td>
                            <td class="px-6 py-4 text-blue-600 font-bold italic">clopez</td>
                            <td class="px-6 py-4 text-gray-600">Caja</td>
                            <td class="px-6 py-4 text-gray-600 font-medium tracking-tight">Registró cobro</td>
                            <td class="px-6 py-4 text-gray-400 italic">Recibo: R-001234 - S/ 850.00</td>
                            <td class="px-6 py-4 text-right text-gray-400 font-mono">192.168.1.120</td>
                        </tr>
                        <tr class="hover:bg-gray-50/50 transition bg-blue-50/20">
                            <td class="px-6 py-4 font-medium text-gray-700">2026-02-03 13:30:15</td>
                            <td class="px-6 py-4 text-blue-600 font-bold italic">admin</td>
                            <td class="px-6 py-4 text-gray-600">Usuarios</td>
                            <td class="px-6 py-4 text-gray-600 font-medium tracking-tight">Modificó permisos</td>
                            <td class="px-6 py-4 text-gray-400 italic">Usuario: psilva - Rol: Farmacia</td>
                            <td class="px-6 py-4 text-right text-gray-400 font-mono">192.168.1.100</td>
                        </tr>
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 font-medium text-gray-700">2026-02-03 12:20:45</td>
                            <td class="px-6 py-4 text-blue-600 font-bold italic">atorres</td>
                            <td class="px-6 py-4 text-gray-600">Farmacia</td>
                            <td class="px-6 py-4 text-gray-600 font-medium tracking-tight">Dispensó medicamento</td>
                            <td class="px-6 py-4 text-gray-400 italic">Metformina 850mg - 30 tabletas</td>
                            <td class="px-6 py-4 text-right text-gray-400 font-mono">192.168.1.118</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-gray-800 tracking-tight mb-4 text-sm">Exportar Logs de Auditoría</h3>
            <div class="flex gap-3">
                <button class="flex items-center gap-2 px-5 py-2 border border-gray-200 rounded-xl text-xs font-bold text-gray-700 hover:bg-gray-50 transition shadow-sm">
                    <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/></svg>
                    Exportar a Excel
                </button>
                <button class="flex items-center gap-2 px-5 py-2 border border-gray-200 rounded-xl text-xs font-bold text-gray-700 hover:bg-gray-50 transition shadow-sm">
                    <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"/></svg>
                    Exportar a PDF
                </button>
            </div>
        </div>

    </div>
</x-app-layout>
