<aside class="w-64 h-screen flex flex-col flex-shrink-0 transition-all duration-300 shadow-xl bg-[#1e3a8a] overflow-hidden">

    <div class="h-16 flex-shrink-0 flex items-center px-6 border-b border-blue-800/50 bg-[#1a306d]">
        <div class="flex items-center gap-3">
            <div class="p-1.5 bg-blue-500/20 rounded-lg text-green-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div>
                <h1 class="font-bold text-lg leading-tight uppercase tracking-tight text-white">HIS / CEMSA</h1>
                <p class="text-[10px] text-blue-300 uppercase tracking-widest font-semibold">Sistema Clínico</p>
            </div>
        </div>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto custom-scrollbar">

        <a href="{{ route('dashboard') }}"
           class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-md' : 'text-blue-100 hover:bg-blue-800/50 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span class="text-sm font-medium">Dashboard</span>
        </a>

        <a href="{{ route('reception') }}"
           class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 {{ request()->routeIs('reception') ? 'bg-blue-600 text-white shadow-md' : 'text-blue-100 hover:bg-blue-800/50 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            <span class="text-sm font-medium">Recepción</span>
        </a>

        <div class="py-2 px-4"><hr class="border-blue-800/50"></div>

        @php
            $menus = [
                'Pacientes' => [
                    'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                    'active' => request()->is('patients*', 'admision*', 'consulta*', 'emergencias*', 'enfermeria*', 'uti*', 'quirofano*', 'hospitalizacion*'),
                    'links' => [
                        ['r' => 'patients.index', 'l' => 'Maestro de Pacientes'],
                        ['r' => 'admision.index', 'l' => 'Admisión'],
                        ['r' => 'consulta.index', 'l' => 'Consulta Externa'],
                        ['r' => 'emergencias.index', 'l' => 'Emergencias'],
                        ['r' => 'enfermeria.index', 'l' => 'Enfermería'],
                        ['r' => 'uti.index', 'l' => 'UTI'],
                        ['r' => 'quirofano.index', 'l' => 'Quirófano'],
                        ['r' => 'hospitalizacion.index', 'l' => 'Hospitalización'],
                    ]
                ],
                'Administración' => [
                    'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    'active' => request()->is('caja*', 'facturacion*', 'admin*'),
                    'links' => [
                        ['r' => 'caja.index', 'l' => 'Caja Central'],
                        ['r' => 'facturacion.index', 'l' => 'Facturación'],
                        ['r' => 'admin.tarifarios', 'l' => 'Tarifarios'],
                        ['r' => 'admin.seguros', 'l' => 'Seguros'],
                        ['r' => 'admin.cuentas', 'l' => 'Cuentas por Cobrar'],
                    ]
                ],
                'Logística' => [
                    'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                    'active' => request()->is('farmacia*'),
                    'links' => [['r' => 'farmacia.index', 'l' => 'Farmacia']]
                ],
                'Gerencial' => [
                    'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                    'active' => request()->is('gerencial*'),
                    'links' => [
                        ['r' => 'gerencial.reportes', 'l' => 'Reportes'],
                        ['r' => 'gerencial.kpis', 'l' => 'KPIs']
                    ]
                ],
                'Seguridad' => [
                    'icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
                    'active' => request()->is('seguridad*'),
                    'links' => [
                        ['r' => 'seguridad.usuarios.index', 'l' => 'Usuarios'],
                        ['r' => 'seguridad.auditoria.index', 'l' => 'Auditoría'],
                        ['r' => 'seguridad.configuracion.index', 'l' => 'Configuración']
                    ]
                ]
            ];
        @endphp

        @foreach($menus as $title => $data)
            <div x-data="{ open: {{ $data['active'] ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="w-full flex items-center px-4 py-3 text-blue-100 hover:bg-blue-800/50 hover:text-white rounded-lg transition-all focus:outline-none">
                    <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $data['icon'] }}"/>
                    </svg>
                    <span class="text-sm font-medium flex-1 text-left">{{ $title }}</span>
                    <svg class="w-4 h-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open" x-cloak class="pl-4 mt-1 space-y-1 border-l-2 border-blue-700/50 ml-6">
                    @foreach($data['links'] as $link)
                        <a href="{{ route($link['r']) }}"
                           class="block px-3 py-2 text-[13px] rounded-md transition-colors {{ request()->routeIs($link['r']) ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">
                            {{ $link['l'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach

    </nav>

    <div class="h-20 flex-shrink-0 p-4 border-t border-blue-800/50 bg-[#1a306d]">
        <div class="flex items-center gap-3 px-2">
            <div class="w-9 h-9 rounded-full bg-blue-600 flex-shrink-0 flex items-center justify-center text-sm font-bold text-white shadow-inner border border-blue-400/30">
                {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
            </div>
            <div class="text-[11px] overflow-hidden">
                <p class="font-bold text-blue-100 truncate">{{ Auth::user()->name ?? 'Usuario' }}</p>
                <div class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                    <span class="text-blue-400 font-medium">En línea</span>
                </div>
            </div>
        </div>
    </div>
</aside>
