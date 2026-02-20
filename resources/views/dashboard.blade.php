<x-app-layout>
    <div class="p-6 bg-gray-50/50 min-h-screen">

        <!-- Header -->
        <div class="flex justify-between items-end mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Dashboard Ejecutivo</h1>
                <p class="text-sm text-gray-500">Resumen general del sistema HIS/ERP - CEMSA</p>
            </div>
            <div class="text-right">
                <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full uppercase tracking-wider animate-pulse">● En Vivo</span>
                <p class="text-[10px] text-gray-400 mt-1 font-medium">{{ now()->format('d M, Y H:i') }}</p>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            
            <!-- Card 1: Pacientes -->
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between h-32 hover:shadow-md transition">
                <div class="flex justify-between items-start">
                    <span class="text-sm font-medium text-gray-500">Pacientes Activos</span>
                    <div class="p-2 bg-blue-50 rounded-lg text-blue-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.123-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800">342</div>
                    <div class="text-xs text-green-500 font-bold flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        +12% vs. mes anterior
                    </div>
                </div>
            </div>

            <!-- Card 2: Consultas -->
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between h-32 hover:shadow-md transition">
                <div class="flex justify-between items-start">
                    <span class="text-sm font-medium text-gray-500">Consultas Hoy</span>
                    <div class="p-2 bg-green-50 rounded-lg text-green-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800">89</div>
                    <div class="text-xs text-green-500 font-bold flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        +5% vs. mes anterior
                    </div>
                </div>
            </div>

            <!-- Card 3: Facturación -->
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between h-32 hover:shadow-md transition">
                <div class="flex justify-between items-start">
                    <span class="text-sm font-medium text-gray-500">Facturación Diaria</span>
                    <div class="p-2 bg-purple-50 rounded-lg text-purple-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800">$45,230</div>
                    <div class="text-xs text-green-500 font-bold flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        +8% vs. mes anterior
                    </div>
                </div>
            </div>

            <!-- Card 4: Cirugías -->
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between h-32 hover:shadow-md transition">
                <div class="flex justify-between items-start">
                    <span class="text-sm font-medium text-gray-500">Cirugías Programadas</span>
                    <div class="p-2 bg-orange-50 rounded-lg text-orange-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800">12</div>
                    <div class="text-xs text-gray-400 font-medium">0% vs. mes anterior</div>
                </div>
            </div>
        </div>

        <!-- Alerts Section -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-8">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-lg font-bold text-gray-800">Alertas Urgentes</h3>
            </div>

            <div class="space-y-3">
                <div class="flex items-center p-4 bg-red-50 border border-red-100 rounded-xl text-red-700 text-sm font-medium">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Vencimientos próximos en Farmacia (5 productos)
                </div>

                <div class="flex items-center p-4 bg-yellow-50 border border-yellow-100 rounded-xl text-yellow-700 text-sm font-medium">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Facturas pendientes de cobro: $23,450
                </div>

                <div class="flex items-center p-4 bg-yellow-50 border border-yellow-100 rounded-xl text-yellow-700 text-sm font-medium">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Quirófano 2: Mantenimiento programado mañana
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-800 text-sm">Pacientes Atendidos</h3>
                    <span class="text-[10px] bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full font-bold uppercase tracking-wider">Semestral</span>
                </div>
                <div class="h-64">
                    <canvas id="pacientesChart"></canvas>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-800 text-sm">Ingresos Mensuales ($)</h3>
                    <span class="text-[10px] bg-green-50 text-green-600 px-2 py-0.5 rounded-full font-bold uppercase tracking-wider">Actualizado</span>
                </div>
                <div class="h-64">
                    <canvas id="ingresosChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Activity Section -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 bg-gray-100 rounded-lg">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">Actividad Reciente</h3>
                </div>
            </div>

            <div class="space-y-6">
                <div class="flex gap-4 relative">
                    <div class="before:content-[''] before:absolute before:left-2 before:top-8 before:w-0.5 before:h-6 before:bg-gray-100">
                        <div class="w-4 h-4 rounded-full bg-green-500 border-4 border-green-100 z-10 relative"></div>
                    </div>
                    <div class="flex-1 flex justify-between">
                        <p class="text-sm font-bold text-gray-800">Alta médica: <span class="font-medium text-gray-600">Paciente López, María</span></p>
                        <span class="text-[10px] text-gray-400 font-medium">Hace 5 min</span>
                    </div>
                </div>

                <div class="flex gap-4 relative">
                    <div class="before:content-[''] before:absolute before:left-2 before:top-8 before:w-0.5 before:h-6 before:bg-gray-100">
                        <div class="w-4 h-4 rounded-full bg-orange-500 border-4 border-orange-100 z-10 relative"></div>
                    </div>
                    <div class="flex-1 text-sm font-bold text-gray-800">
                         Alerta: <span class="font-medium text-gray-600">Stock bajo en Farmacia - Paracetamol</span>
                         <p class="text-[10px] text-gray-400 font-medium mt-0.5">Hace 12 min</p>
                    </div>
                </div>

                <div class="flex gap-4 relative">
                    <div class="before:content-[''] before:absolute before:left-2 before:top-8 before:w-0.5 before:h-6 before:bg-gray-100">
                        <div class="w-4 h-4 rounded-full bg-blue-500 border-4 border-blue-100 z-10 relative"></div>
                    </div>
                    <div class="flex-1 text-sm font-bold text-gray-800">
                         Nueva admisión: <span class="font-medium text-gray-600">Paciente García, Juan</span>
                         <p class="text-[10px] text-gray-400 font-medium mt-0.5">Hace 18 min</p>
                    </div>
                </div>

                <div class="flex gap-4 relative">
                    <div class="before:content-[''] before:absolute before:left-2 before:top-8 before:w-0.5 before:h-6 before:bg-gray-100">
                        <div class="w-4 h-4 rounded-full bg-red-500 border-4 border-red-100 z-10 relative"></div>
                    </div>
                    <div class="flex-1 text-sm font-bold text-gray-800">
                         Factura bloqueada: <span class="font-medium text-gray-600">Falta autorización seguro</span>
                         <p class="text-[10px] text-gray-400 font-medium mt-0.5">Hace 25 min</p>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div class="w-4 h-4 rounded-full bg-green-500 border-4 border-green-100 z-10 relative"></div>
                    <div class="flex-1 text-sm font-bold text-gray-800">
                         Cirugía completada: <span class="font-medium text-gray-600">Apendicectomía</span>
                         <p class="text-[10px] text-gray-400 font-medium mt-0.5">Hace 1 hora</p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Chart.js Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                        backgroundColor: '#3b82f6',
                        borderRadius: 6,
                        barThickness: 24
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
                            grid: { borderDash: [4, 4], color: '#f3f4f6' },
                            ticks: { font: { size: 11 }, color: '#9ca3af' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 }, color: '#6b7280' }
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
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
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
                            grid: { borderDash: [4, 4], color: '#f3f4f6' },
                            ticks: { font: { size: 11 }, color: '#9ca3af' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11 }, color: '#6b7280' }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>