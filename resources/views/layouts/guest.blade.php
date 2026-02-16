<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>CEMSA - {{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts: Inter para el estilo profesional -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body { font-family: 'Inter', sans-serif; }
            /* Azul CEMSA exacto */
            .bg-cemsa { background-color: #0b44a8; }
        </style>
    </head>
    <body class="bg-cemsa text-gray-900 antialiased min-h-screen flex flex-col justify-center items-center p-4">
        
        <!-- Slot Principal: Aquí se inyectará el contenido del login -->
        <div class="w-full flex flex-col items-center justify-center">
            {{ $slot }}
        </div>

        <!-- Footer Global -->
        <div class="mt-8 text-center text-blue-200 text-xs">
            <p>&copy; 2026 CEMSA. Todos los derechos reservados.</p>
            <p class="opacity-80">Versión 2.5.0 | Soporte: soporte@cemsa.com</p>
        </div>
        
        <!-- Botón de Ayuda Flotante -->
        <div class="fixed bottom-6 right-6">
             <button class="bg-gray-800 hover:bg-gray-700 text-white rounded-full w-10 h-10 flex items-center justify-center shadow-lg transition-colors">
                <span class="font-bold text-lg">?</span>
            </button>
        </div>
    </body>
</html>