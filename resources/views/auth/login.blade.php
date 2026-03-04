<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CEMSA - Login</title>
    <!-- Tailwind CSS desde CDN para que funcione inmediatamente -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Fuente Inter para un look profesional similar a la imagen -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        /* Color específico extraído de la imagen (Azul Real Profundo) */
        .bg-cemsa-blue {
            background-color: #0b44a8;
        }
        .text-cemsa-blue {
            color: #0b44a8;
        }
        .btn-cemsa {
            background-color: #005bc4;
        }
        .btn-cemsa:hover {
            background-color: #004a9f;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col lg:flex-row transition-all duration-500
             bg-[#c2eaba]
             sm:bg-cover sm:bg-center sm:bg-no-repeat
             lg:bg-white">

    <style>
        @media (min-width: 640px) and (max-width: 1023px) {
            body {
                background-image: url("{{ asset('images/fondotablet.png') }}") !important;
            }
        }
    </style>

    <div class="flex-grow lg:flex-none lg:w-1/2 flex flex-col justify-center items-center px-6 py-10 relative z-10 lg:bg-[#c2eaba]">

        <div class="text-center mb-10">
            <img src="{{ asset('images/logocelular.png') }}" alt="Cemsa Logo" class="w-40 mx-auto mb-4 drop-shadow-md">
            <h1 class="text-5xl font-serif text-white italic drop-shadow-lg">Cemsa</h1>
        </div>

        <div class="w-full max-w-[350px] sm:max-w-[450px] lg:max-w-[400px]
                    p-12 overflow-hidden transition-all duration-500
                    bg-white rounded-[3.5rem] shadow-2xl
                    sm:bg-white/30 sm:backdrop-blur-lg sm:rounded-[3rem]
                    lg:bg-white lg:backdrop-blur-0 lg:shadow-2xl lg:rounded-[4rem]">

            <h2 class="text-2xl font-bold text-center text-gray-800 mb-10 lg:text-3xl">login</h2>

            <form method="POST" action="{{ route('login') }}" class="space-y-8">
                @csrf

                <div>
                    <label class="block text-sm font-bold text-black sm:text-white lg:text-black mb-2 ml-2">coreo</label>
                    <input type="email" name="email" placeholder="Label"
                           class="w-full px-6 py-4 rounded-full text-gray-600 outline-none
                                  bg-white shadow-[inset_0_2px_10px_rgba(0,0,0,0.1)] border-none">
                </div>

                <div>
                    <label class="block text-sm font-bold text-black sm:text-white lg:text-black mb-2 ml-2">contraseña</label>
                    <input type="password" name="password" placeholder="Label"
                           class="w-full px-6 py-4 rounded-full text-gray-600 outline-none
                                  bg-white shadow-[inset_0_2px_10px_rgba(0,0,0,0.1)] border-none">
                </div>

                <button type="submit"
                        class="w-full py-4 bg-[#0047cc] text-white font-bold rounded-full
                               flex items-center justify-center gap-2 transform transition-all hover:brightness-110 active:scale-95 shadow-lg">
                    Ingresar <span class="text-xl">→</span>
                </button>
            </form>
        </div>
    </div>

    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
        <img src="{{ asset('images/fondotablet.png') }}"
             alt="Fondo Clinica"
             class="absolute inset-0 w-full h-full object-cover transform scale-105 hover:scale-100 transition-transform duration-700">

        <div class="absolute inset-0 bg-black/10"></div>

        <div class="absolute bottom-12 left-0 right-0 text-center px-4">
            <p class="text-white text-2xl font-serif italic drop-shadow-[0_2px_10px_rgba(0,0,0,0.5)]">
                Clinica de especialidades Santa Cruz Srl.
            </p>
        </div>
    </div>

</body>
</html>
