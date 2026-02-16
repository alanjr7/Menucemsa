<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - HIS / ERP CEMSA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chart.js para los gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-bg { background-color: #1e3a8a; }
    </style>
</head>
<body class="bg-gray-50 h-screen flex overflow-hidden">

    <x-sidebar />

    <!-- MAIN CONTENT -->
    <div class="flex-1 flex flex-col min-w-0 bg-gray-50">
        
        <!-- TOP HEADER -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8">
            <div class="flex items-center text-blue-600 font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Clínica CEMSA - Sede Principal
            </div>

            <div class="flex items-center gap-6">
                <!-- Notification Bell -->
                <button class="relative p-2 text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="absolute top-1 right-1 h-4 w-4 bg-red-500 rounded-full flex items-center justify-center text-[10px] text-white font-bold border-2 border-white">3</span>
                </button>

                <!-- User Profile -->
                <div class="flex items-center gap-3 border-l border-gray-200 pl-6">
                    <div class="flex flex-col text-right">
                        <span class="text-sm font-semibold text-gray-800">Dr. Carlos Mendoza</span>
                        <span class="text-xs text-gray-500">Médico General</span>
                    </div>
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <div class="h-9 w-9 rounded-full bg-blue-600 flex items-center justify-center text-white text-sm font-medium ring-2 ring-white shadow-sm cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Editar Perfil') }}
                            </x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Cerrar Sesión') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </div>
        </header>

        <!-- CONTENT SCROLL AREA -->
        <main class="flex-1 overflow-y-auto p-8">
            
            <!-- Dashboard Title -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Dashboard Ejecutivo</h2>
                <p class="text-gray-500 mt-1">Resumen general del sistema HIS/ERP - CEMSA</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                
                <!-- Card 1: Pacientes -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-gray-500 text-sm font-medium">Pacientes Activos</span>
                        <span class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                        </span>
                    </div>
                    <div class="text-3xl font-bold text-gray-800 mb-1">342</div>
                    <div class="flex items-center text-xs font-medium text-green-500">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        +12% vs. mes anterior
                    </div>
                </div>

                <!-- Card 2: Consultas -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-gray-500 text-sm font-medium">Consultas Hoy</span>
                        <span class="p-2 bg-green-50 text-green-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                    </div>
                    <div class="text-3xl font-bold text-gray-800 mb-1">89</div>
                    <div class="flex items-center text-xs font-medium text-green-500">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        +5% vs. mes anterior
                    </div>
                </div>

                <!-- Card 3: Facturación -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-gray-500 text-sm font-medium">Facturación Diaria</span>
                        <span class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                    </div>
                    <div class="text-3xl font-bold text-gray-800 mb-1">$45,230</div>
                    <div class="flex items-center text-xs font-medium text-green-500">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        +8% vs. mes anterior
                    </div>
                </div>

                <!-- Card 4: Cirugías -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <span class="text-gray-500 text-sm font-medium">Cirugías Programadas</span>
                        <span class="p-2 bg-orange-50 text-orange-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </span>
                    </div>
                    <div class="text-3xl font-bold text-gray-800 mb-1">12</div>
                    <div class="flex items-center text-xs font-medium text-gray-500">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/></svg>
                        0% vs. mes anterior
                    </div>
                </div>
            </div>

            <!-- Alerts Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
                <div class="flex items-center mb-6">
                    <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="text-lg font-bold text-gray-800">Alertas Urgentes</h3>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="font-medium">Vencimientos próximos en Farmacia (5 productos)</span>
                    </div>
                    <div class="flex items-center p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-yellow-800">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="font-medium">Facturas pendientes de cobro: $23,450</span>
                    </div>
                    <div class="flex items-center p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-yellow-800">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="font-medium">Quirófano 2: Mantenimiento programado mañana</span>
                    </div>
                </div>
            </div>

            <!-- Charts Section (NUEVO) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Bar Chart -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-gray-800 font-bold mb-4">Pacientes Atendidos</h3>
                    <div class="relative w-full h-64">
                        <canvas id="pacientesChart"></canvas>
                    </div>
                </div>

                <!-- Line Chart -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-gray-800 font-bold mb-4">Ingresos Mensuales ($)</h3>
                    <div class="relative w-full h-64">
                        <canvas id="ingresosChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Activity Section (NUEVO) -->
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-8">
                <div class="flex items-center mb-6">
                    <svg class="w-5 h-5 text-gray-800 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-lg font-bold text-gray-800">Actividad Reciente</h3>
                </div>

                <div class="space-y-0 divide-y divide-gray-100">
                    
                    <!-- Item 1 -->
                    <div class="flex items-start py-4">
                        <div class="flex-shrink-0 mr-4">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Alta médica: Paciente López, María</p>
                            <p class="text-xs text-gray-500 mt-0.5">Hace 5 min</p>
                        </div>
                    </div>

                    <!-- Item 2 -->
                    <div class="flex items-start py-4">
                        <div class="flex-shrink-0 mr-4">
                            <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Alerta: Stock bajo en Farmacia - Paracetamol</p>
                            <p class="text-xs text-gray-500 mt-0.5">Hace 12 min</p>
                        </div>
                    </div>

                    <!-- Item 3 -->
                    <div class="flex items-start py-4">
                        <div class="flex-shrink-0 mr-4">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Nueva admisión: Paciente García, Juan</p>
                            <p class="text-xs text-gray-500 mt-0.5">Hace 18 min</p>
                        </div>
                    </div>

                    <!-- Item 4 -->
                    <div class="flex items-start py-4">
                        <div class="flex-shrink-0 mr-4">
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Factura bloqueada: Falta autorización seguro</p>
                            <p class="text-xs text-gray-500 mt-0.5">Hace 25 min</p>
                        </div>
                    </div>

                    <!-- Item 5 -->
                    <div class="flex items-start py-4">
                        <div class="flex-shrink-0 mr-4">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Cirugía completada: Apendicectomía</p>
                            <p class="text-xs text-gray-500 mt-0.5">Hace 1 hora</p>
                        </div>
                    </div>

                </div>
            </div>

        </main>
    </div>

    <!-- Script para renderizar los gráficos -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // Gráfico de Barras: Pacientes Atendidos
            const ctxPacientes = document.getElementById('pacientesChart').getContext('2d');
            new Chart(ctxPacientes, {
                type: 'bar',
                data: {
                    labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Pacientes',
                        data: [280, 310, 290, 340, 370, 320],
                        backgroundColor: '#0066cc', // Azul corporativo
                        borderRadius: 4,
                        barThickness: 30
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [2, 4], color: '#e5e7eb' },
                            ticks: { font: { size: 11 } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 } }
                        }
                    }
                }
            });

            // Gráfico de Línea: Ingresos Mensuales
            const ctxIngresos = document.getElementById('ingresosChart').getContext('2d');
            new Chart(ctxIngresos, {
                type: 'line',
                data: {
                    labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Ingresos ($)',
                        data: [34000, 42000, 38000, 45000, 48000, 43000],
                        borderColor: '#10b981', // Verde esmeralda
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4, // Curva suave
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#10b981',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [2, 4], color: '#e5e7eb' },
                            ticks: { font: { size: 11 } }
                        },
                        x: {
                            grid: { borderDash: [2, 4], color: '#e5e7eb' },
                            ticks: { font: { size: 11 } }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
