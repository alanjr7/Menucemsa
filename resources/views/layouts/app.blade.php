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

    <body class="font-sans antialiased text-gray-900 bg-gray-100">
        <div class="flex min-h-screen">

            <aside class="w-64 bg-[#1e3a8a] fixed h-full z-20 shadow-2xl text-white">
                @include('layouts.navigation')
            </aside>

            <div class="flex-1 flex flex-col ml-64">

                <header class="bg-white shadow-sm h-16 flex items-center justify-between px-8 sticky top-0 z-10">
                    <div class="text-blue-900 font-medium tracking-tight">
                        <span class="font-bold text-lg">🏥 Clínica CEMSA</span>
                        <span class="mx-2 text-gray-300">|</span>
                        <span class="text-sm text-gray-500 uppercase font-semibold">Sede Principal</span>
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
                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold border-2 border-blue-50 shadow-sm uppercase">
                            {{ substr(Auth::user()->name, 0, 1) }}
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
