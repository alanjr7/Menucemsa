<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CEMSA · @yield('title', 'Reservar cirugía')</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-cemsa { background-color: #0b44a8; }
        .text-cemsa { color: #0b44a8; }
        .ring-cemsa:focus { --tw-ring-color: #0b44a8; }
        [x-cloak] { display: none !important; }
        @keyframes shake { 10%,90%{transform:translateX(-1px)} 20%,80%{transform:translateX(2px)} 30%,50%,70%{transform:translateX(-4px)} 40%,60%{transform:translateX(4px)} }
        .shake { animation: shake .4s ease; }
    </style>
</head>
<body class="min-h-full bg-gray-50 text-gray-900 antialiased">
    <header class="bg-cemsa text-white">
        <div class="max-w-4xl mx-auto px-4 py-4 flex items-center gap-3">
            <img src="{{ asset('images/logocelular.png') }}" alt="CEMSA" class="h-9 w-9 rounded bg-white/10 p-1" onerror="this.style.display='none'">
            <div>
                <p class="font-semibold leading-tight">CEMSA</p>
                <p class="text-xs text-blue-100 leading-tight">Reserva de quirófano — cirujanos externos</p>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-6 md:py-10">
        @yield('content')
    </main>

    <footer class="max-w-4xl mx-auto px-4 py-8 text-center text-xs text-gray-400">
        <p>&copy; {{ date('Y') }} CEMSA. Todos los derechos reservados.</p>
    </footer>

    @stack('scripts')
</body>
</html>
