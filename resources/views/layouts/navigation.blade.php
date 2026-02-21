<div class="flex flex-col h-full bg-[#1e3a8a] text-white shadow-xl">
    <div class="h-16 flex items-center px-6 border-b border-blue-800/50 bg-[#1a306d]">
        <div class="flex items-center gap-3">
            <div class="p-1.5 bg-blue-500/20 rounded-lg">
                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
           class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-md' : 'text-blue-100 hover:bg-blue-800/50' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span class="text-sm font-medium">Dashboard</span>
        </a>

        @if(Auth::user()->isAdmin() || Auth::user()->isReception())
        <a href="{{ route('reception') }}"
           class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('reception') ? 'bg-blue-600 text-white shadow-md' : 'text-blue-100 hover:bg-blue-800/50' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            <span class="text-sm font-medium">Recepción</span>
        </a>
        @endif

        
        <hr class="border-blue-800/50 my-2 mx-4">

        @if(Auth::user()->isAdmin() || Auth::user()->isDirMedico() || Auth::user()->isEmergencia())
        <div x-data="{ open: {{ request()->is('emergencias*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center px-4 py-3 text-blue-100 hover:bg-blue-800/50 rounded-lg transition group">
                <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                <span class="text-sm font-medium flex-1 text-left">Emergencias</span>
                <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <div x-show="open" x-cloak class="pl-4 mt-1 space-y-1 border-l-2 border-blue-700/50 ml-6">
                <a href="{{ route('emergencias.index') }}" class="block px-3 py-2 text-xs rounded-md {{ request()->routeIs('emergencias.index') ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">
                    Gestión de Emergencias
                </a>
            </div>
        </div>
        @endif

        @if(Auth::user()->isAdmin() || Auth::user()->isDirMedico())
        <div x-data="{ open: {{ request()->is('patients*', 'admision*', 'consulta*', 'enfermeria*', 'uti*', 'quirofano*', 'hospitalizacion*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center px-4 py-3 text-blue-100 hover:bg-blue-800/50 rounded-lg transition group">
                <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span class="text-sm font-medium flex-1 text-left">Pacientes</span>
                <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <div x-show="open" x-cloak class="pl-4 mt-1 space-y-1 border-l-2 border-blue-700/50 ml-6">
                @php
                    $pacientesLinks = [
                        ['r' => 'patients.index', 'l' => 'Maestro de Pacientes'],
                        ['r' => 'admision.index', 'l' => 'Admisión'],
                    ];
                    
                    // Admin y Director Médico pueden ver todas las áreas médicas excepto emergencias
                    if(Auth::user()->isAdmin() || Auth::user()->isDirMedico()):
                        $pacientesLinks = array_merge($pacientesLinks, [
                            ['r' => 'consulta.index', 'l' => 'Consulta Externa'],
                            ['r' => 'enfermeria.index', 'l' => 'Enfermería'],
                            ['r' => 'uti.index', 'l' => 'UTI'],
                            ['r' => 'quirofano.index', 'l' => 'Quirófano'],
                            ['r' => 'hospitalizacion.index', 'l' => 'Hospitalización'],
                        ]);
                    endif;
                @endphp
                @foreach($pacientesLinks as $link)
                    <a href="{{ route($link['r']) }}" class="block px-3 py-2 text-xs rounded-md {{ request()->routeIs($link['r']) ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">
                        {{ $link['l'] }}
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        @if(Auth::user()->isAdmin() || Auth::user()->isCaja())
        <div x-data="{ open: {{ request()->is('caja*', 'facturacion*', 'admin*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center px-4 py-3 text-blue-100 hover:bg-blue-800/50 rounded-lg transition group">
                <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span class="text-sm font-medium flex-1 text-left">Administración</span>
                <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <div x-show="open" x-cloak class="pl-4 mt-1 space-y-1 border-l-2 border-blue-700/50 ml-6">
                <a href="{{ route('admin.caja.index') }}" class="block px-3 py-2 text-xs rounded-md {{ request()->routeIs('admin.caja.index') ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">Caja Central</a>
                <a href="{{ route('admin.facturacion.index') }}" class="block px-3 py-2 text-xs rounded-md {{ request()->routeIs('admin.facturacion.index') ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">Facturación</a>
                <a href="{{ route('admin.tarifarios') }}" class="block px-3 py-2 text-xs rounded-md {{ request()->routeIs('admin.tarifarios') ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">Tarifarios</a>
                <a href="{{ route('admin.seguros') }}" class="block px-3 py-2 text-xs rounded-md {{ request()->routeIs('admin.seguros') ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">Seguros</a>
                <a href="{{ route('admin.cuentas') }}" class="block px-3 py-2 text-xs rounded-md {{ request()->routeIs('admin.cuentas') ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">Cuentas por Cobrar</a>
            </div>
        </div>
        @endif

        @if(Auth::user()->isAdmin())
        <div x-data="{ open: {{ request()->is('farmacia*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center px-4 py-3 text-blue-100 hover:bg-blue-800/50 rounded-lg transition group">
                <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                <span class="text-sm font-medium flex-1 text-left">Logística</span>
                <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <div x-show="open" x-cloak class="pl-4 mt-1 space-y-1 border-l-2 border-blue-700/50 ml-6">
                <a href="{{ route('farmacia.index') }}" class="block px-3 py-2 text-xs rounded-md {{ request()->routeIs('farmacia.index') ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">Farmacia</a>
            </div>
        </div>
        @endif

        @if(Auth::user()->isAdmin())
        <div x-data="{ open: {{ request()->is('gerencial*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center px-4 py-3 text-blue-100 hover:bg-blue-800/50 rounded-lg transition group">
                <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                <span class="text-sm font-medium flex-1 text-left">Gerencial</span>
                <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <div x-show="open" x-cloak class="pl-4 mt-1 space-y-1 border-l-2 border-blue-700/50 ml-6">
                <a href="{{ route('gerencial.reportes') }}" class="block px-3 py-2 text-xs rounded-md {{ request()->routeIs('gerencial.reportes') ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">Reportes</a>
                <a href="{{ route('gerencial.kpis') }}" class="block px-3 py-2 text-xs rounded-md {{ request()->routeIs('gerencial.kpis') ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">KPIs</a>
            </div>
        </div>
        @endif

        @if(Auth::user()->isAdmin())
        <div x-data="{ open: {{ request()->is('seguridad*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center px-4 py-3 text-blue-100 hover:bg-blue-800/50 rounded-lg transition group">
                <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                <span class="text-sm font-medium flex-1 text-left">Seguridad</span>
                <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <div x-show="open" x-cloak class="pl-4 mt-1 space-y-1 border-l-2 border-blue-700/50 ml-6">
                <a href="{{ route('seguridad.usuarios.index') }}" class="block px-3 py-2 text-xs rounded-md {{ request()->routeIs('seguridad.usuarios.index') ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">Usuarios</a>
                <a href="{{ route('seguridad.auditoria.index') }}" class="block px-3 py-2 text-xs rounded-md {{ request()->routeIs('seguridad.auditoria.index') ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">Auditoría</a>
                <a href="{{ route('seguridad.configuracion.index') }}" class="block px-3 py-2 text-xs rounded-md {{ request()->routeIs('seguridad.configuracion.index') ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">Configuración</a>
            </div>
        </div>
        @endif

    </nav>

    <div class="p-4 border-t border-blue-800/50 bg-[#1a306d]">
        <div class="flex items-center gap-3 px-2">
            <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center text-sm font-bold text-white shadow-inner border border-blue-400/30">
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
</div>
