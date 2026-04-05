<div class="flex flex-col h-full w-full">

    <!-- Header del Sidebar (Logo y Título) -->
    <div class="h-16 shrink-0 flex items-center bg-black/10 border-b border-white/5 transition-all duration-300"
         :class="sidebarOpen ? 'px-6 justify-start' : 'px-0 justify-center'">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl shadow-lg shadow-blue-900/50 shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div x-show="sidebarOpen" x-transition.opacity.duration.200ms class="whitespace-nowrap">
                <h1 class="font-bold text-lg leading-tight uppercase tracking-wide text-white">HIS / CEMSA</h1>
                <p class="text-[10px] text-blue-300 uppercase tracking-widest font-bold">Sistema Clínico</p>
            </div>
        </div>
    </div>

    <!-- Navegación -->
    <nav class="flex-1 py-4 space-y-1.5 overflow-y-auto custom-scrollbar overflow-x-hidden"
         :class="sidebarOpen ? 'px-4' : 'px-2'">

        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}"
           class="flex items-center py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-900/20' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}"
           :class="sidebarOpen ? 'px-3 justify-start' : 'px-0 justify-center'">
            <svg class="w-6 h-6 shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"
                 :class="sidebarOpen ? 'mr-3' : 'mr-0'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="text-sm font-semibold whitespace-nowrap" x-show="sidebarOpen" x-transition.opacity.duration.200ms>Dashboard</span>
        </a>

        <!-- Recepción -->
        @if(Auth::user()->isReception())
        <a href="{{ route('reception') }}"
           class="flex items-center py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('reception') ? 'bg-blue-600 text-white shadow-md shadow-blue-900/20' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}"
           :class="sidebarOpen ? 'px-3 justify-start' : 'px-0 justify-center'">
            <svg class="w-6 h-6 shrink-0 {{ request()->routeIs('reception') ? 'text-white' : 'text-blue-400 group-hover:text-white' }}"
                 :class="sidebarOpen ? 'mr-3' : 'mr-0'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
            <span class="text-sm font-semibold whitespace-nowrap" x-show="sidebarOpen" x-transition.opacity.duration.200ms>Recepción</span>
        </a>
        @endif

        <!-- Dashboard Emergencia -->
        @if(Auth::user()->isEmergencia())
        <a href="{{ route('emergency-staff.dashboard') }}"
           class="flex items-center py-2.5 rounded-lg transition-all duration-200 group {{ request()->routeIs('emergency-staff.*') ? 'bg-red-600 text-white shadow-md shadow-red-900/20' : 'text-blue-100 hover:bg-white/10 hover:text-white' }}"
           :class="sidebarOpen ? 'px-3 justify-start' : 'px-0 justify-center'">
            <svg class="w-6 h-6 shrink-0 {{ request()->routeIs('emergency-staff.*') ? 'text-white' : 'text-red-400 group-hover:text-red-300' }}"
                 :class="sidebarOpen ? 'mr-3' : 'mr-0'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span class="text-sm font-semibold whitespace-nowrap" x-show="sidebarOpen" x-transition.opacity.duration.200ms>Dashboard Emergencia</span>
        </a>
        @endif

        <!-- Pacientes -->
        @if(Auth::user()->isAdmin() || Auth::user()->isDirMedico() || Auth::user()->isDoctor())
        <div x-data="{ open: {{ request()->is('patients*', 'consulta*', 'enfermeria*', 'uti*', 'quirofano*', 'hospitalizacion*', 'admin/emergencies*') ? 'true' : 'false' }} }">
            <button @click="if(!sidebarOpen) sidebarOpen = true; else open = !open"
                    class="w-full flex items-center py-2.5 text-blue-100 hover:bg-white/10 hover:text-white rounded-lg transition-all duration-200 group"
                    :class="sidebarOpen ? 'px-3 justify-start' : 'px-0 justify-center'">
                <svg class="w-6 h-6 shrink-0 text-blue-400 group-hover:text-white transition-colors"
                     :class="sidebarOpen ? 'mr-3' : 'mr-0'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span class="text-sm font-semibold flex-1 text-left whitespace-nowrap" x-show="sidebarOpen">Pacientes</span>
                <svg class="w-4 h-4 shrink-0 transition-transform duration-300 opacity-60" x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <div x-show="open && sidebarOpen" x-collapse class="pl-4 mt-1 mb-2 space-y-1 border-l border-blue-500/30 ml-5">
                @php
                    $pacientesLinks = [['r' => 'patients.index', 'l' => 'Maestro de Pacientes']];
                    if(Auth::user()->isAdmin() || Auth::user()->isDirMedico()):
                        $pacientesLinks = array_merge($pacientesLinks, [
                            ['r' => 'enfermeria.index', 'l' => 'Enfermería'],
                            ['r' => 'uti.admin.index', 'l' => 'UTI - Administración'],
                            ['r' => 'quirofano.index', 'l' => 'Quirófano'],
                            ['r' => 'hospitalizacion.index', 'l' => 'Hospitalización'],
                            ['r' => 'admin.emergencies.index', 'l' => 'Gestión de Emergencias (Solo Lectura)'],
                        ]);
                    elseif(Auth::user()->isDoctor()):
                        $pacientesLinks = array_merge($pacientesLinks, [
                            ['r' => 'consulta.index', 'l' => 'Consulta Externa'],
                            ['r' => 'consulta.historial-medico', 'l' => 'Historial de Consultas'],
                        ]);
                    endif;
                @endphp
                @foreach($pacientesLinks as $link)
                    <a href="{{ route($link['r']) }}" class="block px-3 py-2 text-[13px] rounded-md transition-all whitespace-nowrap {{ request()->routeIs($link['r']) ? 'text-white bg-blue-600/50 font-bold' : 'text-blue-200 hover:text-white hover:bg-white/5' }}">
                        {{ $link['l'] }}
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Caja Operativa -->
        @if(Auth::user()->isCaja() && !Auth::user()->isAdmin())
        <div x-data="{ open: {{ request()->is('caja-operativa*') ? 'true' : 'false' }} }">
            <button @click="if(!sidebarOpen) sidebarOpen = true; else open = !open"
                    class="w-full flex items-center py-2.5 text-blue-100 hover:bg-white/10 hover:text-white rounded-lg transition-all duration-200 group"
                    :class="sidebarOpen ? 'px-3 justify-start' : 'px-0 justify-center'">
                <svg class="w-6 h-6 shrink-0 text-emerald-400 group-hover:text-emerald-300 transition-colors"
                     :class="sidebarOpen ? 'mr-3' : 'mr-0'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a1 1 0 11-2 0 1 1 0 012 0z"/>
                </svg>
                <span class="text-sm font-semibold flex-1 text-left whitespace-nowrap" x-show="sidebarOpen">Caja Operativa</span>
                <svg class="w-4 h-4 shrink-0 transition-transform duration-300 opacity-60" x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <div x-show="open && sidebarOpen" x-collapse class="pl-4 mt-1 mb-2 space-y-1 border-l border-emerald-500/30 ml-5">
                <a href="{{ route('caja.operativa.index') }}" class="block px-3 py-2 text-[13px] rounded-md transition-all whitespace-nowrap {{ request()->routeIs('caja.operativa.*') ? 'text-white bg-emerald-600/50 font-bold' : 'text-blue-200 hover:text-white hover:bg-white/5' }}">Cobro de Pacientes</a>
            </div>
        </div>
        @endif

        <!-- Administración -->
        @if(Auth::user()->isAdmin())
        @php
            $cajasAbiertasCount = \App\Models\CajaSession::where('estado', 'abierta')->count();
        @endphp
        <div x-data="{ open: {{ request()->is('caja*', 'facturacion*', 'admin*') ? 'true' : 'false' }} }">
            <button @click="if(!sidebarOpen) sidebarOpen = true; else open = !open"
                    class="w-full flex items-center py-2.5 text-blue-100 hover:bg-white/10 hover:text-white rounded-lg transition-all duration-200 group"
                    :class="sidebarOpen ? 'px-3 justify-start' : 'px-0 justify-center'">
                <svg class="w-6 h-6 shrink-0 text-purple-400 group-hover:text-purple-300 transition-colors"
                     :class="sidebarOpen ? 'mr-3' : 'mr-0'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>

                <div class="flex-1 flex items-center justify-between" x-show="sidebarOpen">
                    <span class="text-sm font-semibold text-left whitespace-nowrap">Administración</span>
                    @if($cajasAbiertasCount > 0)
                        <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full animate-pulse mr-2 shadow-lg">
                            {{ $cajasAbiertasCount }} caja{{ $cajasAbiertasCount > 1 ? 's' : '' }}
                        </span>
                    @endif
                </div>

                <svg class="w-4 h-4 shrink-0 transition-transform duration-300 opacity-60" x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <div x-show="open && sidebarOpen" x-collapse class="pl-4 mt-1 mb-2 space-y-1 border-l border-purple-500/30 ml-5">
                <a href="{{ route('admin.especialidades.index') }}" class="block px-3 py-2 text-[13px] rounded-md transition-all whitespace-nowrap {{ request()->routeIs('admin.especialidades*') ? 'text-white bg-purple-600/50 font-bold' : 'text-blue-200 hover:text-white hover:bg-white/5' }}">Especialidades</a>
                <a href="{{ route('admin.doctors.index') }}" class="block px-3 py-2 text-[13px] rounded-md transition-all whitespace-nowrap {{ request()->routeIs('admin.doctors*') ? 'text-white bg-purple-600/50 font-bold' : 'text-blue-200 hover:text-white hover:bg-white/5' }}">Doctores</a>
                <a href="{{ route('admin.facturacion.index') }}" class="block px-3 py-2 text-[13px] rounded-md transition-all whitespace-nowrap {{ request()->routeIs('admin.facturacion.index') ? 'text-white bg-purple-600/50 font-bold' : 'text-blue-200 hover:text-white hover:bg-white/5' }}">Facturación</a>
                <a href="{{ route('admin.consulta-externa-gestion') }}" class="block px-3 py-2 text-[13px] rounded-md transition-all whitespace-nowrap {{ request()->routeIs('admin.consulta-externa-gestion') ? 'text-white bg-purple-600/50 font-bold' : 'text-blue-200 hover:text-white hover:bg-white/5' }}">Gestionar Consulta Externa</a>
                <a href="{{ route('admin.tarifarios') }}" class="block px-3 py-2 text-[13px] rounded-md transition-all whitespace-nowrap {{ request()->routeIs('admin.tarifarios') ? 'text-white bg-purple-600/50 font-bold' : 'text-blue-200 hover:text-white hover:bg-white/5' }}">Tarifarios</a>
                <a href="{{ route('admin.seguros') }}" class="block px-3 py-2 text-[13px] rounded-md transition-all whitespace-nowrap {{ request()->routeIs('admin.seguros') ? 'text-white bg-purple-600/50 font-bold' : 'text-blue-200 hover:text-white hover:bg-white/5' }}">Seguros</a>
                <a href="{{ route('admin.cuentas') }}" class="block px-3 py-2 text-[13px] rounded-md transition-all whitespace-nowrap {{ request()->routeIs('admin.cuentas') ? 'text-white bg-purple-600/50 font-bold' : 'text-blue-200 hover:text-white hover:bg-white/5' }}">Cuentas por Cobrar</a>
                <a href="{{ route('admin.almacen-medicamentos.index') }}" class="block px-3 py-2 text-[13px] rounded-md transition-all whitespace-nowrap {{ request()->routeIs('admin.almacen-medicamentos*') ? 'text-white bg-purple-600/50 font-bold' : 'text-blue-200 hover:text-white hover:bg-white/5' }}">Almacén de Medicamentos</a>
                <a href="{{ route('caja.gestion.index') }}" class="flex items-center justify-between px-3 py-2 text-[13px] rounded-md transition-all whitespace-nowrap {{ request()->routeIs('caja.gestion.*') ? 'text-white bg-purple-600/50 font-bold' : 'text-blue-200 hover:text-white hover:bg-white/5' }}">
                    <span>Control de Caja</span>
                    @if($cajasAbiertasCount > 0)
                        <span class="bg-emerald-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full animate-pulse">
                            {{ $cajasAbiertasCount }}
                        </span>
                    @endif
                </a>
            </div>
        </div>
        @endif

        <!-- Farmacia (Admin) -->
        @if(Auth::user()->isAdmin())
        <div x-data="{ open: {{ request()->is('farmacia/reporte*') ? 'true' : 'false' }} }">
            <button @click="if(!sidebarOpen) sidebarOpen = true; else open = !open"
                    class="w-full flex items-center py-2.5 text-blue-100 hover:bg-white/10 hover:text-white rounded-lg transition-all duration-200 group"
                    :class="sidebarOpen ? 'px-3 justify-start' : 'px-0 justify-center'">
                <svg class="w-6 h-6 shrink-0 text-blue-400 group-hover:text-blue-300 transition-colors"
                     :class="sidebarOpen ? 'mr-3' : 'mr-0'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <span class="text-sm font-semibold flex-1 text-left whitespace-nowrap" x-show="sidebarOpen">Farmacia</span>
                <svg class="w-4 h-4 shrink-0 transition-transform duration-300 opacity-60" x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <div x-show="open && sidebarOpen" x-collapse class="pl-4 mt-1 mb-2 space-y-1 border-l border-blue-500/30 ml-5">
                <a href="{{ route('farmacia.reporte') }}" class="block px-3 py-2 text-[13px] rounded-md transition-all whitespace-nowrap {{ request()->routeIs('farmacia.reporte') ? 'text-white bg-blue-600/50 font-bold' : 'text-blue-200 hover:text-white hover:bg-white/5' }}">Reporte</a>
            </div>
        </div>
        @endif

        <!-- UTI - Terapia Intensiva -->
        @if(Auth::user()->isUti())
        <div x-data="{ open: {{ request()->is('uti*') ? 'true' : 'false' }} }">
            <button @click="if(!sidebarOpen) sidebarOpen = true; else open = !open"
                    class="w-full flex items-center py-2.5 text-blue-100 hover:bg-white/10 hover:text-white rounded-lg transition-all duration-200 group"
                    :class="sidebarOpen ? 'px-3 justify-start' : 'px-0 justify-center'">
                <svg class="w-6 h-6 shrink-0 text-cyan-400 group-hover:text-cyan-300 transition-colors"
                     :class="sidebarOpen ? 'mr-3' : 'mr-0'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <span class="text-sm font-semibold flex-1 text-left whitespace-nowrap" x-show="sidebarOpen">UTI - Terapia Intensiva</span>
                <svg class="w-4 h-4 shrink-0 transition-transform duration-300 opacity-60" x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <div x-show="open && sidebarOpen" x-collapse class="pl-4 mt-1 mb-2 space-y-1 border-l border-cyan-500/30 ml-5">
                <a href="{{ route('uti.operativa.index') }}" class="block px-3 py-2 text-[13px] rounded-md transition-all whitespace-nowrap {{ request()->routeIs('uti.operativa.*') ? 'text-white bg-cyan-600/50 font-bold' : 'text-blue-200 hover:text-white hover:bg-white/5' }}">Panel de Pacientes</a>
            </div>
        </div>
        @endif

        <!-- Farmacia (Operativa) -->
        @if(Auth::user()->isFarmacia())
        <div x-data="{ open: {{ request()->is('farmacia*') ? 'true' : 'false' }} }">
            <button @click="if(!sidebarOpen) sidebarOpen = true; else open = !open"
                    class="w-full flex items-center py-2.5 text-blue-100 hover:bg-white/10 hover:text-white rounded-lg transition-all duration-200 group"
                    :class="sidebarOpen ? 'px-3 justify-start' : 'px-0 justify-center'">
                <svg class="w-6 h-6 shrink-0 text-yellow-400 group-hover:text-yellow-300 transition-colors"
                     :class="sidebarOpen ? 'mr-3' : 'mr-0'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <span class="text-sm font-semibold flex-1 text-left whitespace-nowrap" x-show="sidebarOpen">Farmacia</span>
                <svg class="w-4 h-4 shrink-0 transition-transform duration-300 opacity-60" x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <div x-show="open && sidebarOpen" x-collapse class="pl-4 mt-1 mb-2 space-y-1 border-l border-yellow-500/30 ml-5">
                <a href="{{ route('farmacia.index') }}" class="block px-3 py-2 text-[13px] rounded-md transition-all whitespace-nowrap {{ request()->routeIs('farmacia.index') ? 'text-white bg-yellow-600/50 font-bold' : 'text-blue-200 hover:text-white hover:bg-white/5' }}">Dashboard</a>
                <a href="{{ route('farmacia.pos') }}" class="block px-3 py-2 text-[13px] rounded-md transition-all whitespace-nowrap {{ request()->routeIs('farmacia.pos') ? 'text-white bg-yellow-600/50 font-bold' : 'text-blue-200 hover:text-white hover:bg-white/5' }}">Punto de Venta</a>
                <a href="{{ route('farmacia.inventario') }}" class="block px-3 py-2 text-[13px] rounded-md transition-all whitespace-nowrap {{ request()->routeIs('farmacia.inventario') ? 'text-white bg-yellow-600/50 font-bold' : 'text-blue-200 hover:text-white hover:bg-white/5' }}">Inventario</a>
                <a href="{{ route('farmacia.ventas') }}" class="block px-3 py-2 text-[13px] rounded-md transition-all whitespace-nowrap {{ request()->routeIs('farmacia.ventas') ? 'text-white bg-yellow-600/50 font-bold' : 'text-blue-200 hover:text-white hover:bg-white/5' }}">Ventas</a>
                <a href="{{ route('farmacia.clientes') }}" class="block px-3 py-2 text-[13px] rounded-md transition-all whitespace-nowrap {{ request()->routeIs('farmacia.clientes') ? 'text-white bg-yellow-600/50 font-bold' : 'text-blue-200 hover:text-white hover:bg-white/5' }}">Clientes</a>
                <a href="{{ route('farmacia.reporte') }}" class="block px-3 py-2 text-[13px] rounded-md transition-all whitespace-nowrap {{ request()->routeIs('farmacia.reporte') ? 'text-white bg-yellow-600/50 font-bold' : 'text-blue-200 hover:text-white hover:bg-white/5' }}">Reporte</a>
            </div>
        </div>
        @endif

        <!-- Gerencial -->
        @if(Auth::user()->isAdmin() || Auth::user()->isGerente())
        <div x-data="{ open: {{ request()->is('gerencial*') ? 'true' : 'false' }} }">
            <button @click="if(!sidebarOpen) sidebarOpen = true; else open = !open"
                    class="w-full flex items-center py-2.5 text-blue-100 hover:bg-white/10 hover:text-white rounded-lg transition-all duration-200 group"
                    :class="sidebarOpen ? 'px-3 justify-start' : 'px-0 justify-center'">
                <svg class="w-6 h-6 shrink-0 text-orange-400 group-hover:text-orange-300 transition-colors"
                     :class="sidebarOpen ? 'mr-3' : 'mr-0'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span class="text-sm font-semibold flex-1 text-left whitespace-nowrap" x-show="sidebarOpen">Gerencial</span>
                <svg class="w-4 h-4 shrink-0 transition-transform duration-300 opacity-60" x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <div x-show="open && sidebarOpen" x-collapse class="pl-4 mt-1 mb-2 space-y-1 border-l border-orange-500/30 ml-5">
                <a href="{{ route('gerencial.reportes') }}" class="block px-3 py-2 text-[13px] rounded-md transition-all whitespace-nowrap {{ request()->routeIs('gerencial.reportes') ? 'text-white bg-orange-600/50 font-bold' : 'text-blue-200 hover:text-white hover:bg-white/5' }}">Reportes</a>
                <a href="{{ route('gerencial.kpis') }}" class="block px-3 py-2 text-[13px] rounded-md transition-all whitespace-nowrap {{ request()->routeIs('gerencial.kpis') ? 'text-white bg-orange-600/50 font-bold' : 'text-blue-200 hover:text-white hover:bg-white/5' }}">KPIs</a>
            </div>
        </div>
        @endif

        <!-- Seguridad -->
        @if(Auth::user()->isAdmin() || Auth::user()->isGerente())
        <div x-data="{ open: {{ request()->is('seguridad*', 'user-management*') ? 'true' : 'false' }} }">
            <button @click="if(!sidebarOpen) sidebarOpen = true; else open = !open"
                    class="w-full flex items-center py-2.5 text-blue-100 hover:bg-white/10 hover:text-white rounded-lg transition-all duration-200 group"
                    :class="sidebarOpen ? 'px-3 justify-start' : 'px-0 justify-center'">
                <svg class="w-6 h-6 shrink-0 text-slate-400 group-hover:text-slate-300 transition-colors"
                     :class="sidebarOpen ? 'mr-3' : 'mr-0'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <span class="text-sm font-semibold flex-1 text-left whitespace-nowrap" x-show="sidebarOpen">Seguridad</span>
                <svg class="w-4 h-4 shrink-0 transition-transform duration-300 opacity-60" x-show="sidebarOpen" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <div x-show="open && sidebarOpen" x-collapse class="pl-4 mt-1 mb-2 space-y-1 border-l border-slate-500/30 ml-5">
                <a href="{{ route('user-management.index') }}" class="block px-3 py-2 text-[13px] rounded-md transition-all whitespace-nowrap {{ request()->routeIs('user-management*') ? 'text-white bg-slate-600/50 font-bold' : 'text-blue-200 hover:text-white hover:bg-white/5' }}">
                    Gestión de Usuarios
                </a>
                <a href="{{ route('seguridad.activity-logs.index') }}" class="block px-3 py-2 text-[13px] rounded-md transition-all whitespace-nowrap {{ request()->routeIs('seguridad.activity-logs.index') ? 'text-white bg-slate-600/50 font-bold' : 'text-blue-200 hover:text-white hover:bg-white/5' }}">
                    Bitácora de Actividades
                </a>
            </div>
        </div>
        @endif

    </nav>

    <!-- Footer de Usuario en Sidebar (Inferior) -->
    <div class="py-4 bg-black/20 border-t border-white/5 shrink-0 transition-all duration-300"
         :class="sidebarOpen ? 'px-4' : 'px-0 flex justify-center'">
        <div class="flex items-center gap-3">
            <div class="relative shrink-0">
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-500 to-blue-400 flex items-center justify-center text-sm font-bold text-white shadow-lg ring-2 ring-white/10">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
                <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-[#112048] rounded-full"></div>
            </div>
            <div class="text-sm overflow-hidden flex-1 whitespace-nowrap" x-show="sidebarOpen" x-transition.opacity.duration.200ms>
                <p class="font-bold text-white truncate">{{ Auth::user()->name ?? 'Usuario' }}</p>
                <div class="flex items-center gap-1.5">
                    <span class="text-[11px] text-blue-300 font-medium">En línea</span>
                </div>
            </div>
        </div>
    </div>
</div>
