@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 font-sans" x-data="contabilidad()" x-init="init()">

    {{-- ===== TOAST ===== --}}
    <div x-show="toast.visible"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="fixed bottom-5 right-5 z-50 flex items-center gap-3 px-4 py-3 rounded-xl shadow-lg text-sm font-medium min-w-[240px]"
         :class="toast.type === 'error' ? 'bg-red-600 text-white' : 'bg-emerald-600 text-white'"
         style="display:none">
        <svg x-show="toast.type !== 'error'" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
        </svg>
        <svg x-show="toast.type === 'error'" class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        <span x-text="toast.message" class="flex-1"></span>
        <button @click="toast.visible=false" class="opacity-70 hover:opacity-100 cursor-pointer transition-opacity">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- ===== DRAWER: Registrar Egreso ===== --}}
    <div x-show="drawer" class="fixed inset-0 z-40" style="display:none">
        <div class="absolute inset-0 bg-black/40"
             @click="drawer=false"
             x-transition:enter="transition-opacity ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>
        <div class="absolute inset-y-0 right-0 w-full max-w-md bg-white shadow-2xl flex flex-col overflow-y-auto"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-white sticky top-0 z-10">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Registrar Egreso</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Salida de dinero del período</p>
                </div>
                <button @click="drawer=false" class="p-2 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 cursor-pointer transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form @submit.prevent="guardarEgreso()" class="p-5 space-y-4 flex-1">
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Fecha</label>
                        <input type="date" x-model="form.fecha" required
                            class="w-full border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Método de pago</label>
                        <select x-model="form.metodo_pago" required
                            class="w-full border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="efectivo">Efectivo</option>
                            <option value="transferencia">Transferencia</option>
                            <option value="cheque">Cheque</option>
                            <option value="tarjeta">Tarjeta</option>
                            <option value="qr">QR</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Categoría</label>
                    <select x-model="form.categoria" required
                        class="w-full border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">Seleccionar categoría...</option>
                        @foreach ($categorias as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Descripción</label>
                    <input type="text" x-model="form.descripcion" required maxlength="255"
                        class="w-full border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        placeholder="Ej: Pago sueldo enfermería">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Monto (Bs)</label>
                        <input type="text" inputmode="decimal" x-model="form.monto" required
                            class="w-full border-gray-200 rounded-lg text-sm font-mono focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="0.00">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Comprobante N° (opc.)</label>
                        <input type="text" x-model="form.comprobante_nro" maxlength="50"
                            class="w-full border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Proveedor (opc.)</label>
                    <input type="text" x-model="form.proveedor" maxlength="255"
                        class="w-full border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>

                {{-- Crédito fiscal --}}
                <div class="rounded-xl border border-gray-200 bg-slate-50 p-4">
                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <input type="checkbox" x-model="form.con_credito_fiscal"
                            @change="if (form.con_credito_fiscal) form.aplica_retencion = false"
                            class="rounded border-gray-300 text-green-80h0focus:ring-green-500 w-4 h-4">
                        <span class="text-sm text-gray-700 font-medium">Factura con crédito fiscal (IVA)</span>
                    </label>
                    <div x-show="form.con_credito_fiscal" class="mt-3 space-y-3">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">NIT proveedor</label>
                                <input type="text" x-model="form.nit_proveedor" maxlength="20" inputmode="numeric"
                                    class="w-full border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                    placeholder="1023456789">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">N° factura</label>
                                <input type="text" x-model="form.nro_factura" maxlength="50"
                                    class="w-full border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Cód. autorización / CUF (opc.)</label>
                            <input type="text" x-model="form.codigo_autorizacion" maxlength="100"
                                class="w-full border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        </div>
                        <div class="flex justify-between items-center bg-cyan-50 border border-cyan-100 rounded-lg px-3 py-2 text-sm">
                            <span class="text-gray-600">Crédito fiscal IVA (13%)</span>
                            <span class="font-semibold text-cyan-700 font-mono">Bs. <span x-text="fmt(ivaCalculado())"></span></span>
                        </div>
                    </div>
                </div>

                {{-- Retención --}}
                <div class="rounded-xl border border-gray-200 bg-slate-50 p-4">
                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <input type="checkbox" x-model="form.aplica_retencion"
                            @change="if (form.aplica_retencion) form.con_credito_fiscal = false"
                            class="rounded border-gray-300 text-amber-600 focus:ring-amber-500 w-4 h-4">
                        <span class="text-sm text-gray-700 font-medium">Retención (pago sin factura)</span>
                    </label>
                    <div x-show="form.aplica_retencion" class="mt-3 space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Tipo de retención</label>
                            <select x-model="form.retencion_tipo"
                                class="w-full border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                <option value="servicios">Servicios — IUE 12,5% + IT 3% = 15,5%</option>
                                <option value="bienes">Bienes — IUE 5% + IT 3% = 8%</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Beneficiario</label>
                                <input type="text" x-model="form.proveedor" maxlength="255"
                                    class="w-full border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                    placeholder="Nombre completo">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">NIT/CI</label>
                                <input type="text" x-model="form.nit_proveedor" maxlength="20" inputmode="numeric"
                                    class="w-full border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            </div>
                        </div>
                        <div class="bg-amber-50 border border-amber-100 rounded-lg px-3 py-2.5 text-sm space-y-1.5">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Retención IUE</span>
                                <span class="font-medium font-mono">Bs. <span x-text="fmt(retencionCalc().iue)"></span></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Retención IT</span>
                                <span class="font-medium font-mono">Bs. <span x-text="fmt(retencionCalc().it)"></span></span>
                            </div>
                            <div class="flex justify-between border-t border-amber-200 pt-1.5">
                                <span class="font-medium text-gray-700">Total retenido</span>
                                <span class="font-semibold text-amber-700 font-mono">Bs. <span x-text="fmt(retencionCalc().total)"></span></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">Neto al beneficiario</span>
                                <span class="font-semibold text-emerald-700 font-mono">Bs. <span x-text="fmt(retencionCalc().neto)"></span></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div x-show="periodoCerrado(form.fecha)"
                    class="rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-800 flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Período cerrado — no se pueden registrar egresos.
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" @click="drawer=false"
                        class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl cursor-pointer transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" :disabled="guardando || periodoCerrado(form.fecha)"
                        class="flex-1 px-4 py-2.5 bg-green-600 hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-semibold rounded-xl cursor-pointer transition-colors">
                        <span x-text="guardando ? 'Guardando...' : 'Registrar egreso'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== MODAL: Anular Egreso ===== --}}
    <div x-show="modalAnular.open" class="fixed inset-0 z-40 flex items-center justify-center p-4" style="display:none">
        <div class="absolute inset-0 bg-black/40"
             @click="modalAnular.open=false"
             x-transition:enter="transition-opacity ease-out duration-200"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-150"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
            <div class="flex items-start gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900">Anular egreso</h3>
                    <p class="text-xs text-gray-500 mt-0.5" x-text="modalAnular.egreso ? modalAnular.egreso.descripcion + ' — Bs. ' + fmt(modalAnular.egreso.monto) : ''"></p>
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1.5">Motivo de anulación <span class="text-red-500">*</span></label>
                <textarea x-model="modalAnular.motivo" rows="3"
                    class="w-full border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-none"
                    placeholder="Describa el motivo de la anulación..."></textarea>
            </div>
            <div class="flex gap-3 mt-4">
                <button @click="modalAnular.open=false"
                    class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl cursor-pointer transition-colors">
                    Cancelar
                </button>
                <button @click="confirmarAnular()" :disabled="!modalAnular.motivo.trim()"
                    class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-semibold rounded-xl cursor-pointer transition-colors">
                    Anular
                </button>
            </div>
        </div>
    </div>

    {{-- ===== MODAL: Revertir Anulación ===== --}}
    <div x-show="modalRevertir.open" class="fixed inset-0 z-40 flex items-center justify-center p-4" style="display:none">
        <div class="absolute inset-0 bg-black/40"
             @click="modalRevertir.open=false"
             x-transition:enter="transition-opacity ease-out duration-200"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-150"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
            <h3 class="text-base font-semibold text-gray-900 mb-2">Revertir anulación</h3>
            <p class="text-sm text-gray-600 mb-5"
                x-text="'¿Revertir la anulación de «' + (modalRevertir.egreso ? modalRevertir.egreso.descripcion : '') + '»? Volverá a contar para el flujo de caja.'"></p>
            <div class="flex gap-3">
                <button @click="modalRevertir.open=false"
                    class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl cursor-pointer transition-colors">
                    Cancelar
                </button>
                <button @click="confirmarRevertir()"
                    class="flex-1 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl cursor-pointer transition-colors">
                    Revertir
                </button>
            </div>
        </div>
    </div>

    @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('administrador'))
    {{-- ===== MODAL: Cerrar Período (admin) ===== --}}
    <div x-show="modalCierre.open" class="fixed inset-0 z-40 flex items-center justify-center p-4" style="display:none">
        <div class="absolute inset-0 bg-black/40"
             @click="modalCierre.open=false"
             x-transition:enter="transition-opacity ease-out duration-200"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-in duration-150"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
            <div class="flex items-start gap-3 mb-5">
                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900">Cerrar período contable</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Bloquea registrar y anular egresos en ese mes</p>
                </div>
            </div>
            <form @submit.prevent="cerrarPeriodo()" class="space-y-3">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Mes</label>
                        <select x-model.number="nuevoCierre.mes"
                            class="w-full border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-gray-400">
                            <template x-for="m in meses" :key="m.v">
                                <option :value="m.v" x-text="m.n"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Año</label>
                        <input type="number" x-model.number="nuevoCierre.anio" min="2020" max="2100"
                            class="w-full border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-gray-400">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Observaciones (opc.)</label>
                    <input type="text" x-model="nuevoCierre.observaciones" maxlength="255"
                        class="w-full border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-gray-400"
                        placeholder="Ej: Declarado F-200 junio">
                </div>
                <div class="flex gap-3 pt-1">
                    <button type="button" @click="modalCierre.open=false"
                        class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-xl cursor-pointer transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" :disabled="cerrando"
                        class="flex-1 px-4 py-2.5 bg-gray-800 hover:bg-gray-900 disabled:opacity-50 text-white text-sm font-semibold rounded-xl cursor-pointer transition-colors">
                        <span x-text="cerrando ? 'Cerrando...' : 'Cerrar período'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- ===== MAIN PAGE ===== --}}
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">

            {{-- Header --}}
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Libro de Caja</h1>
                    <p class="text-gray-500 text-sm mt-0.5">Ingresos automáticos y egresos manuales</p>
                </div>
                <div class="flex flex-wrap items-end gap-2">
                    <div class="flex-1 min-w-[140px]">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Desde</label>
                        <input type="date" x-model="filtros.fecha_inicio" @change="cargar()"
                            class="w-full border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <div class="flex-1 min-w-[140px]">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Hasta</label>
                        <input type="date" x-model="filtros.fecha_fin" @change="cargar()"
                            class="w-full border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>
                    <a :href="urlExportar()"
                        class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg h-[38px] whitespace-nowrap cursor-pointer transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Excel
                    </a>
                    <a :href="urlExportarRcv()" title="Registro de Compras y Ventas + impuestos"
                        class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-700 hover:bg-slate-800 text-white text-sm font-medium rounded-lg h-[38px] whitespace-nowrap cursor-pointer transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        RCV
                    </a>
                    <a href="{{ route('caja.contabilidad.homologacion-sin') }}" title="Homologación codigoProductoSin + dosificación"
                        class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg h-[38px] cursor-pointer transition-colors">
                        SIN
                    </a>
                    <a href="{{ route('caja.gestion.index') }}"
                        class="flex-1 sm:flex-none inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg h-[38px] cursor-pointer transition-colors">
                        Volver
                    </a>
                </div>
            </div>

            {{-- KPI Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Ingresos</p>
                        <p class="text-xl font-bold text-emerald-600 mt-0.5 font-mono">Bs. <span x-text="fmt(totales.ingresos)"></span></p>
                        <p class="text-xs text-gray-400 mt-0.5">Caja + Farmacia</p>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Egresos</p>
                        <p class="text-xl font-bold text-red-600 mt-0.5 font-mono">Bs. <span x-text="fmt(totales.egresos)"></span></p>
                        <p class="text-xs text-gray-400 mt-0.5">Gastos del período</p>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4"
                    :class="parseFloat(totales.saldo) >= 0 ? 'border-l-4 border-l-cyan-400' : 'border-l-4 border-l-orange-400'">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0"
                        :class="parseFloat(totales.saldo) >= 0 ? 'bg-cyan-50' : 'bg-orange-50'">
                        <svg class="w-6 h-6" :class="parseFloat(totales.saldo) >= 0 ? 'text-cyan-600' : 'text-orange-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 19h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Saldo Neto</p>
                        <p class="text-xl font-bold mt-0.5 font-mono" :class="parseFloat(totales.saldo) >= 0 ? 'text-cyan-700' : 'text-orange-600'">
                            Bs. <span x-text="fmt(totales.saldo)"></span>
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">Ingresos − Egresos</p>
                    </div>
                </div>
            </div>

            {{-- Chart --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
                <div class="px-5 py-3.5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <h3 class="text-sm font-semibold text-gray-900">Flujo diario del período</h3>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-500">
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-emerald-500 inline-block"></span>Caja</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-cyan-500 inline-block"></span>Farmacia</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-red-400 inline-block"></span>Egresos</span>
                    </div>
                </div>
                <div class="p-4 h-64">
                    <canvas id="graficoFlujo"></canvas>
                </div>
            </div>

            {{-- Main 2-column grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-[240px_1fr] gap-6">

                {{-- === LEFT SIDEBAR === --}}
                <div class="space-y-3">

                    {{-- Registrar egreso CTA --}}
                    <button @click="drawer=true"
                        class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl cursor-pointer transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        Registrar egreso
                    </button>

                    @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('administrador'))
                    <button @click="modalCierre.open=true"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-white hover:bg-gray-50 border border-gray-200 text-gray-700 text-sm font-medium rounded-xl cursor-pointer transition-colors">
                        <svg class="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Cerrar período
                    </button>
                    @endif

                    {{-- Egresos por categoría --}}
                    <div x-show="egresosPorCategoria.length" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <h3 class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Por categoría</h3>
                        </div>
                        <div class="p-4 space-y-3">
                            <template x-for="c in egresosPorCategoria" :key="c.label">
                                <div>
                                    <div class="flex justify-between text-xs mb-1">
                                        <span class="text-gray-600 truncate pr-2" x-text="c.label"></span>
                                        <span class="font-semibold text-gray-800 font-mono shrink-0">Bs. <span x-text="fmt(c.total)"></span></span>
                                    </div>
                                    <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-red-400 rounded-full transition-all duration-500"
                                            :style="`width:${pctCategoria(c.total)}%`"></div>
                                    </div>
                                </div>
                            </template>
                            <div class="flex justify-between text-xs pt-2 mt-1 border-t border-gray-100"
                                x-show="parseFloat(totales.credito_fiscal || 0) > 0">
                                <span class="text-cyan-700 font-medium">Crédito fiscal IVA</span>
                                <span class="font-semibold text-cyan-700 font-mono">Bs. <span x-text="fmt(totales.credito_fiscal)"></span></span>
                            </div>
                        </div>
                    </div>

                    {{-- Retenciones del período --}}
                    <div x-show="parseFloat(totales.retencion_total || 0) > 0" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <h3 class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Retenciones (F-570)</h3>
                        </div>
                        <div class="p-4 space-y-2 text-xs">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Retención IUE</span>
                                <span class="font-medium font-mono">Bs. <span x-text="fmt(totales.retencion_iue)"></span></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Retención IT</span>
                                <span class="font-medium font-mono">Bs. <span x-text="fmt(totales.retencion_it)"></span></span>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-gray-100">
                                <span class="font-semibold text-gray-700">Total retenido</span>
                                <span class="font-semibold text-amber-600 font-mono">Bs. <span x-text="fmt(totales.retencion_total)"></span></span>
                            </div>
                        </div>
                    </div>

                    {{-- Posición IVA del período (débito ventas − crédito compras) --}}
                    <div x-show="parseFloat(totales.debito_fiscal || 0) > 0 || parseFloat(totales.credito_fiscal || 0) > 0"
                        class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <h3 class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Posición IVA</h3>
                        </div>
                        <div class="p-4 space-y-2 text-xs">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Débito fiscal (ventas)</span>
                                <span class="font-medium font-mono">Bs. <span x-text="fmt(totales.debito_fiscal)"></span></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Crédito fiscal (compras)</span>
                                <span class="font-medium font-mono">Bs. <span x-text="fmt(totales.credito_fiscal)"></span></span>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-gray-100">
                                <span class="font-semibold text-gray-700" x-text="parseFloat(totales.posicion_iva || 0) >= 0 ? 'IVA a pagar' : 'Saldo a favor'"></span>
                                <span class="font-semibold font-mono" :class="parseFloat(totales.posicion_iva || 0) >= 0 ? 'text-red-600' : 'text-emerald-600'">Bs. <span x-text="fmt(Math.abs(parseFloat(totales.posicion_iva || 0)))"></span></span>
                            </div>
                        </div>
                    </div>

                    {{-- Bases IT / IUE (referencial) --}}
                    <div x-show="parseFloat(totales.it_3 || 0) > 0"
                        class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <h3 class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Bases IT / IUE</h3>
                            <p class="text-[11px] text-gray-400 mt-0.5">Referencial — el contador arma F-400 / F-500.</p>
                        </div>
                        <div class="p-4 space-y-2 text-xs">
                            <div class="flex justify-between">
                                <span class="text-gray-500">IT (3% sobre ventas)</span>
                                <span class="font-medium font-mono">Bs. <span x-text="fmt(totales.it_3)"></span></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Utilidad estimada</span>
                                <span class="font-medium font-mono">Bs. <span x-text="fmt(totales.utilidad_estimada)"></span></span>
                            </div>
                            <div class="flex justify-between pt-2 border-t border-gray-100">
                                <span class="font-semibold text-gray-700">IUE estimado (25%)</span>
                                <span class="font-semibold text-indigo-600 font-mono">Bs. <span x-text="fmt(totales.iue_estimado)"></span></span>
                            </div>
                        </div>
                    </div>

                    {{-- Admin: lista de períodos cerrados --}}
                    @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('administrador'))
                    <div x-show="cierres.length" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <h3 class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Períodos cerrados</h3>
                        </div>
                        <div class="p-3 space-y-1.5">
                            <template x-for="c in cierres" :key="c.id">
                                <div class="flex items-center justify-between px-3 py-2 bg-gray-50 rounded-lg">
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold text-gray-800" x-text="c.etiqueta"></p>
                                        <p class="text-[10px] text-gray-400 truncate" x-text="c.cerrado_por"></p>
                                    </div>
                                    <button @click="reabrirPeriodo(c)"
                                        class="text-[11px] text-green-600 hover:text-green-800 font-medium ml-2 cursor-pointer whitespace-nowrap">
                                        Reabrir
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                    @endif

                </div>

                {{-- === MAIN CONTENT (tabs) === --}}
                <div>

                    {{-- Tab bar --}}
                    <div class="flex p-1 bg-white rounded-xl shadow-sm border border-gray-100 mb-4 gap-1">
                        <button @click="tab='egresos'"
                            class="flex-1 px-4 py-2 text-sm font-medium rounded-lg transition-colors cursor-pointer"
                            :class="tab==='egresos' ? 'bg-green-600 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'">
                            Egresos <span class="ml-1 text-xs opacity-70" x-text="'(' + egresos.length + ')'"></span>
                        </button>
                        <button @click="tab='ingresos'"
                            class="flex-1 px-4 py-2 text-sm font-medium rounded-lg transition-colors cursor-pointer"
                            :class="tab==='ingresos' ? 'bg-green-600 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'">
                            Ingresos <span class="ml-1 text-xs opacity-70" x-text="'(' + ingresos.length + ')'"></span>
                        </button>
                    </div>

                    {{-- Tab: Egresos --}}
                    <div x-show="tab==='egresos'">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-gray-900">Egresos del período</h3>
                                <div class="flex items-center gap-3">
                                    <span class="text-xs text-gray-400" x-text="egresos.length + ' registros'"></span>
                                    <button @click="drawer=true"
                                        class="sm:hidden text-xs font-semibold text-green-600 hover:text-green-800 cursor-pointer">
                                        + Nuevo
                                    </button>
                                </div>
                            </div>
                            {{-- Desktop table --}}
                            <div class="overflow-x-auto hidden sm:block">
                                <table class="min-w-full divide-y divide-gray-50 text-sm">
                                    <thead>
                                        <tr class="bg-gray-50/70">
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Fecha</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Categoría</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Descripción</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Método</th>
                                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Monto</th>
                                            <th class="px-4 py-3"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        <template x-for="e in egresos" :key="e.id">
                                            <tr class="hover:bg-slate-50/60 transition-colors" :class="e.anulado ? 'opacity-55' : ''">
                                                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500" x-text="e.fecha"></td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-700" x-text="e.categoria"></span>
                                                </td>
                                                <td class="px-4 py-3 max-w-xs">
                                                    <div class="flex items-center gap-1.5 flex-wrap">
                                                        <span :class="e.anulado ? 'line-through text-gray-400' : 'text-gray-800 font-medium'" x-text="e.descripcion"></span>
                                                        <span x-show="e.anulado" class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700 uppercase tracking-wide">Anulado</span>
                                                        <span x-show="periodoCerrado(e.fecha_iso)" class="text-gray-300 text-base leading-none" title="Período cerrado">&#128274;</span>
                                                    </div>
                                                    <p class="text-xs text-gray-400 mt-0.5" x-show="e.proveedor" x-text="e.proveedor"></p>
                                                    <p class="text-xs text-cyan-600 mt-0.5" x-show="e.con_credito_fiscal && !e.anulado"
                                                        x-text="'F°' + e.nro_factura + ' · IVA Bs. ' + fmt(e.importe_iva)"></p>
                                                    <p class="text-xs text-amber-600 mt-0.5" x-show="e.aplica_retencion && !e.anulado"
                                                        x-text="'Ret. Bs. ' + fmt(e.retencion_total) + ' · Neto Bs. ' + fmt(e.neto_pagado)"></p>
                                                    <p class="text-xs text-red-500 mt-0.5" x-show="e.anulado"
                                                        x-text="'Por ' + e.anulado_por + ' · ' + e.anulado_at + ' — ' + e.motivo_anulacion"></p>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500 capitalize" x-text="e.metodo_pago"></td>
                                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                                    <span class="font-semibold font-mono"
                                                        :class="e.anulado ? 'text-gray-400 line-through' : 'text-red-600'">
                                                        Bs. <span x-text="fmt(e.monto)"></span>
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                                    <div class="flex items-center justify-end gap-3">
                                                        <a x-show="e.aplica_retencion && !e.anulado"
                                                            :href="'{{ url('contabilidad/egresos') }}/' + e.id + '/comprobante-retencion'"
                                                            target="_blank"
                                                            class="inline-flex items-center gap-1 text-xs text-amber-600 hover:text-amber-800 font-medium cursor-pointer">
                                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                            </svg>
                                                            RET
                                                        </a>
                                                        <button x-show="!periodoCerrado(e.fecha_iso) && !e.anulado"
                                                            @click="abrirAnular(e)"
                                                            class="text-xs text-red-600 hover:text-red-800 font-medium cursor-pointer">
                                                            Anular
                                                        </button>
                                                        <button x-show="!periodoCerrado(e.fecha_iso) && e.anulado"
                                                            @click="abrirRevertir(e)"
                                                            class="text-xs text-green-600 hover:text-green-800 font-medium cursor-pointer">
                                                            Revertir
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr x-show="!egresos.length">
                                            <td colspan="6" class="px-4 py-12 text-center">
                                                <svg class="w-8 h-8 text-gray-200 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <p class="text-sm text-gray-400">Sin egresos en el período</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            {{-- Mobile cards --}}
                            <div class="sm:hidden divide-y divide-gray-50">
                                <template x-for="e in egresos" :key="e.id">
                                    <div class="p-4" :class="e.anulado ? 'opacity-55' : ''">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <span class="font-medium text-sm"
                                                        :class="e.anulado ? 'line-through text-gray-400' : 'text-gray-900'"
                                                        x-text="e.descripcion"></span>
                                                    <span x-show="e.anulado" class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700 uppercase">Anulado</span>
                                                </div>
                                                <p class="text-xs text-gray-400 mt-0.5" x-show="e.proveedor" x-text="e.proveedor"></p>
                                                <p class="text-xs text-cyan-600 mt-0.5" x-show="e.con_credito_fiscal && !e.anulado"
                                                    x-text="'F°' + e.nro_factura + ' · IVA Bs. ' + fmt(e.importe_iva)"></p>
                                                <p class="text-xs text-amber-600 mt-0.5" x-show="e.aplica_retencion && !e.anulado"
                                                    x-text="'Ret. Bs. ' + fmt(e.retencion_total) + ' · Neto Bs. ' + fmt(e.neto_pagado)"></p>
                                                <a x-show="e.aplica_retencion && !e.anulado"
                                                    :href="'{{ url('contabilidad/egresos') }}/' + e.id + '/comprobante-retencion'"
                                                    target="_blank" class="text-xs text-amber-600 font-medium underline">
                                                    Comprobante retención
                                                </a>
                                                <p class="text-xs text-red-500 mt-0.5" x-show="e.anulado"
                                                    x-text="'Anulado: ' + e.motivo_anulacion"></p>
                                                <div class="flex flex-wrap items-center gap-x-1.5 gap-y-0.5 mt-1.5 text-xs text-gray-400">
                                                    <span x-text="e.fecha"></span>
                                                    <span>·</span>
                                                    <span x-text="e.categoria"></span>
                                                    <span>·</span>
                                                    <span class="capitalize" x-text="e.metodo_pago"></span>
                                                </div>
                                            </div>
                                            <div class="text-right shrink-0">
                                                <p class="font-bold whitespace-nowrap font-mono"
                                                    :class="e.anulado ? 'text-gray-400 line-through' : 'text-red-600'">
                                                    Bs. <span x-text="fmt(e.monto)"></span>
                                                </p>
                                                <div class="flex flex-col items-end gap-1 mt-1.5">
                                                    <span x-show="periodoCerrado(e.fecha_iso)" class="text-gray-300 text-base">&#128274;</span>
                                                    <button x-show="!periodoCerrado(e.fecha_iso) && !e.anulado" @click="abrirAnular(e)"
                                                        class="text-xs text-red-600 hover:text-red-800 font-medium cursor-pointer">Anular</button>
                                                    <button x-show="!periodoCerrado(e.fecha_iso) && e.anulado" @click="abrirRevertir(e)"
                                                        class="text-xs text-green-600 hover:text-green-800 font-medium cursor-pointer">Revertir</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <p x-show="!egresos.length" class="p-8 text-center text-gray-400 text-sm">Sin egresos en el período</p>
                            </div>
                        </div>
                    </div>

                    {{-- Tab: Ingresos --}}
                    <div x-show="tab==='ingresos'" class="space-y-4">
                        {{-- Ingresos por método --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3" x-show="Object.keys(ingresosPorMetodo).length">
                            <template x-for="(monto, metodo) in ingresosPorMetodo" :key="metodo">
                                <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
                                    <p class="text-xs text-gray-500 capitalize font-medium" x-text="metodo"></p>
                                    <p class="text-base font-bold text-emerald-600 font-mono mt-1">Bs. <span x-text="fmt(monto)"></span></p>
                                </div>
                            </template>
                        </div>
                        {{-- Ingresos table --}}
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                                <h3 class="text-sm font-semibold text-gray-900">Ingresos del período</h3>
                                <span class="text-xs text-gray-400" x-text="ingresos.length + ' registros'"></span>
                            </div>
                            {{-- Desktop --}}
                            <div class="overflow-x-auto hidden sm:block">
                                <table class="min-w-full divide-y divide-gray-50 text-sm">
                                    <thead>
                                        <tr class="bg-gray-50/70">
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Fecha</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Origen</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Paciente</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Descripción</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Método</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Cajero</th>
                                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        <template x-for="i in ingresos" :key="i.id">
                                            <tr class="hover:bg-slate-50/60 transition-colors">
                                                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500" x-text="i.fecha"></td>
                                                <td class="px-4 py-3 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                                        :class="i.origen === 'Farmacia' ? 'bg-cyan-100 text-cyan-700' : 'bg-emerald-100 text-emerald-700'"
                                                        x-text="i.origen"></span>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap font-medium text-gray-900" x-text="i.paciente"></td>
                                                <td class="px-4 py-3">
                                                    <span x-text="i.descripcion" class="text-gray-700"></span>
                                                    <span class="block text-xs text-gray-400" x-text="i.cuenta_id"></span>
                                                    <span class="block text-xs text-gray-400" x-show="i.referencia" x-text="'Ref: ' + i.referencia"></span>
                                                </td>
                                                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500" x-text="i.metodo_pago"></td>
                                                <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500" x-text="i.usuario"></td>
                                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                                    <span class="font-semibold text-emerald-600 font-mono">Bs. <span x-text="fmt(i.monto)"></span></span>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr x-show="!ingresos.length">
                                            <td colspan="7" class="px-4 py-12 text-center">
                                                <svg class="w-8 h-8 text-gray-200 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <p class="text-sm text-gray-400">Sin ingresos en el período</p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            {{-- Mobile --}}
                            <div class="sm:hidden divide-y divide-gray-50">
                                <template x-for="i in ingresos" :key="i.id">
                                    <div class="p-4">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                                        :class="i.origen === 'Farmacia' ? 'bg-cyan-100 text-cyan-700' : 'bg-emerald-100 text-emerald-700'"
                                                        x-text="i.origen"></span>
                                                    <span class="font-medium text-gray-900 text-sm truncate" x-text="i.paciente"></span>
                                                </div>
                                                <p class="text-sm text-gray-600" x-text="i.descripcion"></p>
                                                <p class="text-xs text-gray-400" x-text="i.cuenta_id"></p>
                                                <p class="text-xs text-gray-400" x-show="i.referencia" x-text="'Ref: ' + i.referencia"></p>
                                                <div class="flex flex-wrap items-center gap-x-1.5 mt-1.5 text-xs text-gray-400">
                                                    <span x-text="i.fecha"></span>
                                                    <span>·</span>
                                                    <span class="capitalize" x-text="i.metodo_pago"></span>
                                                    <template x-if="i.usuario"><span> · <span x-text="i.usuario"></span></span></template>
                                                </div>
                                            </div>
                                            <p class="font-bold text-emerald-600 whitespace-nowrap shrink-0 font-mono">Bs. <span x-text="fmt(i.monto)"></span></p>
                                        </div>
                                    </div>
                                </template>
                                <p x-show="!ingresos.length" class="p-8 text-center text-gray-400 text-sm">Sin ingresos en el período</p>
                            </div>
                        </div>
                    </div>

                </div>{{-- /main content --}}
            </div>{{-- /grid --}}
        </div>{{-- /max-w --}}
    </div>{{-- /padding --}}
</div>{{-- /root --}}
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
function contabilidad() {
    const hoy = new Date();
    const primerDia = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
    const mesAnterior = new Date(hoy.getFullYear(), hoy.getMonth() - 1, 1);
    const iso = d => d.toISOString().slice(0, 10);

    return {
        filtros: { fecha_inicio: iso(primerDia), fecha_fin: iso(hoy) },
        totales: { ingresos: '0', egresos: '0', credito_fiscal: '0', saldo: '0', retencion_iue: '0', retencion_it: '0', retencion_total: '0' },
        ingresosPorMetodo: {},
        ingresos: [],
        egresosPorCategoria: [],
        egresos: [],
        guardando: false,
        chart: null,
        cierres: [],
        cerrando: false,
        tab: 'egresos',
        drawer: false,
        modalAnular: { open: false, egreso: null, motivo: '' },
        modalRevertir: { open: false, egreso: null },
        modalCierre: { open: false },
        toast: { visible: false, message: '', type: 'success', _timer: null },
        nuevoCierre: { mes: mesAnterior.getMonth() + 1, anio: mesAnterior.getFullYear(), observaciones: '' },
        meses: [
            { v: 1, n: 'Enero' }, { v: 2, n: 'Febrero' }, { v: 3, n: 'Marzo' },
            { v: 4, n: 'Abril' }, { v: 5, n: 'Mayo' }, { v: 6, n: 'Junio' },
            { v: 7, n: 'Julio' }, { v: 8, n: 'Agosto' }, { v: 9, n: 'Septiembre' },
            { v: 10, n: 'Octubre' }, { v: 11, n: 'Noviembre' }, { v: 12, n: 'Diciembre' },
        ],
        form: {
            fecha: iso(hoy),
            categoria: '', descripcion: '', monto: '',
            metodo_pago: 'efectivo', proveedor: '', comprobante_nro: '',
            con_credito_fiscal: false, nit_proveedor: '', nro_factura: '', codigo_autorizacion: '',
            aplica_retencion: false, retencion_tipo: 'servicios',
        },

        init() {
            this.cargar();
            this.cargarCierres();
        },

        formVacio() {
            return {
                fecha: iso(new Date()),
                categoria: '', descripcion: '', monto: '',
                metodo_pago: 'efectivo', proveedor: '', comprobante_nro: '',
                con_credito_fiscal: false, nit_proveedor: '', nro_factura: '', codigo_autorizacion: '',
                aplica_retencion: false, retencion_tipo: 'servicios',
            };
        },

        showToast(message, type = 'success') {
            clearTimeout(this.toast._timer);
            this.toast.message = message;
            this.toast.type = type;
            this.toast.visible = true;
            this.toast._timer = setTimeout(() => { this.toast.visible = false; }, 4000);
        },

        ivaCalculado() {
            return parseFloat(this.form.monto || 0) * 0.13;
        },

        retencionCalc() {
            const monto = parseFloat(this.form.monto || 0);
            const tasas = this.form.retencion_tipo === 'bienes'
                ? { iue: 0.05, it: 0.03 }
                : { iue: 0.125, it: 0.03 };
            const iue = monto * tasas.iue;
            const it = monto * tasas.it;
            return { iue, it, total: iue + it, neto: monto - iue - it };
        },

        pctCategoria(total) {
            const max = Math.max(...this.egresosPorCategoria.map(c => parseFloat(c.total || 0)));
            return max > 0 ? (parseFloat(total) / max * 100).toFixed(1) : 0;
        },

        fmt(v) {
            return parseFloat(v || 0).toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        urlExportar() {
            const p = new URLSearchParams(this.filtros);
            return `{{ route('caja.contabilidad.exportar') }}?${p.toString()}`;
        },

        urlExportarRcv() {
            const p = new URLSearchParams(this.filtros);
            return `{{ route('caja.contabilidad.exportar-rcv') }}?${p.toString()}`;
        },

        async cargar() {
            const p = new URLSearchParams(this.filtros);
            const res = await fetch(`{{ route('caja.contabilidad.resumen') }}?${p.toString()}`);
            const data = await res.json();
            if (!data.success) { this.showToast(data.message, 'error'); return; }
            this.totales = data.totales;
            this.ingresosPorMetodo = data.ingresos_por_metodo;
            this.ingresos = data.ingresos ?? [];
            this.egresosPorCategoria = data.egresos_por_categoria;
            this.egresos = data.egresos;
            this.renderChart(data.serie);
        },

        renderChart(serie) {
            const ctx = document.getElementById('graficoFlujo');
            if (this.chart) {
                this.chart.data.labels = serie.labels;
                this.chart.data.datasets[0].data = serie.ingresos;
                this.chart.data.datasets[1].data = serie.farmacia;
                this.chart.data.datasets[2].data = serie.egresos;
                this.chart.update();
                return;
            }
            this.chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: serie.labels,
                    datasets: [
                        { label: 'Ingresos caja', data: serie.ingresos, backgroundColor: '#10b981', borderRadius: 4 },
                        { label: 'Ingresos farmacia', data: serie.farmacia, backgroundColor: '#06b6d4', borderRadius: 4 },
                        { label: 'Egresos', data: serie.egresos, backgroundColor: '#f87171', borderRadius: 4 },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: c => `${c.dataset.label}: Bs. ${this.fmt(c.raw)}`,
                            },
                        },
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { callback: v => 'Bs. ' + v }, grid: { color: '#f1f5f9' } },
                        x: { grid: { display: false } },
                    },
                },
            });
        },

        async guardarEgreso() {
            this.guardando = true;
            try {
                const res = await fetch('{{ route('caja.contabilidad.egresos.store') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(this.form),
                });
                const data = await res.json();
                if (!data.success) { this.showToast(data.message, 'error'); return; }
                this.form = this.formVacio();
                this.drawer = false;
                this.showToast('Egreso registrado correctamente');
                await this.cargar();
            } finally {
                this.guardando = false;
            }
        },

        abrirAnular(e) {
            this.modalAnular.egreso = e;
            this.modalAnular.motivo = '';
            this.modalAnular.open = true;
        },

        async confirmarAnular() {
            const e = this.modalAnular.egreso;
            const motivo = this.modalAnular.motivo.trim();
            if (!motivo) return;
            const res = await fetch(`{{ url('contabilidad/egresos') }}/${e.id}/anular`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ motivo }),
            });
            const data = await res.json();
            this.modalAnular.open = false;
            if (!data.success) { this.showToast(data.message, 'error'); return; }
            this.showToast('Egreso anulado');
            await this.cargar();
        },

        abrirRevertir(e) {
            this.modalRevertir.egreso = e;
            this.modalRevertir.open = true;
        },

        async confirmarRevertir() {
            const e = this.modalRevertir.egreso;
            const res = await fetch(`{{ url('contabilidad/egresos') }}/${e.id}/revertir`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            });
            const data = await res.json();
            this.modalRevertir.open = false;
            if (!data.success) { this.showToast(data.message, 'error'); return; }
            this.showToast('Anulación revertida');
            await this.cargar();
        },

        periodoCerrado(fechaIso) {
            if (!fechaIso) return false;
            const [y, m] = fechaIso.split('-').map(Number);
            return this.cierres.some(c => c.anio === y && c.mes === m);
        },

        async cargarCierres() {
            const res = await fetch('{{ route('caja.contabilidad.cierres.index') }}');
            const data = await res.json();
            if (data.success) this.cierres = data.cierres;
        },

        async cerrarPeriodo() {
            this.cerrando = true;
            try {
                const res = await fetch('{{ route('caja.contabilidad.cierres.store') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(this.nuevoCierre),
                });
                const data = await res.json();
                if (!data.success) { this.showToast(data.message, 'error'); return; }
                this.nuevoCierre.observaciones = '';
                this.modalCierre.open = false;
                this.showToast(data.message);
                await this.cargarCierres();
                await this.cargar();
            } finally {
                this.cerrando = false;
            }
        },

        async reabrirPeriodo(c) {
            const res = await fetch(`{{ url('contabilidad/cierres') }}/${c.id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            });
            const data = await res.json();
            if (!data.success) { this.showToast(data.message, 'error'); return; }
            this.showToast(data.message);
            await this.cargarCierres();
            await this.cargar();
        },
    };
}
</script>
@endpush
