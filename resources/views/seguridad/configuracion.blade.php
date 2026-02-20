<x-app-layout>
    <div class="p-8 bg-gray-50/50 min-h-screen" x-data="{ tab: 'general' }">

        <div class="mb-6">
            <div class="flex items-center gap-3 text-blue-600 mb-1">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Configuración del Sistema</h1>
            </div>
            <p class="text-sm text-gray-500 ml-11 font-medium">Ajustes generales y parámetros del HIS/ERP</p>
        </div>

        <div class="bg-gray-200/50 p-1.5 rounded-xl inline-flex w-full mb-6">
            <button @click="tab = 'general'"
                :class="tab === 'general' ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 py-2 text-xs font-bold rounded-lg transition">
                General
            </button>
            <button @click="tab = 'clinica'"
                :class="tab === 'clinica' ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 py-2 text-xs font-bold rounded-lg transition">
                Clínica
            </button>
            <button @click="tab = 'facturacion'"
                :class="tab === 'facturacion' ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 py-2 text-xs font-bold rounded-lg transition">
                Facturación
            </button>
            <button @click="tab = 'notificaciones'"
                :class="tab === 'notificaciones' ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 py-2 text-xs font-bold rounded-lg transition">
                Notificaciones
            </button>
        </div>

        <div x-show="tab === 'general'" class="space-y-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 mb-6">
                <div class="flex items-center gap-2 mb-8">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <h3 class="font-bold text-gray-800 text-sm">Información de la Clínica</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-800 mb-2">Razón Social</label>
                            <input type="text" class="w-full bg-gray-50/50 border-none rounded-xl px-4 py-2.5 text-xs text-gray-600 focus:ring-2 focus:ring-blue-100" value="Clínica CEMSA S.A.C.">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-800 mb-2">Dirección</label>
                            <input type="text" class="w-full bg-gray-50/50 border-none rounded-xl px-4 py-2.5 text-xs text-gray-600 focus:ring-2 focus:ring-blue-100" value="Av. Principal 123, Lima">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-800 mb-2">Email</label>
                            <input type="email" class="w-full bg-gray-50/50 border-none rounded-xl px-4 py-2.5 text-xs text-gray-600 focus:ring-2 focus:ring-blue-100" value="contacto@cemsa.com.pe">
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-800 mb-2">RUC</label>
                            <input type="text" class="w-full bg-gray-50/50 border-none rounded-xl px-4 py-2.5 text-xs text-gray-600 focus:ring-2 focus:ring-blue-100" value="20123456789">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-800 mb-2">Teléfono</label>
                            <input type="text" class="w-full bg-gray-50/50 border-none rounded-xl px-4 py-2.5 text-xs text-gray-600 focus:ring-2 focus:ring-blue-100" value="+51 1 234-5678">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-800 mb-2">Web</label>
                            <input type="text" class="w-full bg-gray-50/50 border-none rounded-xl px-4 py-2.5 text-xs text-gray-600 focus:ring-2 focus:ring-blue-100" value="www.cemsa.com.pe">
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <button class="bg-blue-600 text-white px-6 py-2 rounded-lg text-xs font-bold hover:bg-blue-700 shadow-md shadow-blue-200 transition">
                        Guardar Cambios
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 mb-6">
                <div class="flex items-center gap-2 mb-8">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="font-bold text-gray-800 text-sm">Configuración Regional</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-800 mb-2">Zona Horaria</label>
                            <select class="w-full bg-gray-50/50 border-none rounded-xl px-4 py-2.5 text-xs text-gray-600 focus:ring-2 focus:ring-blue-100 appearance-none">
                                <option>América/Lima (GMT-5)</option>
                                <option>América/Bogotá (GMT-5)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-800 mb-2">Moneda</label>
                            <select class="w-full bg-gray-50/50 border-none rounded-xl px-4 py-2.5 text-xs text-gray-600 focus:ring-2 focus:ring-blue-100 appearance-none">
                                <option>Soles (S/)</option>
                                <option>Dólares ($)</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-800 mb-2">Idioma</label>
                            <select class="w-full bg-gray-50/50 border-none rounded-xl px-4 py-2.5 text-xs text-gray-600 focus:ring-2 focus:ring-blue-100 appearance-none">
                                <option>Español</option>
                                <option>English</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-800 mb-2">Formato de Fecha</label>
                            <select class="w-full bg-gray-50/50 border-none rounded-xl px-4 py-2.5 text-xs text-gray-600 focus:ring-2 focus:ring-blue-100 appearance-none">
                                <option>DD/MM/YYYY</option>
                                <option>YYYY-MM-DD</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="tab === 'clinica'" x-cloak>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 mb-6">
                <div class="flex items-center gap-2 mb-8">
                    <h3 class="font-bold text-gray-800 text-sm">Parámetros Clínicos</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-24 gap-y-8 mb-10">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-800 mb-2 uppercase tracking-wider">Duración Consulta (minutos)</label>
                            <input type="number" class="w-full bg-gray-50/50 border-none rounded-xl px-4 py-2.5 text-xs text-gray-600 focus:ring-2 focus:ring-blue-100" value="30">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-800 mb-2 uppercase tracking-wider">Formato Historia Clínica</label>
                            <input type="text" class="w-full bg-gray-50/50 border-none rounded-xl px-4 py-2.5 text-xs text-gray-600 focus:ring-2 focus:ring-blue-100" value="HC-NNNNNN">
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-800 mb-2 uppercase tracking-wider">Días Vigencia Receta</label>
                            <input type="number" class="w-full bg-gray-50/50 border-none rounded-xl px-4 py-2.5 text-xs text-gray-600 focus:ring-2 focus:ring-blue-100" value="30">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-800 mb-2 uppercase tracking-wider">Alerta Stock Mínimo (%)</label>
                            <input type="number" class="w-full bg-gray-50/50 border-none rounded-xl px-4 py-2.5 text-xs text-gray-600 focus:ring-2 focus:ring-blue-100" value="20">
                        </div>
                    </div>
                </div>

                <div class="space-y-6 border-t border-gray-50 pt-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-800">Requerir Consentimiento Informado</p>
                            <p class="text-[11px] text-gray-400">Para procedimientos invasivos</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-800">Validar Alergias</p>
                            <p class="text-[11px] text-gray-400">Al prescribir medicamentos</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-800">Firma Digital</p>
                            <p class="text-[11px] text-gray-400">Habilitar firma electrónica de documentos</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                </div>

                <div class="mt-10">
                    <button class="bg-blue-600 text-white px-8 py-2.5 rounded-xl text-xs font-bold hover:bg-blue-700 shadow-lg shadow-blue-100 transition-all active:scale-95">
                        Guardar Parámetros
                    </button>
                </div>
            </div>
        </div>

      <div x-show="tab === 'facturacion'" x-cloak>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 mb-6">
                <div class="flex items-center gap-2 mb-8">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="font-bold text-gray-800 text-sm">Configuración de Facturación</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-24 gap-y-8 mb-10">
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-800 mb-2">Serie Facturas</label>
                            <input type="text" class="w-full bg-gray-50/50 border-none rounded-xl px-4 py-2.5 text-xs text-gray-600 focus:ring-2 focus:ring-blue-100" value="F001">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-800 mb-2">Próximo Número</label>
                            <input type="text" class="w-full bg-gray-50/50 border-none rounded-xl px-4 py-2.5 text-xs text-gray-600 focus:ring-2 focus:ring-blue-100" value="00001240">
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-800 mb-2">Serie Boletas</label>
                            <input type="text" class="w-full bg-gray-50/50 border-none rounded-xl px-4 py-2.5 text-xs text-gray-600 focus:ring-2 focus:ring-blue-100" value="B001">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-800 mb-2">Días Crédito</label>
                            <input type="number" class="w-full bg-gray-50/50 border-none rounded-xl px-4 py-2.5 text-xs text-gray-600 focus:ring-2 focus:ring-blue-100" value="30">
                        </div>
                    </div>
                </div>

                <div class="pt-8 border-t border-gray-50">
                    <h4 class="text-[12px] font-bold text-gray-800 mb-6">Conexión SUNAT</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-24 gap-y-8">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-800 mb-2">Usuario SOL</label>
                            <input type="text" class="w-full bg-gray-50/50 border-none rounded-xl px-4 py-2.5 text-xs text-gray-600 focus:ring-2 focus:ring-blue-100" value="20123456789MODDATOS">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-800 mb-2">Clave SOL</label>
                            <input type="password" class="w-full bg-gray-50/50 border-none rounded-xl px-4 py-2.5 text-xs text-gray-600 focus:ring-2 focus:ring-blue-100" value="password123">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-800 mb-2">Certificado Digital</label>
                            <div class="flex items-center gap-3 mt-1">
                                <label class="cursor-pointer bg-gray-100 hover:bg-gray-200 text-gray-700 text-[11px] font-bold py-2.5 px-4 rounded-xl border border-gray-200 transition shadow-sm">
                                    Seleccionar archivo
                                    <input type="file" class="hidden">
                                </label>
                                <span class="text-[11px] text-gray-400 italic truncate">Ningún archivo seleccionado</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-800 mb-2">Contraseña Certificado</label>
                            <input type="password" class="w-full bg-gray-50/50 border-none rounded-xl px-4 py-2.5 text-xs text-gray-600 focus:ring-2 focus:ring-blue-100" placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <div class="mt-12">
                    <button class="bg-blue-600 text-white px-10 py-2.5 rounded-lg text-xs font-bold hover:bg-blue-700 shadow-md shadow-blue-200 transition-all active:scale-95">
                        Guardar Configuración
                    </button>
                </div>
            </div>
        </div>

        <<div x-show="tab === 'notificaciones'" x-cloak class="space-y-6">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 mb-6">
        <div class="flex items-center gap-2 mb-8">
            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <h3 class="font-bold text-gray-800 text-sm">Configuración de Notificaciones</h3>
        </div>

        <div class="space-y-6 mb-10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-800">Email de Confirmación de Citas</p>
                    <p class="text-[11px] text-gray-400">Enviar recordatorios automáticos</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" class="sr-only peer" checked>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>

            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-800">SMS de Recordatorio</p>
                    <p class="text-[11px] text-gray-400">24 horas antes de la cita</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>

            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-800">Alertas de Stock Bajo</p>
                    <p class="text-[11px] text-gray-400">Notificar al responsable de farmacia</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" class="sr-only peer" checked>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>

            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-800">Alertas de Vencimientos</p>
                    <p class="text-[11px] text-gray-400">Productos próximos a vencer</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" class="sr-only peer" checked>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>

            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-800">Notificaciones de Pago</p>
                    <p class="text-[11px] text-gray-400">Enviar comprobante por email</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" class="sr-only peer" checked>
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>
        </div>

        <div class="pt-8 border-t border-gray-50">
            <h4 class="text-[12px] font-bold text-gray-800 mb-6">Configuración SMTP</h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-24 gap-y-8">
                <div>
                    <label class="block text-[11px] font-bold text-gray-800 mb-2">Servidor SMTP</label>
                    <input type="text" class="w-full bg-gray-50/50 border-none rounded-xl px-4 py-2.5 text-xs text-gray-600 focus:ring-2 focus:ring-blue-100" value="smtp.gmail.com">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-800 mb-2">Puerto</label>
                    <input type="number" class="w-full bg-gray-50/50 border-none rounded-xl px-4 py-2.5 text-xs text-gray-600 focus:ring-2 focus:ring-blue-100" value="587">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-800 mb-2">Usuario</label>
                    <input type="email" class="w-full bg-gray-50/50 border-none rounded-xl px-4 py-2.5 text-xs text-gray-600 focus:ring-2 focus:ring-blue-100" value="notificaciones@cemsa.com.pe">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-800 mb-2">Contraseña</label>
                    <input type="password" class="w-full bg-gray-50/50 border-none rounded-xl px-4 py-2.5 text-xs text-gray-600 focus:ring-2 focus:ring-blue-100" value="••••••••">
                </div>
            </div>
        </div>

        <div class="mt-12">
            <button class="bg-blue-600 text-white px-10 py-2.5 rounded-lg text-xs font-bold hover:bg-blue-700 shadow-md shadow-blue-200 transition-all active:scale-95">
                Guardar Configuración
            </button>
        </div>
    </div>
</div>

    </div>




</x-app-layout>
