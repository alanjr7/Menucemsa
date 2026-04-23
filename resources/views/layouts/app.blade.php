<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Clínica CEMSA') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
            
            /* Custom Scrollbar */
            .custom-scrollbar::-webkit-scrollbar { width: 4px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
            .custom-scrollbar::-webkit-scrollbar-thumb { 
                background: rgba(255, 255, 255, 0.2); 
                border-radius: 10px; 
            }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover { 
                background: rgba(255, 255, 255, 0.3); 
            }
        </style>
    </head>

    <!-- 
      Alpine.js State:
      Manejamos únicamente si el sidebar está abierto o cerrado.
      El CSS (lg:) se encarga de saber si estamos en móvil o escritorio.
    -->
    <body class="font-sans antialiased text-slate-700 bg-slate-50"
          x-data="{ sidebarOpen: window.innerWidth >= 1024 }"
          @resize.window.debounce.100ms="if (window.innerWidth < 1024) sidebarOpen = false">

        <div class="flex relative min-h-screen">

            <!-- Overlay oscuro para móviles -->
            <div x-show="sidebarOpen"
                 x-cloak
                 @click="sidebarOpen = false"
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden"
                 aria-hidden="true">
            </div>

            <!-- Sidebar -->
            <aside class="fixed inset-y-0 left-0 z-50 flex flex-col w-72 h-full overflow-hidden text-white transition-all duration-300 ease-in-out bg-gradient-to-b from-[#1a306d] to-[#112048] shadow-2xl"
                   :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0 lg:w-20'">
                @include('layouts.navigation')
            </aside>

            <!-- Contenedor Principal (Ajusta su margen izquierdo en base al Sidebar en PC) -->
            <div class="flex flex-col flex-1 min-w-0 transition-all duration-300 ease-in-out"
                 :class="sidebarOpen ? 'lg:ml-72' : 'lg:ml-20'">

                <!-- Header -->
                <header class="sticky top-0 z-30 flex items-center justify-between h-16 px-4 bg-white/90 backdrop-blur-md border-b border-slate-200 shadow-sm sm:px-6 lg:px-8">
                    
                    <!-- Lado Izquierdo Header -->
                    <div class="flex items-center gap-3 sm:gap-4">
                        <button @click="sidebarOpen = !sidebarOpen"
                                type="button"
                                class="p-2 text-blue-700 transition-all duration-200 rounded-lg bg-blue-50 hover:bg-blue-600 hover:text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                                :aria-expanded="sidebarOpen.toString()">
                            <span class="sr-only">Alternar menú</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        <div class="hidden sm:flex flex-col justify-center">
                            <h1 class="flex items-center gap-2 text-lg font-bold leading-tight text-slate-800">
                                🏥 Clínica CEMSA
                            </h1>
                            <span class="text-xs font-semibold tracking-wider text-blue-600 uppercase">
                                Sede Principal
                            </span>
                        </div>
                    </div>

                    <!-- Lado Derecho Header -->
                    <div class="flex items-center gap-3 sm:gap-5">

                        <!-- Widget de Notificaciones -->
                        <div x-data="sistemaNotificaciones()" class="relative" x-cloak>
                            <button @click="mostrar = !mostrar" type="button" class="relative p-2 text-slate-500 hover:text-slate-700 transition rounded-lg hover:bg-slate-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <span x-show="total > 0" x-text="total" class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center animate-pulse"></span>
                            </button>

                            <!-- Dropdown de Notificaciones -->
                            <div x-show="mostrar" @click.away="mostrar = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-96 bg-white rounded-xl shadow-2xl ring-1 ring-slate-900/5 z-50 overflow-hidden">
                                <div class="px-4 py-3 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Notificaciones</p>
                                    <button x-show="total > 0" @click="marcarTodas" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                        Marcar todas
                                    </button>
                                </div>
                                <div class="max-h-80 overflow-y-auto">
                                    <template x-if="notificaciones.length === 0">
                                        <div class="px-4 py-8 text-center">
                                            <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                            </svg>
                                            <p class="text-sm text-slate-400">Sin notificaciones nuevas</p>
                                        </div>
                                    </template>
                                    <template x-for="n in notificaciones" :key="n.id">
                                        <div class="group flex items-start gap-3 px-4 py-3 hover:bg-slate-50 transition border-b border-slate-50 last:border-0">
                                            <!-- Área clickeable para marcar como leída -->
                                            <div @click="marcarLeida(n.id)" class="flex-1 flex items-start gap-3 cursor-pointer">
                                                <div :class="iconBg(n.color)" class="p-1.5 rounded-lg flex-shrink-0">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="iconPath(n.icon)"></path>
                                                    </svg>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-semibold text-slate-800" x-text="n.title"></p>
                                                    <p class="text-xs text-slate-600 line-clamp-2" x-text="n.message"></p>
                                                    <p class="text-[10px] text-slate-400 mt-1" x-text="n.created_at"></p>
                                                </div>
                                            </div>
                                            <!-- Botón alternativo para marcar como leída -->
                                            <button @click.stop="marcarLeida(n.id)" class="opacity-0 group-hover:opacity-100 p-1.5 text-slate-400 hover:text-green-600 hover:bg-green-50 rounded-lg transition flex-shrink-0" title="Marcar como leída">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <script>
                        function sistemaNotificaciones() {
                            return {
                                notificaciones: [],
                                total: 0,
                                mostrar: false,

                                iconBg(color) {
                                    const map = {
                                        'danger': 'bg-red-100 text-red-600',
                                        'warning': 'bg-amber-100 text-amber-600',
                                        'success': 'bg-green-100 text-green-600',
                                        'info': 'bg-blue-100 text-blue-600'
                                    };
                                    return map[color] || map.info;
                                },

                                iconPath(icon) {
                                    const paths = {
                                        'user-plus': 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
                                        'ambulance': 'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01',
                                        'calendar': 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                                        'credit-card': 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
                                        'exclamation-triangle': 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
                                        'clipboard-list': 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6 4h3',
                                        'clock': 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                                        'dollar-sign': 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                                        'alert-circle': 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                                        'arrow-right': 'M14 5l7 7m0 0l-7 7m7-7H3',
                                        'bell': 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'
                                    };
                                    return paths[icon] || paths.bell;
                                },

                                async cargar() {
                                    try {
                                        const r = await fetch('{{ route("notificaciones.index") }}');
                                        const d = await r.json();
                                        this.notificaciones = d.notifications ?? [];
                                        this.total = d.total ?? 0;
                                    } catch(e) { console.error('Error cargando notificaciones:', e); }
                                },

                                async marcarLeida(id) {
                                    try {
                                        const r = await fetch('{{ url("/api/notificaciones") }}/' + id + '/leer', {
                                            method: 'POST',
                                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                        });
                                        if (r.ok) {
                                            this.notificaciones = this.notificaciones.filter(n => n.id !== id);
                                            this.total = Math.max(0, this.total - 1);
                                        }
                                    } catch(e) { console.error('Error marcando notificación:', e); }
                                },

                                async marcarTodas() {
                                    if (!confirm('¿Marcar todas las notificaciones como leídas?')) return;
                                    try {
                                        const r = await fetch('{{ route("notificaciones.leer-todas") }}', {
                                            method: 'POST',
                                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                        });
                                        if (r.ok) {
                                            this.notificaciones = [];
                                            this.total = 0;
                                        }
                                    } catch(e) { console.error('Error marcando todas:', e); }
                                },

                                init() {
                                    this.cargar();
                                    setInterval(() => this.cargar(), 30000);
                                }
                            }
                        }
                        </script>

                        <!-- Info de Usuario (Solo PC) -->
                        <div class="hidden text-right sm:block">
                            <p class="text-sm font-bold leading-none text-slate-800">
                                {{ Auth::user()->name }}
                            </p>
                            <div class="flex items-center justify-end gap-1.5 mt-1">
                                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                <p class="text-[10px] font-bold tracking-wider text-green-600 uppercase">En línea</p>
                            </div>
                        </div>

                        <!-- Dropdown de Perfil -->
                        <div x-data="{ dropdownOpen: false }" class="relative">
                            <button @click="dropdownOpen = !dropdownOpen" 
                                    @click.away="dropdownOpen = false"
                                    type="button"
                                    class="flex items-center justify-center w-10 h-10 font-bold text-white transition-all rounded-full shadow-md bg-gradient-to-tr from-blue-700 to-blue-500 ring-2 ring-blue-100 hover:scale-105 focus:outline-none focus:ring-offset-2 focus:ring-blue-500"
                                    :aria-expanded="dropdownOpen.toString()">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </button>

                            <!-- Menú Desplegable -->
                            <div x-show="dropdownOpen" 
                                 x-cloak
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                                 class="absolute right-0 z-50 w-48 mt-3 origin-top-right bg-white rounded-xl shadow-lg ring-1 ring-slate-900/5 focus:outline-none">
                                
                                <div class="py-1">
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2.5 text-sm font-medium text-slate-700 transition-colors hover:bg-slate-50 hover:text-blue-600">
                                        Mi Perfil
                                    </a>
                                    
                                    <hr class="border-slate-100">

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full px-4 py-2.5 text-sm font-medium text-left text-red-600 transition-colors hover:bg-red-50 hover:text-red-700">
                                            Cerrar Sesión
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Contenido Principal -->
                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    <div class="w-full max-w-7xl mx-auto">
                        @yield('content')
                    </div>
                </main>

            </div>
        </div>

        @stack('scripts')
    </body>
</html>