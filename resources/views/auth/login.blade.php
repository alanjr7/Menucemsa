<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CEMSA - Iniciar Sesión</title>
    <!-- Tailwind CSS desde CDN para que funcione inmediatamente -->
    <script src="https://cdn.tailwindcss.com"></script>
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
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input id="password" type="password" name="password" placeholder="••••••••" required
                               class="w-full pl-11 pr-5 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-800 
                                      placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#0b44a8]/50 focus:bg-white focus:border-[#0b44a8]
                                      transition-all duration-300 shadow-sm">
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
             class="absolute inset-0 w-full h-full object-cover opacity-85 transition-transform duration-[15s] hover:scale-110">
        
        <!-- Overlay oscuro para mejorar la lectura del texto -->
        <div class="absolute inset-0 bg-gradient-to-t from-[#041a45]/90 via-[#041a45]/40 to-transparent"></div>

        <!-- Decoración abstracta opcional -->
        <div class="absolute top-0 left-0 w-full h-full opacity-30 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-white/20 via-transparent to-transparent"></div>

        <div class="absolute bottom-20 left-0 right-0 px-16 text-center">
            <div class="inline-block p-4 rounded-2xl backdrop-blur-sm bg-white/10 border border-white/20 shadow-2xl mb-4">
                <h2 class="text-white text-4xl xl:text-5xl font-serif italic drop-shadow-xl leading-tight">
                    Clínica de Especialidades<br>
                    <span class="text-blue-200">Santa Cruz S.R.L.</span>
                </h2>
            </div>
            <p class="text-gray-200 text-lg xl:text-xl font-light tracking-wide mt-2 drop-shadow-md">
                Excelencia médica, tecnología de punta y calidez humana al servicio de su salud.
            </p>
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