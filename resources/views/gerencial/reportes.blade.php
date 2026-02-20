<x-app-layout>
    <div class="min-h-screen bg-gray-50/50 p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">Reportes Gerenciales</h1>
                <p class="text-sm text-gray-500 font-medium">Informes y análisis detallado del sistema</p>
            </div>
            <button class="flex items-center gap-2 bg-white border border-gray-300 px-4 py-2 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Seleccionar Periodo
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-6">
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <h3 class="font-bold text-gray-800 mb-6">Atenciones por Tipo</h3>
                <div class="relative h-[300px]">
                    <canvas id="chartAtenciones"></canvas>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col">
                <h3 class="font-bold text-gray-800 mb-6">Distribución por Especialidad</h3>
                <div class="relative h-[300px] flex justify-center">
                    <canvas id="chartEspecialidades"></canvas>
                </div>
            </div>
        </div>

        <div class="space-y-6 -mt-2 mb-10"> <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-50 flex items-center gap-3 bg-gray-50/30">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <h2 class="font-bold text-gray-800">Reportes Clínicos</h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:bg-blue-50/50 transition group">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800">Atenciones por Especialidad</h4>
                            <p class="text-xs text-gray-500">Resumen de consultas por área médica</p>
                        </div>
                        <button class="flex items-center gap-2 bg-white border border-gray-200 px-3 py-1.5 rounded-lg text-xs font-bold text-gray-700 shadow-sm group-hover:border-blue-300">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Generar
                        </button>
                    </div>
                    <div class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:bg-blue-50/50 transition group">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800">Pacientes Hospitalizados</h4>
                            <p class="text-xs text-gray-500">Detalle de hospitalizaciones activas</p>
                        </div>
                        <button class="flex items-center gap-2 bg-white border border-gray-200 px-3 py-1.5 rounded-lg text-xs font-bold text-gray-700 shadow-sm group-hover:border-blue-300">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Generar
                        </button>
                    </div>
                    <div class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:bg-blue-50/50 transition group">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800">Cirugías Realizadas</h4>
                            <p class="text-xs text-gray-500">Listado de procedimientos quirúrgicos</p>
                        </div>
                        <button class="flex items-center gap-2 bg-white border border-gray-200 px-3 py-1.5 rounded-lg text-xs font-bold text-gray-700 shadow-sm group-hover:border-blue-300">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Generar
                        </button>
                    </div>
                    <div class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:bg-blue-50/50 transition group">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800">Emergencias</h4>
                            <p class="text-xs text-gray-500">Registro de atenciones en emergencia</p>
                        </div>
                        <button class="flex items-center gap-2 bg-white border border-gray-200 px-3 py-1.5 rounded-lg text-xs font-bold text-gray-700 shadow-sm group-hover:border-blue-300">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Generar
                        </button>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-50 flex items-center gap-3 bg-gray-50/30">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <h2 class="font-bold text-gray-800">Reportes Financieros</h2>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:bg-emerald-50/50 transition group">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800">Ingresos por Servicio</h4>
                            <p class="text-xs text-gray-500">Facturación detallada por tipo</p>
                        </div>
                        <button class="flex items-center gap-2 bg-white border border-gray-200 px-3 py-1.5 rounded-lg text-xs font-bold text-gray-700 shadow-sm group-hover:border-emerald-300">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Generar
                        </button>
                    </div>

                    <div class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:bg-emerald-50/50 transition group">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800">Cuentas por Cobrar</h4>
                            <p class="text-xs text-gray-500">Estado de cobranza</p>
                        </div>
                        <button class="flex items-center gap-2 bg-white border border-gray-200 px-3 py-1.5 rounded-lg text-xs font-bold text-gray-700 shadow-sm group-hover:border-emerald-300">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Generar
                        </button>
                    </div>

                    <div class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:bg-emerald-50/50 transition group">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800">Análisis de Morosidad</h4>
                            <p class="text-xs text-gray-500">Seguimiento de pagos pendientes</p>
                        </div>
                        <button class="flex items-center gap-2 bg-white border border-gray-200 px-3 py-1.5 rounded-lg text-xs font-bold text-gray-700 shadow-sm group-hover:border-emerald-300">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Generar
                        </button>
                    </div>

                    <div class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:bg-emerald-50/50 transition group">
                        <div>
                            <h4 class="text-sm font-bold text-gray-800">Cierre de Caja</h4>
                            <p class="text-xs text-gray-500">Movimientos diarios de caja</p>
                        </div>
                        <button class="flex items-center gap-2 bg-white border border-gray-200 px-3 py-1.5 rounded-lg text-xs font-bold text-gray-700 shadow-sm group-hover:border-emerald-300">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Generar
                        </button>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-50 flex items-center gap-3 bg-gray-50/30">
        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
        </svg>
        <h2 class="font-bold text-gray-800">Reportes Operativos</h2>
    </div>
    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:bg-orange-50/50 transition group">
            <div>
                <h4 class="text-sm font-bold text-gray-800">Uso de Quirófanos</h4>
                <p class="text-xs text-gray-500">Ocupación de salas de cirugía</p>
            </div>
            <button class="flex items-center gap-2 bg-white border border-gray-200 px-3 py-1.5 rounded-lg text-xs font-bold text-gray-700 shadow-sm group-hover:border-orange-300">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Generar
            </button>
        </div>

        <div class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:bg-orange-50/50 transition group">
            <div>
                <h4 class="text-sm font-bold text-gray-800">Ocupación de Camas</h4>
                <p class="text-xs text-gray-500">Disponibilidad hospitalaria</p>
            </div>
            <button class="flex items-center gap-2 bg-white border border-gray-200 px-3 py-1.5 rounded-lg text-xs font-bold text-gray-700 shadow-sm group-hover:border-orange-300">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Generar
            </button>
        </div>

        <div class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:bg-orange-50/50 transition group">
            <div>
                <h4 class="text-sm font-bold text-gray-800">Stock de Farmacia</h4>
                <p class="text-xs text-gray-500">Inventario de medicamentos</p>
            </div>
            <button class="flex items-center gap-2 bg-white border border-gray-200 px-3 py-1.5 rounded-lg text-xs font-bold text-gray-700 shadow-sm group-hover:border-orange-300">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Generar
            </button>
        </div>

        <div class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:bg-orange-50/50 transition group">
            <div>
                <h4 class="text-sm font-bold text-gray-800">Productividad Médica</h4>
                <p class="text-xs text-gray-500">Atenciones por profesional</p>
            </div>
            <button class="flex items-center gap-2 bg-white border border-gray-200 px-3 py-1.5 rounded-lg text-xs font-bold text-gray-700 shadow-sm group-hover:border-orange-300">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Generar
            </button>
        </div>
    </div>
</div>

     <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <h3 class="font-bold text-gray-800 mb-4">Opciones de Exportación</h3>
                <div class="flex flex-wrap gap-4">

                    <button class="flex items-center gap-2 bg-white border border-gray-300 px-4 py-2 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50 transition shadow-sm">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Exportar a PDF
                    </button>

                    <button class="flex items-center gap-2 bg-white border border-gray-300 px-4 py-2 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50 transition shadow-sm">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Exportar a Excel
                    </button>

                    <button class="flex items-center gap-2 bg-white border border-gray-300 px-4 py-2 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50 transition shadow-sm">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Enviar por Email
                    </button>

                </div>
            </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const tooltipConfig = {
            backgroundColor: 'rgba(255, 255, 255, 0.95)',
            titleColor: '#1f2937',
            bodyColor: '#4b5563',
            borderColor: '#e5e7eb',
            borderWidth: 1,
            padding: 12,
            boxPadding: 6,
            usePointStyle: true,
            titleFont: { weight: 'bold', size: 14 }
        };

        const ctxBar = document.getElementById('chartAtenciones').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: ['Ene', 'Feb', 'Mar', 'Abr'],
                datasets: [
                    { label: 'Consultas', data: [450, 520, 480, 550], backgroundColor: '#3b82f6', borderRadius: 4 },
                    { label: 'Emergencias', data: [120, 150, 140, 160], backgroundColor: '#f59e0b', borderRadius: 4 },
                    { label: 'Cirugías', data: [45, 60, 55, 70], backgroundColor: '#10b981', borderRadius: 4 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' }, tooltip: tooltipConfig },
                scales: { y: { beginAtZero: true, grid: { color: '#f3f4f6' } }, x: { grid: { display: false } } }
            }
        });

        const ctxPie = document.getElementById('chartEspecialidades').getContext('2d');
        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: ['Medicina General', 'Pediatría', 'Cardiología', 'Traumatología', 'Ginecología'],
                datasets: [{
                    data: [40, 25, 15, 10, 10],
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899'],
                    borderWidth: 0,
                    hoverOffset: 20
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { usePointStyle: true, font: { weight: 'bold' } } },
                    tooltip: tooltipConfig
                },
                cutout: '70%'
            }
        });
    </script>
</x-app-layout>
