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

        @if(Auth::user()->isReception())
        <a href="{{ route('reception') }}"
           class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('reception') ? 'bg-blue-600 text-white shadow-md' : 'text-blue-100 hover:bg-blue-800/50' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            <span class="text-sm font-medium">Recepción</span>
        </a>
        @endif


        @if(Auth::user()->isEmergencia())
        <a href="{{ route('emergency-staff.dashboard') }}"
           class="flex items-center px-4 py-3 rounded-lg transition {{ request()->routeIs('emergency-staff.*') ? 'bg-red-600 text-white shadow-md' : 'text-blue-100 hover:bg-blue-800/50' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span class="text-sm font-medium">Dashboard Emergencia</span>
        </a>
        @endif

        @if(Auth::user()->isAdmin())
        <div x-data="{ open: {{ request()->is('admin/emergencies*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center px-4 py-3 text-blue-100 hover:bg-blue-800/50 rounded-lg transition group">
                <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span class="text-sm font-medium flex-1 text-left">Emergencias</span>
                <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <div x-show="open" x-cloak class="pl-4 mt-1 space-y-1 border-l-2 border-blue-700/50 ml-6">
                <a href="{{ route('admin.emergencies.index') }}" class="block px-3 py-2 text-xs rounded-md {{ request()->routeIs('admin.emergencies.index') ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">
                    Gestión de Emergencias (Solo Lectura)
                </a>
            </div>
        </div>
        @endif

        <hr class="border-blue-800/50 my-2 mx-4">

        @if(Auth::user()->isAdmin() || Auth::user()->isDirMedico() || Auth::user()->isDoctor())
        <div x-data="{ open: {{ request()->is('patients*', 'consulta*', 'enfermeria*', 'uti*', 'quirofano*', 'hospitalizacion*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center px-4 py-3 text-blue-100 hover:bg-blue-800/50 rounded-lg transition group">
                <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <span class="text-sm font-medium flex-1 text-left">Pacientes</span>
                <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <div x-show="open" x-cloak class="pl-4 mt-1 space-y-1 border-l-2 border-blue-700/50 ml-6">
                @php
                    $pacientesLinks = [
                        ['r' => 'patients.index', 'l' => 'Maestro de Pacientes'],
                    ];

                    // Admin y Director Médico pueden ver todas las áreas médicas excepto emergencias
                    if(Auth::user()->isAdmin() || Auth::user()->isDirMedico()):
                        $pacientesLinks = array_merge($pacientesLinks, [
                            ['r' => 'enfermeria.index', 'l' => 'Enfermería'],
                            ['r' => 'uti.index', 'l' => 'UTI'],
                            ['r' => 'quirofano.index', 'l' => 'Quirófano'],
                            ['r' => 'hospitalizacion.index', 'l' => 'Hospitalización'],
                        ]);
                    // Doctor role puede ver Consulta Externa y Historial de Consultas
                    elseif(Auth::user()->isDoctor()):
                        $pacientesLinks = array_merge($pacientesLinks, [
                            ['r' => 'consulta.index', 'l' => 'Consulta Externa'],
                            ['r' => 'consulta.historial-medico', 'l' => 'Historial de Consultas'],
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
                <a href="{{ route('admin.especialidades.index') }}" class="block px-3 py-2 text-xs rounded-md {{ request()->routeIs('admin.especialidades*') ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">Especialidades</a>
                <a href="{{ route('admin.doctors.index') }}" class="block px-3 py-2 text-xs rounded-md {{ request()->routeIs('admin.doctors*') ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">Doctores</a>
                <a href="{{ route('admin.caja.index') }}" class="block px-3 py-2 text-xs rounded-md {{ request()->routeIs('admin.caja.index') ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">Caja Central</a>
                <a href="{{ route('admin.facturacion.index') }}" class="block px-3 py-2 text-xs rounded-md {{ request()->routeIs('admin.facturacion.index') ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">Facturación</a>
                <a href="{{ route('admin.consulta-externa-gestion') }}" class="block px-3 py-2 text-xs rounded-md {{ request()->routeIs('admin.consulta-externa-gestion') ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">Gestionar Consulta Externa</a>
                <a href="{{ route('admin.tarifarios') }}" class="block px-3 py-2 text-xs rounded-md {{ request()->routeIs('admin.tarifarios') ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">Tarifarios</a>
                <a href="{{ route('admin.seguros') }}" class="block px-3 py-2 text-xs rounded-md {{ request()->routeIs('admin.seguros') ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">Seguros</a>
                <a href="{{ route('admin.cuentas') }}" class="block px-3 py-2 text-xs rounded-md {{ request()->routeIs('admin.cuentas') ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">Cuentas por Cobrar</a>
                <a href="{{ route('admin.almacen-medicamentos.index') }}" class="block px-3 py-2 text-xs rounded-md {{ request()->routeIs('admin.almacen-medicamentos*') ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">Almacén de Medicamentos</a>
            </div>
        </div>
        @endif

        @if(Auth::user()->isAdmin())
        <div x-data="{ open: {{ request()->is('farmacia*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center px-4 py-3 text-blue-100 hover:bg-blue-800/50 rounded-lg transition group">
                <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                <span class="text-sm font-medium flex-1 text-left">Farmacia</span>
                <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <div x-show="open" x-cloak class="pl-4 mt-1 space-y-1 border-l-2 border-blue-700/50 ml-6">
                <a href="{{ route('farmacia.reporte') }}" class="block px-3 py-2 text-xs rounded-md {{ request()->routeIs('farmacia.reporte') ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">Reporte</a>
            </div>
        </div>
        @endif

        @if(Auth::user()->isFarmacia())
        <div x-data="{ open: {{ request()->is('farmacia*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center px-4 py-3 text-blue-100 hover:bg-blue-800/50 rounded-lg transition group">
                <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                <span class="text-sm font-medium flex-1 text-left">Farmacia</span>
                <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <div x-show="open" x-cloak class="pl-4 mt-1 space-y-1 border-l-2 border-blue-700/50 ml-6">
                <a href="{{ route('farmacia.index') }}" class="block px-3 py-2 text-xs rounded-md {{ request()->routeIs('farmacia.index') ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">Dashboard</a>
                <a href="{{ route('farmacia.pos') }}"
                   class="block px-3 py-2 text-xs rounded-md {{ request()->routeIs('farmacia.pos') ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">
                   Punto de Venta
                </a>
                <a href="{{ route('farmacia.inventario') }}" class="block px-3 py-2 text-xs rounded-md {{ request()->routeIs('farmacia.inventario') ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">Inventario</a>
                <a href="{{ route('farmacia.ventas') }}" class="block px-3 py-2 text-xs rounded-md {{ request()->routeIs('farmacia.ventas') ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">Ventas</a>
                <a href="{{ route('farmacia.clientes') }}" class="block px-3 py-2 text-xs rounded-md {{ request()->routeIs('farmacia.clientes') ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">Clientes</a>
                <a href="{{ route('farmacia.reporte') }}" class="block px-3 py-2 text-xs rounded-md {{ request()->routeIs('farmacia.reporte') ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">Reporte</a>
            </div>
        </div>
        @endif

        @if(Auth::user()->isAdmin() || Auth::user()->isGerente())
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

        @if(Auth::user()->isAdmin() || Auth::user()->isGerente())
        <div x-data="{ open: {{ request()->is('seguridad*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center px-4 py-3 text-blue-100 hover:bg-blue-800/50 rounded-lg transition group">
                <svg class="w-5 h-5 mr-3 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                <span class="text-sm font-medium flex-1 text-left">Seguridad</span>
                <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </button>
            <div x-show="open" x-cloak class="pl-4 mt-1 space-y-1 border-l-2 border-blue-700/50 ml-6">
                <a href="{{ route('user-management.index') }}" class="block px-3 py-2 text-xs rounded-md {{ request()->routeIs('user-management*') ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">
                    Gestión de Usuarios
                </a>
                <a href="{{ route('seguridad.activity-logs.index') }}" class="block px-3 py-2 text-xs rounded-md {{ request()->routeIs('seguridad.activity-logs.index') ? 'text-white bg-blue-700/60 font-bold' : 'text-blue-200 hover:text-white hover:bg-blue-800' }}">
                    Bitácora de Actividades
                </a>
               
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
