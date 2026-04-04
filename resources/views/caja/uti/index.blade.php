<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caja - UTI</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen" x-data="utiCaja()">
        @include('layouts.navigation')

        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Caja UTI</h1>
                        <p class="text-sm text-gray-500">Cobro de pacientes en Terapia Intensiva</p>
                    </div>
                    <div class="flex gap-3">
                        <button @click="loadPacientes()" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-auto p-6">
                <div class="max-w-7xl mx-auto">
                    <!-- Filters -->
                    <div class="bg-white rounded-lg shadow p-4 mb-4">
                        <div class="flex gap-4">
                            <select x-model="filtroEstado" @change="loadPacientes()" class="border-gray-300 rounded-lg text-sm">
                                <option value="todos">Todos los estados</option>
                                <option value="activo">En UTI</option>
                                <option value="alta_clinica">Listos para Alta</option>
                            </select>
                            <div class="flex-1"></div>
                            <div class="text-sm text-gray-500">
                                Total pacientes: <span class="font-medium" x-text="pacientes.length"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Pacientes Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <template x-for="p in pacientes" :key="p.id">
                            <div class="bg-white rounded-lg shadow-md overflow-hidden" :class="{'border-2 border-green-400': p.listo_para_cobro}">
                                <div class="p-4 border-b border-gray-100">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <span class="text-xs font-medium px-2 py-1 rounded-full" :class="{
                                                'bg-green-100 text-green-800': p.estado === 'alta_clinica',
                                                'bg-blue-100 text-blue-800': p.estado === 'activo'
                                            }" x-text="p.estado_label"></span>
                                            <span x-show="p.listo_para_cobro" class="ml-2 text-xs font-medium text-green-600">✓ Listo para cobro</span>
                                        </div>
                                        <span class="text-xs text-gray-500" x-text="p.cama"></span>
                                    </div>
                                    <h3 class="font-bold text-gray-900" x-text="p.paciente.nombre"></h3>
                                    <p class="text-sm text-gray-500">CI: <span x-text="p.paciente.ci"></span></p>
                                </div>
                                <div class="p-4 space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Días en UTI:</span>
                                        <span class="font-medium" x-text="p.dias_en_uti"></span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Tipo de pago:</span>
                                        <span class="font-medium" x-text="p.seguro"></span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Total acumulado:</span>
                                        <span class="font-medium text-lg" x-text="'$' + p.cuenta.total.toFixed(2)"></span>
                                    </div>
                                    <div x-show="p.cuenta.depositos_realizados > 0" class="flex justify-between text-sm">
                                        <span class="text-gray-500">Depósitos:</span>
                                        <span class="font-medium text-green-600" x-text="'$' + p.cuenta.depositos_realizados.toFixed(2)"></span>
                                    </div>
                                    <div x-show="p.cuenta.saldo_pendiente > 0" class="flex justify-between text-sm">
                                        <span class="text-gray-500">Saldo pendiente:</span>
                                        <span class="font-medium text-red-600" x-text="'$' + p.cuenta.saldo_pendiente.toFixed(2)"></span>
                                    </div>
                                </div>
                                <div class="p-4 bg-gray-50">
                                    <button @click="verDetalleCuenta(p)" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                                        Ver Detalle de Cuenta
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div x-show="pacientes.length === 0" class="text-center py-12">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a1 1 0 11-2 0 1 1 0 012 0z"/></svg>
                        <p class="text-gray-500">No hay pacientes UTI para cobrar</p>
                    </div>
                </div>
            </main>
        </div>

        <!-- Modal Detalle Cuenta -->
        <div x-show="showDetalleModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full m-4 max-h-[90vh] overflow-auto">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-xl font-bold" x-text="selectedPaciente?.paciente?.nombre"></h3>
                            <p class="text-sm text-gray-500">CI: <span x-text="selectedPaciente?.paciente?.ci"></span> | Ingreso: <span x-text="selectedPaciente?.nro_ingreso"></span></p>
                        </div>
                        <button @click="showDetalleModal = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Información del Ingreso</h4>
                            <div class="space-y-1 text-sm">
                                <p><span class="text-gray-500">Fecha ingreso:</span> <span x-text="detalleCuenta?.ingreso?.fecha_ingreso"></span></p>
                                <p><span class="text-gray-500">Días en UTI:</span> <span x-text="detalleCuenta?.ingreso?.dias_en_uti"></span></p>
                                <p><span class="text-gray-500">Cama:</span> <span x-text="detalleCuenta?.ingreso?.cama"></span></p>
                                <p><span class="text-gray-500">Diagnóstico:</span> <span x-text="detalleCuenta?.ingreso?.diagnostico"></span></p>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">Resumen de Cuenta</h4>
                            <div class="space-y-1 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Estadía:</span>
                                    <span x-text="'$' + (detalleCuenta?.cuenta?.estadia?.subtotal || 0).toFixed(2)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Medicamentos:</span>
                                    <span x-text="'$' + (detalleCuenta?.cuenta?.medicamentos || 0).toFixed(2)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Insumos:</span>
                                    <span x-text="'$' + (detalleCuenta?.cuenta?.insumos || 0).toFixed(2)"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Alimentación:</span>
                                    <span x-text="'$' + (detalleCuenta?.cuenta?.alimentacion?.subtotal || 0).toFixed(2)"></span>
                                </div>
                                <div x-show="detalleCuenta?.cuenta?.descuento_seguro > 0" class="flex justify-between text-green-600">
                                    <span>Descuento seguro:</span>
                                    <span x-text="'-$' + detalleCuenta?.cuenta?.descuento_seguro.toFixed(2)"></span>
                                </div>
                                <div class="border-t pt-2 mt-2">
                                    <div class="flex justify-between font-bold text-lg">
                                        <span>TOTAL:</span>
                                        <span x-text="'$' + (detalleCuenta?.cuenta?.total || 0).toFixed(2)"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detalle de Items -->
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-900 mb-3">Detalle de Consumos</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left">Concepto</th>
                                        <th class="px-3 py-2 text-left">Fecha</th>
                                        <th class="px-3 py-2 text-right">Cantidad</th>
                                        <th class="px-3 py-2 text-right">Precio Unit.</th>
                                        <th class="px-3 py-2 text-right">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <template x-for="item in [...(detalleCuenta?.cuenta?.detalle_medicamentos || []), ...(detalleCuenta?.cuenta?.detalle_insumos || [])]" :key="item.nombre + item.fecha">
                                        <tr>
                                            <td class="px-3 py-2" x-text="item.nombre"></td>
                                            <td class="px-3 py-2" x-text="item.fecha"></td>
                                            <td class="px-3 py-2 text-right" x-text="item.cantidad"></td>
                                            <td class="px-3 py-2 text-right" x-text="'$' + item.precio_unitario.toFixed(2)"></td>
                                            <td class="px-3 py-2 text-right" x-text="'$' + item.total.toFixed(2)"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Formulario de Cobro -->
                    <div x-show="selectedPaciente?.listo_para_cobro" class="border-t pt-4">
                        <h4 class="font-medium text-gray-900 mb-3">Procesar Cobro</h4>
                        <form @submit.prevent="procesarCobro()">
                            <div class="grid grid-cols-4 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Efectivo</label>
                                    <input type="number" step="0.01" x-model="cobroForm.monto_efectivo" class="mt-1 block w-full rounded-lg border-gray-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tarjeta</label>
                                    <input type="number" step="0.01" x-model="cobroForm.monto_tarjeta" class="mt-1 block w-full rounded-lg border-gray-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Transferencia</label>
                                    <input type="number" step="0.01" x-model="cobroForm.monto_transferencia" class="mt-1 block w-full rounded-lg border-gray-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Depósito</label>
                                    <input type="number" step="0.01" x-model="cobroForm.monto_deposito" class="mt-1 block w-full rounded-lg border-gray-300">
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <button type="button" @click="registrarDeposito()" class="bg-yellow-500 text-white px-6 py-2 rounded-lg hover:bg-yellow-600">Registrar Depósito</button>
                                <button type="submit" class="flex-1 bg-green-600 text-white py-2 rounded-lg hover:bg-green-700">Procesar Cobro Total</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function utiCaja() {
            return {
                pacientes: [],
                filtroEstado: 'todos',
                showDetalleModal: false,
                selectedPaciente: null,
                detalleCuenta: null,
                cobroForm: { monto_efectivo: '', monto_tarjeta: '', monto_transferencia: '', monto_deposito: '', es_cobro_total: true },

                init() {
                    this.loadPacientes();
                },

                async loadPacientes() {
                    try {
                        const response = await fetch(`/api/uti-caja/pacientes?estado=${this.filtroEstado}`);
                        const data = await response.json();
                        if (data.success) this.pacientes = data.pacientes;
                    } catch (error) { console.error('Error:', error); }
                },

                async verDetalleCuenta(paciente) {
                    this.selectedPaciente = paciente;
                    try {
                        const response = await fetch(`/api/uti-caja/detalle-cuenta/${paciente.id}`);
                        const data = await response.json();
                        if (data.success) {
                            this.detalleCuenta = data;
                            this.showDetalleModal = true;
                        }
                    } catch (error) { console.error('Error:', error); }
                },

                async procesarCobro() {
                    try {
                        const response = await fetch(`/api/uti-caja/procesar-cobro/${this.selectedPaciente.id}`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
                            body: JSON.stringify(this.cobroForm)
                        });
                        const data = await response.json();
                        if (data.success) {
                            alert('Cobro procesado correctamente');
                            this.showDetalleModal = false;
                            this.loadPacientes();
                        } else {
                            alert(data.message);
                        }
                    } catch (error) { console.error('Error:', error); }
                },

                async registrarDeposito() {
                    const monto = parseFloat(this.cobroForm.monto_efectivo) || parseFloat(this.cobroForm.monto_tarjeta) || 
                                  parseFloat(this.cobroForm.monto_transferencia) || parseFloat(this.cobroForm.monto_deposito);
                    if (!monto) {
                        alert('Ingrese un monto');
                        return;
                    }
                    try {
                        const response = await fetch(`/api/uti-caja/deposito/${this.selectedPaciente.id}`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content },
                            body: JSON.stringify({ monto: monto, metodo_pago: 'efectivo' })
                        });
                        const data = await response.json();
                        if (data.success) {
                            alert('Depósito registrado');
                            this.verDetalleCuenta(this.selectedPaciente);
                        }
                    } catch (error) { console.error('Error:', error); }
                }
            }
        }
    </script>
</body>
</html>
