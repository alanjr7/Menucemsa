<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Clínica CEMSA') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
            .custom-scrollbar::-webkit-scrollbar { width: 4px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.2); border-radius: 10px; }
            .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.3); }
        </style>
    </head>

    <body class="font-sans antialiased text-slate-700 bg-slate-50"
          x-data="{ sidebarOpen: window.innerWidth >= 1024, isMobile: window.innerWidth < 1024 }"
          @resize.window="isMobile = window.innerWidth < 1024; if(isMobile) sidebarOpen = false;">

        <div class="flex min-h-screen relative">

            <!-- Fondo oscuro para móviles -->
            <div x-show="sidebarOpen && isMobile"
                 x-cloak
                 @click="sidebarOpen = false"
                 x-transition.opacity.duration.300ms
                 class="fixed inset-0 bg-slate-900/60 z-40 backdrop-blur-sm lg:hidden">
            </div>

            <!-- Sidebar (w-72 abierto, w-20 cerrado en PC) -->
            <aside class="fixed inset-y-0 left-0 z-50 flex flex-col h-full bg-gradient-to-b from-[#1a306d] to-[#112048] shadow-2xl text-white transition-all duration-300 ease-in-out overflow-hidden"
                   :class="isMobile ? (sidebarOpen ? 'translate-x-0 w-72' : '-translate-x-full w-72') : (sidebarOpen ? 'translate-x-0 w-72' : 'translate-x-0 w-20')">
                @include('layouts.navigation')
            </aside>

            <!-- Contenedor Principal (Ajusta margen ml-72 o ml-20 en PC) -->
            <div class="flex-1 flex flex-col min-w-0 transition-all duration-300 ease-in-out"
                 :class="isMobile ? 'ml-0' : (sidebarOpen ? 'ml-72' : 'ml-20')">

                <!-- Header -->
                <header class="bg-white/90 backdrop-blur-md shadow-sm border-b border-slate-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 sticky top-0 z-30">
                    <div class="flex items-center gap-3 sm:gap-4">
                        <button @click="sidebarOpen = !sidebarOpen"
                                class="p-2 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white transition-all duration-200 shadow-sm focus:outline-none">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        <div class="hidden sm:flex flex-col justify-center">
                            <div class="text-slate-800 font-bold text-lg leading-tight flex items-center gap-2">
                                🏥 Clínica CEMSA
                            </div>
                            <span class="text-xs text-blue-600 font-semibold uppercase tracking-wider">Sede Principal</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 sm:gap-5">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-bold text-slate-800 leading-none">
                                {{ Auth::user()->name }}
                            </p>
                            <div class="flex items-center justify-end gap-1.5 mt-1">
                                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                <p class="text-[10px] text-green-600 font-bold uppercase tracking-wider">En línea</p>
                            </div>
                        </div>

                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="w-10 h-10 bg-gradient-to-tr from-blue-700 to-blue-500 rounded-full flex items-center justify-center text-white font-bold ring-2 ring-blue-100 shadow-md hover:scale-105 transition-all">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </button>

                            <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-3 w-48 rounded-xl shadow-xl bg-white ring-1 ring-slate-900/5 z-50">
                                <div class="py-1">
                                    <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50">Mi Perfil</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">Cerrar Sesión</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    <div class="w-full mx-auto max-w-7xl">
                        @yield('content')
                    </div>
                </main>

            </div>
        </div>

        @stack('scripts')
    </body>
</html>