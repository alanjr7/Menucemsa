<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CEMSA - Iniciar Sesión</title>
    <!-- Tailwind CSS compilado por Vite (funciona sin internet, igual que el resto del sistema) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Fuente Inter para un look profesional -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f7f6;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1; 
        }
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1; 
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8; 
        }
    </style>
</head>
<body class="min-h-screen flex text-gray-800 selection:bg-[#0b44a8] selection:text-white">

    <!-- Sección Izquierda: Formulario -->
    <div class="w-full lg:w-1/2 flex flex-col justify-center items-center px-6 py-12 lg:px-16 bg-white relative z-10 shadow-[10px_0_30px_rgba(0,0,0,0.05)]">
        
        <!-- Contenedor del logo móvil/tablet -->
        <div class="lg:hidden mb-8 text-center">
            <img src="/images/logocelular.png" alt="Cemsa Logo" class="w-28 mx-auto mb-4 drop-shadow-sm transition-transform hover:scale-105 duration-300">
            <h1 class="text-3xl font-serif text-[#0b44a8] italic tracking-wide">CEMSA</h1>
        </div>

        <!-- Tarjeta del formulario -->
        <div class="w-full max-w-md bg-white rounded-3xl lg:rounded-none p-8 sm:p-10 lg:p-0 shadow-2xl lg:shadow-none border border-gray-100 lg:border-none relative overflow-hidden">
            
            <!-- Efecto de resplandor sutil (solo en móvil) -->
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-[#0b44a8] rounded-full mix-blend-multiply filter blur-3xl opacity-10 lg:hidden"></div>

            <div class="hidden lg:block text-center mb-10">
                <img src="/images/logocelular.png" alt="Cemsa Logo" class="w-32 mx-auto mb-6 drop-shadow-sm transition-transform hover:scale-105 duration-300">
                <h2 class="text-3xl font-bold text-gray-900 tracking-tight">Bienvenido a CEMSA</h2>
                <p class="text-gray-500 mt-2 font-light">Ingresa tus credenciales para acceder al sistema.</p>
            </div>

            <div class="lg:hidden text-center mb-8 relative z-10">
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Iniciar Sesión</h2>
                <p class="text-gray-500 mt-1 text-sm">Ingresa a tu cuenta para continuar</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-6 relative z-10">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Correo Electrónico</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="ejemplo@cemsa.com" required autofocus
                               class="w-full pl-11 pr-5 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-800 
                                      placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#0b44a8]/50 focus:bg-white focus:border-[#0b44a8]
                                      transition-all duration-300 shadow-sm">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Contraseña</label>
                    <div x-data="{ show: false }" class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input id="password" :type="show ? 'text' : 'password'" name="password" placeholder="••••••••" required
                               class="w-full pl-11 pr-12 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-800
                                      placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#0b44a8]/50 focus:bg-white focus:border-[#0b44a8]
                                      transition-all duration-300 shadow-sm">
                        <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none transition-colors duration-200">
                            <svg x-show="!show" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="show" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.477 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 4.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-[#0b44a8] bg-gray-100 border-gray-300 rounded focus:ring-[#0b44a8] cursor-pointer">
                        <span class="ml-2 text-sm text-gray-600 group-hover:text-gray-900 transition-colors">Recordarme</span>
                    </label>
                </div>

                <button type="submit"
                        class="w-full py-3.5 mt-2 bg-gradient-to-r from-[#0b44a8] to-[#005bc4] text-white font-semibold rounded-xl
                               hover:from-[#09398f] hover:to-[#004a9f] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#0b44a8]
                               transform transition-all duration-300 active:scale-[0.98] shadow-[0_8px_20px_rgba(11,68,168,0.25)] hover:shadow-[0_12px_25px_rgba(11,68,168,0.35)] flex justify-center items-center gap-2 group">
                    <span class="tracking-wide">Ingresar al Sistema</span>
                    <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </form>
        </div>
        
        <!-- Footer simple para la izquierda en móvil -->
        <div class="lg:hidden mt-auto pt-10 text-center">
            <p class="text-xs text-gray-400">&copy; {{ date('Y') }} Clínica de Especialidades Santa Cruz</p>
        </div>
    </div>

    <!-- Sección Derecha: Imagen de fondo -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gray-900">
        <img src="/images/fondoDia.png"
             alt="Fondo Clinica CEMSA"
             class="absolute inset-0 w-full h-full object-cover transition-transform duration-[15s] hover:scale-110">

        <!-- Degradado neutro solo en la base para legibilidad del recuadro (sin tinte azul) -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>

        <!-- Decoración abstracta opcional -->
        <div class="absolute top-0 left-0 w-full h-full opacity-30 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-white/20 via-transparent to-transparent"></div>

        <div class="absolute bottom-6 left-0 right-0 px-6 text-center">
            <div class="inline-block px-5 py-2 rounded-xl backdrop-blur-sm bg-white/10 border border-white/20 shadow-2xl">
                <h2 class="text-white text-xl xl:text-2xl font-serif italic drop-shadow-xl whitespace-nowrap">
                    Clínica de Especialidades <span class="text-blue-200">Medicas Santa Cruz S.R.L.</span>
                </h2>
            </div>
            <!-- <p class="text-gray-200 text-sm xl:text-base font-light tracking-wide mt-1 drop-shadow-md">
                Excelencia médica, tecnología de punta y calidez humana al servicio de su salud.
            </p> -->
        </div>
    </div>

    <!-- SweetAlert2 para notificaciones flotantes -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 6000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: 'error',
                title: @json($errors->first('email') ?? $errors->first())
            });
        });
    </script>
    @endif
</body>
</html>