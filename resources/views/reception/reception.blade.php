<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recepción - HIS / ERP CEMSA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-bg { background-color: #1e3a8a; }
        
        /* Estilos personalizados para inputs */
        .form-select, .form-input {
            width: 100%;
            border-color: #d1d5db;
            border-radius: 0.5rem;
            padding: 0.625rem 0.75rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            background-color: #fff;
        }
        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: #3b82f6;
            ring: 2px solid #3b82f6;
        }
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
                <button class="relative p-2 text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="absolute top-1 right-1 h-4 w-4 bg-red-500 rounded-full flex items-center justify-center text-[10px] text-white font-bold border-2 border-white">3</span>
                </button>

                <div class="flex items-center gap-3 border-l border-gray-200 pl-6">
                    <div class="flex flex-col text-right">
                        <span class="text-sm font-semibold text-gray-800">Dr. Carlos Mendoza</span>
                        <span class="text-xs text-gray-500">Médico General</span>
                    </div>
                    <div class="h-9 w-9 rounded-full bg-blue-600 flex items-center justify-center text-white text-sm font-medium ring-2 ring-white shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </header>

        <!-- CONTENT SCROLL AREA -->
        <main class="flex-1 overflow-y-auto p-8">
            
            <!-- Reception Header -->
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Recepción y Admisión</h2>
                    <p class="text-gray-500 mt-1">María González - Turno: Mañana (07:00 - 15:00)</p>
                </div>
                <div class="flex gap-3">
                    <button class="flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
                        <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Nueva Cita
                    </button>
                    <!-- El botón ahora activa la pestaña -->
                    <button onclick="switchTab('admision')" class="flex items-center px-4 py-2 bg-[#0056b3] text-white font-medium rounded-lg hover:bg-blue-700 transition-colors shadow-md">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        Admisión Rápida
                    </button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col items-center justify-center">
                    <span class="text-gray-500 text-sm font-medium mb-1">Citas Programadas</span>
                    <span class="text-3xl font-bold text-gray-800">24</span>
                </div>
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col items-center justify-center">
                    <span class="text-gray-500 text-sm font-medium mb-1">En Atención</span>
                    <span class="text-3xl font-bold text-green-600">3</span>
                </div>
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col items-center justify-center">
                    <span class="text-gray-500 text-sm font-medium mb-1">En Espera</span>
                    <span class="text-3xl font-bold text-orange-500">5</span>
                </div>
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col items-center justify-center">
                    <span class="text-gray-500 text-sm font-medium mb-1">Admisiones</span>
                    <span class="text-3xl font-bold text-blue-600">8</span>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="bg-gray-200/50 p-1 rounded-lg inline-flex w-full mb-6">
                <button id="btn-agenda" onclick="switchTab('agenda')" class="flex-1 px-4 py-2 bg-white text-gray-800 font-semibold rounded-md shadow-sm text-center text-sm transition-all duration-200">Agenda del Día</button>
                <button id="btn-admision" onclick="switchTab('admision')" class="flex-1 px-4 py-2 text-gray-600 hover:bg-gray-100 font-medium rounded-md text-center text-sm transition-all duration-200">Admisión Rápida</button>
                <button id="btn-llamadas" onclick="switchTab('llamadas')" class="flex-1 px-4 py-2 text-gray-600 hover:bg-gray-100 font-medium rounded-md text-center text-sm transition-all duration-200">Gestión de Llamadas</button>
            </div>

            <!-- Container for Tab Content -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 min-h-[400px]">

                <!-- TAB 1: AGENDA DEL DÍA -->
                <div id="tab-agenda" class="block">
                    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-gray-800 font-semibold">Citas Programadas - 2026-02-03</h3>
                        <span class="text-sm text-gray-500">03/02/2026</span>
                    </div>
                    <div class="divide-y divide-gray-50">
                        <!-- Item 1 -->
                        <div class="p-4 hover:bg-gray-50 transition-colors flex flex-col sm:flex-row items-start sm:items-center gap-4">
                            <div class="flex flex-col items-center justify-center min-w-[60px] text-gray-700">
                                <svg class="w-5 h-5 text-blue-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="font-bold text-sm">08:00</span>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-bold text-gray-900">García, Juan</span>
                                    <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-600 text-[10px] font-bold border border-blue-100 uppercase tracking-wide flex items-center">Confirmado</span>
                                </div>
                                <div class="text-sm text-gray-500 flex items-center flex-wrap gap-2">
                                    <span>DNI: 12345678</span><span class="text-gray-300">•</span><span>Cardiología</span><span class="text-gray-300">•</span><span>Dr. Ramírez</span>
                                </div>
                                <div class="text-xs text-gray-400 mt-1 flex items-center">📞 987654321</div>
                            </div>
                            <div class="flex items-center gap-2 mt-2 sm:mt-0">
                                <button class="bg-[#0056b3] hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg shadow-sm">Registrar Llegada</button>
                                <button class="border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-sm font-medium px-3 py-2 rounded-lg flex items-center shadow-sm">📄 Ficha</button>
                            </div>
                        </div>
                        
                        <!-- Item 2 -->
                        <div class="p-4 hover:bg-gray-50 transition-colors flex flex-col sm:flex-row items-start sm:items-center gap-4 border-l-4 border-green-500 bg-green-50/10">
                            <div class="flex flex-col items-center justify-center min-w-[60px] text-gray-700">
                                <svg class="w-5 h-5 text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="font-bold text-sm">08:30</span>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-bold text-gray-900">López, María</span>
                                    <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-[10px] font-bold border border-green-200 uppercase tracking-wide flex items-center">En Atención</span>
                                </div>
                                <div class="text-sm text-gray-500 flex items-center flex-wrap gap-2">
                                    <span>DNI: 23456789</span><span class="text-gray-300">•</span><span>Pediatría</span><span class="text-gray-300">•</span><span>Dra. Torres</span>
                                </div>
                                <div class="text-xs text-gray-400 mt-1 flex items-center">📞 987654322</div>
                            </div>
                            <div class="flex items-center gap-2 mt-2 sm:mt-0">
                                <button class="border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-sm font-medium px-3 py-2 rounded-lg flex items-center shadow-sm">📄 Ficha</button>
                            </div>
                        </div>

                        <!-- Item 3 -->
                        <div class="p-4 hover:bg-gray-50 transition-colors flex flex-col sm:flex-row items-start sm:items-center gap-4">
                            <div class="flex flex-col items-center justify-center min-w-[60px] text-gray-700">
                                <svg class="w-5 h-5 text-blue-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span class="font-bold text-sm">09:00</span>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-bold text-gray-900">Rodríguez, Pedro</span>
                                    <span class="px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 text-[10px] font-bold border border-orange-200 uppercase tracking-wide flex items-center">En Espera</span>
                                </div>
                                <div class="text-sm text-gray-500 flex items-center flex-wrap gap-2">
                                    <span>DNI: 34567890</span><span class="text-gray-300">•</span><span>Medicina General</span><span class="text-gray-300">•</span><span>Dr. Silva</span>
                                </div>
                                <div class="text-xs text-gray-400 mt-1 flex items-center">📞 987654323</div>
                            </div>
                            <div class="flex items-center gap-2 mt-2 sm:mt-0">
                                <button class="bg-[#10b981] hover:bg-green-600 text-white text-sm font-medium px-4 py-2 rounded-lg shadow-sm">Llamar a Consultorio</button>
                                <button class="border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-sm font-medium px-3 py-2 rounded-lg flex items-center shadow-sm">📄 Ficha</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: ADMISIÓN RÁPIDA -->
                <div id="tab-admision" class="hidden p-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Admisión Rápida de Paciente</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Tipo de Documento -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Documento</label>
                            <select class="form-select border">
                                <option>DNI</option>
                                <option>Pasaporte</option>
                                <option>Carnet de Extranjería</option>
                            </select>
                        </div>
                        
                        <!-- Número de Documento + Botón Buscar -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Número de Documento</label>
                            <div class="flex gap-2">
                                <input type="text" placeholder="Ingrese número" class="form-input flex-1 border" value="12345678">
                                <button class="bg-[#0056b3] hover:bg-blue-700 text-white font-medium px-6 py-2 rounded-lg transition-colors">Buscar</button>
                            </div>
                        </div>
                    </div>

                    <!-- Resultado Búsqueda (Paciente encontrado) -->
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-6 mb-6">
                        <h4 class="text-sm font-semibold text-blue-800 mb-3">Paciente encontrado en el sistema</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Nombre:</p>
                                <p class="font-medium text-gray-900">García Mendoza, Juan Carlos</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">H.C.:</p>
                                <p class="font-medium text-gray-900">HC-001234</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Edad:</p>
                                <p class="font-medium text-gray-900">45 años</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Teléfono:</p>
                                <p class="font-medium text-gray-900">987654321</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Atención</label>
                            <select class="form-select border">
                                <option>Consulta Externa</option>
                                <option>Emergencia</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Especialidad</label>
                            <select class="form-select border">
                                <option>Medicina General</option>
                                <option>Cardiología</option>
                                <option>Pediatría</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Médico</label>
                            <select class="form-select border">
                                <option>Dr. Ramírez, Carlos</option>
                                <option>Dra. Torres, Ana</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Seguro</label>
                            <select class="form-select border">
                                <option>Particular</option>
                                <option>SIS</option>
                                <option>EsSalud</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <button onclick="switchTab('agenda')" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">Cancelar</button>
                        <button class="px-6 py-2.5 bg-[#0056b3] text-white rounded-lg font-medium hover:bg-blue-700 transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            Registrar Admisión
                        </button>
                    </div>
                </div>

                <!-- TAB 3: GESTIÓN DE LLAMADAS -->
                <div id="tab-llamadas" class="hidden p-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Gestión de Llamadas y Confirmaciones</h3>

                    <div class="space-y-4">
                        <!-- Card 1 -->
                        <div class="border border-gray-200 rounded-xl p-6 flex flex-col md:flex-row justify-between items-center bg-white shadow-sm hover:shadow-md transition-shadow">
                            <div class="mb-4 md:mb-0">
                                <h4 class="font-bold text-gray-900 text-lg">Recordatorios de Citas - Mañana</h4>
                                <p class="text-gray-500 mt-1">15 pacientes por confirmar</p>
                            </div>
                            <button class="bg-[#0056b3] hover:bg-blue-700 text-white font-medium px-6 py-2.5 rounded-lg flex items-center transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                Iniciar Llamadas
                            </button>
                        </div>

                        <!-- Card 2 -->
                        <div class="border border-gray-200 rounded-xl p-6 flex flex-col md:flex-row justify-between items-center bg-white shadow-sm hover:shadow-md transition-shadow">
                            <div class="mb-4 md:mb-0">
                                <h4 class="font-bold text-gray-900 text-lg">Seguimiento Post-Alta</h4>
                                <p class="text-gray-500 mt-1">8 pacientes pendientes</p>
                            </div>
                            <button class="border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium px-6 py-2.5 rounded-lg transition-colors">
                                Ver Lista
                            </button>
                        </div>

                        <!-- Card 3 -->
                        <div class="border border-gray-200 rounded-xl p-6 flex flex-col md:flex-row justify-between items-center bg-white shadow-sm hover:shadow-md transition-shadow">
                            <div class="mb-4 md:mb-0">
                                <h4 class="font-bold text-gray-900 text-lg">Confirmación de Cirugías</h4>
                                <p class="text-gray-500 mt-1">3 confirmaciones pendientes</p>
                            </div>
                            <button class="border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium px-6 py-2.5 rounded-lg transition-colors">
                                Ver Lista
                            </button>
                        </div>
                    </div>
                </div>

            </div>

        </main>
    </div>

    <!-- JavaScript para el cambio de pestañas -->
    <script>
        function switchTab(tabName) {
            // Ocultar todos los contenidos
            document.getElementById('tab-agenda').classList.add('hidden');
            document.getElementById('tab-admision').classList.add('hidden');
            document.getElementById('tab-llamadas').classList.add('hidden');
            document.getElementById('tab-agenda').classList.remove('block');
            document.getElementById('tab-admision').classList.remove('block');
            document.getElementById('tab-llamadas').classList.remove('block');

            // Resetear estilos de botones
            const btnClassesInactive = "text-gray-600 hover:bg-gray-100 font-medium";
            const btnClassesActive = "bg-white text-gray-800 font-semibold shadow-sm";
            
            ['agenda', 'admision', 'llamadas'].forEach(tab => {
                const btn = document.getElementById(`btn-${tab}`);
                btn.className = `flex-1 px-4 py-2 rounded-md text-center text-sm transition-all duration-200 ${btnClassesInactive}`;
            });

            // Mostrar contenido seleccionado
            document.getElementById(`tab-${tabName}`).classList.remove('hidden');
            document.getElementById(`tab-${tabName}`).classList.add('block');

            // Activar botón seleccionado
            document.getElementById(`btn-${tabName}`).className = `flex-1 px-4 py-2 rounded-md text-center text-sm transition-all duration-200 ${btnClassesActive}`;
        }
    </script>
</body>
</html>
