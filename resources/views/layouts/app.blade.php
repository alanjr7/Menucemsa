<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
            /* Estilo para personalizar el scroll del menú si es muy largo */
            .custom-scrollbar::-webkit-scrollbar { width: 4px; }
            .custom-scrollbar::-webkit-scrollbar-track { background: #1e3a8a; }
            .custom-scrollbar::-webkit-scrollbar-thumb { background: #3b82f6; border-radius: 10px; }
        </style>
    </head>

    <body class="font-sans antialiased text-gray-900 bg-gray-100" x-data="{ sidebarOpen: true }">
        <div class="flex min-h-screen">

            <aside class="bg-[#1e3a8a] fixed h-full shadow-2xl text-white transition-all duration-300 ease-in-out z-10" 
                   :class="sidebarOpen ? 'w-64' : 'w-0 overflow-hidden'">
                @include('layouts.navigation')
            </aside>

            <div class="flex-1 flex flex-col transition-all duration-300 ease-in-out" 
                 :class="sidebarOpen ? 'ml-64' : 'ml-0'">

                <header class="bg-white shadow-sm h-16 flex items-center justify-between px-8 sticky top-0 z-10">
                    <div class="flex items-center gap-4">
                        <button @click="sidebarOpen = !sidebarOpen" 
                                class="p-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors shadow-md relative z-20">
                            <!-- Hamburger icon when sidebar is open -->
                            <svg x-show="sidebarOpen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                            </svg>
                            <!-- Menu icon when sidebar is closed -->
                            <svg x-show="!sidebarOpen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <div class="text-blue-900 font-medium tracking-tight">
                            <span class="font-bold text-lg">🏥 Clínica CEMSA</span>
                            <span class="mx-2 text-gray-300">|</span>
                            <span class="text-sm text-gray-500 uppercase font-semibold">Sede Principal</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-bold text-gray-800 leading-none">
                                {{ Auth::user()->name }}
                            </p>
                            <p class="text-[10px] text-green-600 font-bold uppercase mt-1 tracking-wider">
                                En línea
                            </p>
                        </div>
                        
                        <!-- Dropdown menu for user actions -->
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold border-2 border-blue-50 shadow-sm uppercase hover:bg-blue-700 transition-colors">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </button>
                            
                            <div x-show="open" 
                                 x-cloak
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        Perfil
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}" class="block">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                            Cerrar Sesión
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <main class="p-8">
                    <div class="max-w-7xl mx-auto">
                        {{ $slot }}
                    </div>
                </main>

                <footer class="mt-auto py-4 px-8 text-center text-xs text-gray-400">
                    &copy; {{ date('Y') }} - HIS / ERP CEMSA - Todos los derechos reservados.
                </footer>
            </div>
        </div>


    </body>
</html>
