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
<body class="bg-cemsa-blue min-h-screen flex flex-col justify-between relative overflow-hidden">

    <!-- Contenido Principal Centrado -->
    <div class="flex-grow flex flex-col justify-center items-center px-4 sm:px-6 lg:px-8 py-10">
        
        <!-- HEADER: Logo y Títulos -->
        <div class="text-center mb-8 transform transition-all duration-500 ease-in-out">
            <!-- Círculo Blanco con Icono -->
            <div class="mx-auto w-24 h-24 bg-white rounded-full flex items-center justify-center mb-4 shadow-lg">
                <!-- Icono de Ritmo Cardíaco (SVG) -->
                <svg class="w-12 h-12 text-cemsa-blue" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                </svg>
            </div>
            
            <!-- Título Principal -->
            <h1 class="text-3xl sm:text-4xl font-bold text-white mb-2 tracking-wide drop-shadow-md">
                HIS / ERP Clínico
            </h1>
            
            <!-- Subtítulo -->
            <p class="text-blue-100 text-sm sm:text-base font-light tracking-wide opacity-90">
                CEMSA - Sistema de Gestión Hospitalaria
            </p>
        </div>

        <!-- CARD: Formulario de Login -->
        <div class="w-full max-w-[450px] bg-white rounded-xl shadow-2xl overflow-hidden">
            <div class="p-8 sm:p-10">
                
                <!-- Encabezado del Formulario -->
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-gray-800">Iniciar Sesión</h2>
                    <p class="text-gray-500 text-sm mt-2">Ingrese sus credenciales para acceder al sistema</p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <!-- Formulario -->
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <!-- Campo: Usuario -->
                    <div class="mb-5">
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                            Usuario
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <!-- Icono Usuario -->
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input 
                                id="email" 
                                type="email" 
                                name="email" 
                                :value="old('email')" 
                                required 
                                autofocus 
                                autocomplete="username" 
                                class="appearance-none block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-150 ease-in-out sm:text-sm" 
                                placeholder="Ingrese su usuario"
                            >
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Campo: Contraseña -->
                    <div class="mb-8">
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                            Contraseña
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <!-- Icono Candado -->
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input 
                                id="password" 
                                type="password" 
                                name="password" 
                                required 
                                autocomplete="current-password" 
                                class="appearance-none block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg placeholder-gray-400 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-150 ease-in-out sm:text-sm" 
                                placeholder="Ingrese su contraseña"
                            >
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Botón Submit -->
                    <div>
                        <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white btn-cemsa focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            Ingresar al Sistema
                        </button>
                    </div>

                    <!-- Link Olvidó Contraseña -->
                    <div class="mt-6 text-center">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="font-medium text-sm text-blue-600 hover:text-blue-500 transition-colors">
                                ¿Olvidó su contraseña?
                            </a>
                        @endif
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- FOOTER: Información Legal -->
    <div class="w-full text-center pb-6 text-blue-200 text-xs sm:text-sm space-y-1">
        <p>&copy; 2026 CEMSA. Todos los derechos reservados.</p>
        <p class="opacity-80">Versión 2.5.0 | Soporte: soporte@cemsa.com</p>
    </div>

    <!-- Botón Flotante de Ayuda (?) -->
    <div class="fixed bottom-5 right-5 z-10">
        <button class="bg-gray-800 hover:bg-gray-700 text-white rounded-full w-10 h-10 flex items-center justify-center shadow-lg transition-transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900" title="Ayuda">
            <span class="font-bold text-lg">?</span>
        </button>
    </div>

</body>
</html>