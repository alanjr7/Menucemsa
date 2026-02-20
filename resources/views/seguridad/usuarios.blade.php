<x-app-layout>
    <div class="p-6 bg-gray-50/50 min-h-screen">

        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Gestión de Usuarios</h1>
                <p class="text-sm text-gray-500">Administración de usuarios, roles y permisos</p>
            </div>
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-bold flex items-center gap-2 transition shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                Nuevo Usuario
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
            @php
                $stats = [
                    ['label' => 'Administrador', 'count' => 3, 'color' => 'purple'],
                    ['label' => 'Médico', 'count' => 12, 'color' => 'blue'],
                    ['label' => 'Enfermera', 'count' => 18, 'color' => 'green'],
                    ['label' => 'Farmacia', 'count' => 5, 'color' => 'orange'],
                    ['label' => 'Caja', 'count' => 4, 'color' => 'indigo'],
                ];
            @endphp
            @foreach($stats as $stat)
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col items-center justify-center group hover:border-blue-300 transition-colors">
                <svg class="w-8 h-8 text-blue-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span class="text-2xl font-bold text-gray-800">{{ $stat['count'] }}</span>
                <span class="text-xs font-medium text-gray-400 uppercase tracking-widest">{{ $stat['label'] }}</span>
            </div>
            @endforeach
        </div>

        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm mb-6 flex justify-between items-center">
            <div class="relative w-96">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <input type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-100 rounded-xl leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-500 sm:text-sm" placeholder="Buscar por usuario, nombre o rol...">
            </div>
            <button class="px-4 py-2 text-sm font-bold text-gray-500 border border-gray-100 rounded-xl hover:bg-gray-50 transition">Filtros</button>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-50">
                <h3 class="font-bold text-gray-800 tracking-tight">Usuarios del Sistema (5)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50 text-gray-400 text-[11px] uppercase font-bold tracking-widest">
                        <tr>
                            <th class="px-6 py-4">Usuario</th>
                            <th class="px-6 py-4">Nombre Completo</th>
                            <th class="px-6 py-4">Rol</th>
                            <th class="px-6 py-4">Área/Especialidad</th>
                            <th class="px-6 py-4">Estado</th>
                            <th class="px-6 py-4">Último Acceso</th>
                            <th class="px-6 py-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php
                            $users = [
                                ['user' => 'jramirez', 'name' => 'Dr. Juan Ramírez Pérez', 'role' => 'Médico', 'area' => 'Medicina General', 'status' => 'Activo', 'date' => '2026-02-03 14:30', 'color' => 'blue'],
                                ['user' => 'atorres', 'name' => 'Dra. Ana Torres López', 'role' => 'Médico', 'area' => 'Pediatría', 'status' => 'Activo', 'date' => '2026-02-03 13:15', 'color' => 'blue'],
                                ['user' => 'mgarcia', 'name' => 'María García Silva', 'role' => 'Enfermera', 'area' => 'UTI', 'status' => 'Activo', 'date' => '2026-02-03 15:00', 'color' => 'green'],
                                ['user' => 'clopez', 'name' => 'Carlos López Mendoza', 'role' => 'Administrador', 'area' => 'Caja', 'status' => 'Activo', 'date' => '2026-02-03 12:45', 'color' => 'purple'],
                                ['user' => 'psilva', 'name' => 'Pedro Silva Castro', 'role' => 'Farmacia', 'area' => 'Dispensación', 'status' => 'Inactivo', 'date' => '2026-02-01 18:20', 'color' => 'orange'],
                            ];
                        @endphp
                        @foreach($users as $u)
                        <tr class="hover:bg-gray-50/50 transition text-sm">
                            <td class="px-6 py-4 font-bold text-gray-800">{{ $u['user'] }}</td>
                            <td class="px-6 py-4 text-gray-600 font-medium">{{ $u['name'] }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded text-[10px] font-bold bg-{{ $u['color'] }}-50 text-{{ $u['color'] }}-600 italic">
                                    {{ $u['role'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-500">{{ $u['area'] }}</td>
                            <td class="px-6 py-4">
                                <span class="flex items-center gap-1.5 px-2 py-1 rounded-md text-[10px] font-bold {{ $u['status'] == 'Activo' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                                    @if($u['status'] == 'Activo')
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
                                    @else
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/></svg>
                                    @endif
                                    {{ $u['status'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-400">{{ $u['date'] }}</td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <button class="flex items-center gap-1.5 px-3 py-1.5 border border-gray-200 rounded-lg text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        Editar
                                    </button>
                                    <button class="flex items-center gap-1.5 px-3 py-1.5 border border-gray-200 rounded-lg text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        Permisos
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

   <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mt-8">
    <div class="flex items-center gap-2 mb-6 text-gray-800">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
        </svg>
        <h3 class="font-bold tracking-tight">Actividad Reciente del Sistema</h3>
    </div>

    <div class="space-y-3">
        <div class="flex justify-between items-center p-4 bg-white border border-gray-100 rounded-xl hover:shadow-sm transition-shadow">
            <div class="flex items-center gap-4">
                <div class="text-blue-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800">jramirez - Acceso al sistema</p>
                    <p class="text-xs text-gray-400">Módulo: Dashboard • IP: 192.168.1.105</p>
                </div>
            </div>
            <span class="text-xs font-medium text-gray-400">2026-02-03 14:30</span>
        </div>

        <div class="flex justify-between items-center p-4 bg-white border border-gray-100 rounded-xl hover:shadow-sm transition-shadow">
            <div class="flex items-center gap-4">
                <div class="text-blue-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800">atorres - Registro de consulta</p>
                    <p class="text-xs text-gray-400">Módulo: Consulta Externa • IP: 192.168.1.108</p>
                </div>
            </div>
            <span class="text-xs font-medium text-gray-400">2026-02-03 13:15</span>
        </div>

        <div class="flex justify-between items-center p-4 bg-white border border-gray-100 rounded-xl hover:shadow-sm transition-shadow">
            <div class="flex items-center gap-4">
                <div class="text-blue-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800">clopez - Registro de pago</p>
                    <p class="text-xs text-gray-400">Módulo: Caja • IP: 192.168.1.120</p>
                </div>
            </div>
            <span class="text-xs font-medium text-gray-400">2026-02-03 12:45</span>
        </div>

        <div class="flex justify-between items-center p-4 bg-white border border-gray-100 rounded-xl hover:shadow-sm transition-shadow">
            <div class="flex items-center gap-4">
                <div class="text-blue-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800">mgarcia - Actualización de signos vitales</p>
                    <p class="text-xs text-gray-400">Módulo: Enfermería • IP: 192.168.1.115</p>
                </div>
            </div>
            <span class="text-xs font-medium text-gray-400">2026-02-03 15:00</span>
        </div>
    </div>
</div>

    </div>
</x-app-layout>
