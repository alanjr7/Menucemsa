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