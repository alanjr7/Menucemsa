<x-app-layout>
    <div class="flex h-[calc(100vh-64px)] bg-[#f8fafc] overflow-hidden font-sans" x-data="posSystem()">

        <div class="flex-1 flex flex-col min-w-0">
            <div class="p-8 pb-4">
                <h1 class="text-2xl font-bold text-gray-800 mb-5">Punto de Venta</h1>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text"
                        class="block w-full pl-12 pr-4 py-3 border border-blue-400 rounded-xl bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm placeholder-gray-400 text-sm"
                        placeholder="Buscar por nombre o código de barras...">
                </div>
            </div>

            <div class="flex-1 overflow-y-auto px-8 pb-8 custom-scrollbar">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

                    <div @click="addToCart('Paracetamol 500mg', 5.50)"
                         class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:border-blue-400 hover:shadow-md transition-all cursor-pointer group flex flex-col justify-between">
                        <div>
                            <h3 class="text-[17px] font-bold text-gray-800 mb-1">Paracetamol 500mg</h3>
                            <p class="text-[12px] text-gray-400 font-medium mb-1">Medicamento</p>
                            <div class="text-[11px] text-gray-400 space-y-0.5 mb-4">
                                <p>Lote: PAR-2024-001</p>
                                <p class="flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2"/></svg>
                                    Vence: 14/8/2026
                                </p>
                            </div>
                        </div>
                        <div class="flex justify-between items-end">
                            <span class="text-xl font-bold text-blue-600">$5.50</span>
                            <span class="text-[12px] text-gray-400 font-medium">Stock: 150</span>
                        </div>
                    </div>

                    <div @click="addToCart('Ibuprofeno 400mg', 8.00)"
                         class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:border-blue-400 hover:shadow-md transition-all cursor-pointer flex flex-col justify-between">
                        <div>
                            <h3 class="text-[17px] font-bold text-gray-800 mb-1">Ibuprofeno 400mg</h3>
                            <p class="text-[12px] text-gray-400 font-medium mb-1">Medicamento</p>
                            <div class="text-[11px] text-gray-400 space-y-0.5 mb-4">
                                <p>Lote: IBU-2024-002</p>
                                <p class="flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2"/></svg>
                                    Vence: 19/12/2026
                                </p>
                            </div>
                        </div>
                        <div class="flex justify-between items-end">
                            <span class="text-xl font-bold text-blue-600">$8.00</span>
                            <span class="text-[12px] text-gray-400 font-medium">Stock: 120</span>
                        </div>
                    </div>

                    <div @click="addToCart('evo', 44.97)"
                         class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:border-blue-400 hover:shadow-md transition-all cursor-pointer flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-1">
                                <h3 class="text-[17px] font-bold text-gray-800">evo</h3>
                                <span class="bg-red-50 text-red-500 text-[10px] font-bold px-2 py-0.5 rounded border border-red-100">Receta</span>
                            </div>
                            <p class="text-[12px] text-gray-400 font-medium mb-1">Receta</p>
                            <div class="text-[11px] text-gray-400 space-y-0.5 mb-4">
                                <p>Lote: AMX-2024-003</p>
                                <p class="flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2"/></svg>
                                    Vence: 29/6/2026
                                </p>
                            </div>
                        </div>
                        <div class="flex justify-between items-end">
                            <span class="text-xl font-bold text-blue-600">$44.97</span>
                            <span class="text-[12px] text-gray-400 font-medium">Stock: 83</span>
                        </div>
                    </div>

                    <div @click="addToCart('Omeprazol 20mg', 12.50)"
                         class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:border-blue-400 hover:shadow-md transition-all cursor-pointer flex flex-col justify-between">
                        <div>
                            <h3 class="text-[17px] font-bold text-gray-800 mb-1">Omeprazol 20mg</h3>
                            <p class="text-[12px] text-gray-400 font-medium mb-10">Medicamento</p>
                        </div>
                        <div class="flex justify-between items-end">
                            <span class="text-xl font-bold text-blue-600">$12.50</span>
                            <span class="text-[12px] text-gray-400 font-medium">Stock: 90</span>
                        </div>
                    </div>

                    <div @click="addToCart('Loratadina 10mg', 15.00)"
                         class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:border-blue-400 hover:shadow-md transition-all cursor-pointer flex flex-col justify-between">
                        <div>
                            <h3 class="text-[17px] font-bold text-gray-800 mb-1">Loratadina 10mg</h3>
                            <p class="text-[12px] text-gray-400 font-medium mb-10">Medicamento</p>
                        </div>
                        <div class="flex justify-between items-end">
                            <span class="text-xl font-bold text-blue-600">$15.00</span>
                            <span class="text-[12px] text-gray-400 font-medium">Stock: 100</span>
                        </div>
                    </div>

                    <div @click="addToCart('Vitamina C 1000mg', 25.00)"
                         class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:border-blue-400 hover:shadow-md transition-all cursor-pointer flex flex-col justify-between">
                        <div>
                            <h3 class="text-[17px] font-bold text-gray-800 mb-1">Vitamina C 1000mg</h3>
                            <p class="text-[12px] text-gray-400 font-medium mb-10">Vitaminas</p>
                        </div>
                        <div class="flex justify-between items-end">
                            <span class="text-xl font-bold text-blue-600">$25.00</span>
                            <span class="text-[12px] text-gray-400 font-medium">Stock: 60</span>
                        </div>
                    </div>

                    <div @click="addToCart('Alcohol en Gel 250ml', 18.00)"
                         class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:border-blue-400 hover:shadow-md transition-all cursor-pointer flex flex-col justify-between">
                        <div>
                            <h3 class="text-[17px] font-bold text-gray-800 mb-1">Alcohol en Gel 250ml</h3>
                            <p class="text-[12px] text-gray-400 font-medium mb-10">Cuidado Personal</p>
                        </div>
                        <div class="flex justify-between items-end">
                            <span class="text-xl font-bold text-blue-600">$18.00</span>
                            <span class="text-[12px] text-gray-400 font-medium">Stock: 200</span>
                        </div>
                    </div>

                    <div @click="addToCart('Vendas Elásticas', 12.00)"
                         class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:border-blue-400 hover:shadow-md transition-all cursor-pointer flex flex-col justify-between">
                        <div>
                            <h3 class="text-[17px] font-bold text-gray-800 mb-1">Vendas Elásticas</h3>
                            <p class="text-[12px] text-gray-400 font-medium mb-10">Primeros Auxilios</p>
                        </div>
                        <div class="flex justify-between items-end">
                            <span class="text-xl font-bold text-blue-600">$12.00</span>
                            <span class="text-[12px] text-gray-400 font-medium">Stock: 45</span>
                        </div>
                    </div>

                    <div @click="addToCart('Enalapril 10mg', 35.00)"
                         class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:border-blue-400 hover:shadow-md transition-all cursor-pointer flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-1">
                                <h3 class="text-[17px] font-bold text-gray-800">Enalapril 10mg</h3>
                                <span class="bg-red-50 text-red-500 text-[10px] font-bold px-2 py-0.5 rounded border border-red-100">Receta</span>
                            </div>
                            <p class="text-[12px] text-gray-400 font-medium mb-10">Receta</p>
                        </div>
                        <div class="flex justify-between items-end">
                            <span class="text-xl font-bold text-blue-600">$35.00</span>
                            <span class="text-[12px] text-gray-400 font-medium">Stock: 65</span>
                        </div>
                    </div>

                    <div @click="addToCart('Metformina 850mg', 28.00)"
                         class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:border-blue-400 hover:shadow-md transition-all cursor-pointer flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-1">
                                <h3 class="text-[17px] font-bold text-gray-800">Metformina 850mg</h3>
                                <span class="bg-red-50 text-red-500 text-[10px] font-bold px-2 py-0.5 rounded border border-red-100">Receta</span>
                            </div>
                            <p class="text-[12px] text-gray-400 font-medium mb-10">Receta</p>
                        </div>
                        <div class="flex justify-between items-end">
                            <span class="text-xl font-bold text-blue-600">$28.00</span>
                            <span class="text-[12px] text-gray-400 font-medium">Stock: 70</span>
                        </div>
                    </div>

                    <div @click="addToCart('Termómetro Digital', 85.00)"
                         class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:border-blue-400 hover:shadow-md transition-all cursor-pointer flex flex-col justify-between">
                        <div>
                            <h3 class="text-[17px] font-bold text-gray-800 mb-1">Termómetro Digital</h3>
                            <p class="text-[12px] text-gray-400 font-medium mb-10">Primeros Auxilios</p>
                        </div>
                        <div class="flex justify-between items-end">
                            <span class="text-xl font-bold text-blue-600">$85.00</span>
                            <span class="text-[12px] text-gray-400 font-medium">Stock: 25</span>
                        </div>
                    </div>

                    <div @click="addToCart('Complejo B', 32.00)"
                         class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:border-blue-400 hover:shadow-md transition-all cursor-pointer flex flex-col justify-between">
                        <div>
                            <h3 class="text-[17px] font-bold text-gray-800 mb-1">Complejo B</h3>
                            <p class="text-[12px] text-gray-400 font-medium mb-10">Vitaminas</p>
                        </div>
                        <div class="flex justify-between items-end">
                            <span class="text-xl font-bold text-blue-600">$32.00</span>
                            <span class="text-[12px] text-gray-400 font-medium">Stock: 55</span>
                        </div>
                    </div>

                    <div @click="addToCart('Suero Oral', 8.50)"
                         class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:border-blue-400 hover:shadow-md transition-all cursor-pointer flex flex-col justify-between">
                        <div>
                            <h3 class="text-[17px] font-bold text-gray-800 mb-1">Suero Oral</h3>
                            <p class="text-[12px] text-gray-400 font-medium mb-10">Medicamento</p>
                        </div>
                        <div class="flex justify-between items-end">
                            <span class="text-xl font-bold text-blue-600">$8.50</span>
                            <span class="text-[12px] text-gray-400 font-medium">Stock: 110</span>
                        </div>
                    </div>

                    <div @click="addToCart('Diclofenaco Gel', 42.00)"
                         class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:border-blue-400 hover:shadow-md transition-all cursor-pointer flex flex-col justify-between">
                        <div>
                            <h3 class="text-[17px] font-bold text-gray-800 mb-1">Diclofenaco Gel</h3>
                            <p class="text-[12px] text-gray-400 font-medium mb-10">Medicamento</p>
                        </div>
                        <div class="flex justify-between items-end">
                            <span class="text-xl font-bold text-blue-600">$42.00</span>
                            <span class="text-[12px] text-gray-400 font-medium">Stock: 40</span>
                        </div>
                    </div>

                    <div @click="addToCart('Mascarillas Quirúrgicas x50', 95.00)"
                         class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:border-blue-400 hover:shadow-md transition-all cursor-pointer flex flex-col justify-between">
                        <div>
                            <h3 class="text-[17px] font-bold text-gray-800 mb-1">Mascarillas Quirúrgicas x50</h3>
                            <p class="text-[12px] text-gray-400 font-medium mb-10">Cuidado Personal</p>
                        </div>
                        <div class="flex justify-between items-end">
                            <span class="text-xl font-bold text-blue-600">$95.00</span>
                            <span class="text-[12px] text-gray-400 font-medium">Stock: 15</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="w-[400px] bg-white border-l border-gray-200 flex flex-col shadow-xl z-20">
            <div class="p-6 border-b border-gray-50 flex items-center gap-2">
                <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <h2 class="text-lg font-bold text-gray-800">Carrito de Venta</h2>
            </div>

            <div class="p-5">
                <label class="block text-[12px] text-gray-500 font-medium mb-2">Cliente (Opcional)</label>
                <select class="w-full border-gray-200 rounded-lg py-2.5 text-sm shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option>Cliente General</option>
                </select>
            </div>

            <div class="flex-1 overflow-y-auto px-5 space-y-4">
                <template x-for="(item, index) in cart" :key="index">
                    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm relative mb-3 group">
                        <div class="flex justify-between items-start mb-1">
                            <div class="pr-2">
                                <p class="font-bold text-gray-800 text-[14px] leading-tight" x-text="item.name"></p>
                                <p class="text-[11px] text-gray-400 mt-0.5" x-text="'$' + item.price.toFixed(2) + ' c/u'"></p>
                            </div>

                            <button @click="removeFromCart(index)"
                                    class="text-red-400 hover:text-red-600 p-1 transition-colors rounded-md hover:bg-red-50">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>

                        <div class="flex justify-between items-center mt-3">
                            <div class="flex items-center gap-0 border border-gray-200 rounded-lg overflow-hidden bg-white">
                                <button @click="updateQty(index, -1)" class="px-2.5 py-1 text-gray-500 hover:bg-gray-100 font-bold transition-colors">−</button>
                                <span class="text-sm font-bold w-7 text-center text-gray-700 bg-gray-50/50 py-1" x-text="item.qty"></span>
                                <button @click="updateQty(index, 1)" class="px-2.5 py-1 text-gray-500 hover:bg-gray-100 font-bold transition-colors">+</button>
                            </div>
                            <span class="font-bold text-gray-900 text-[16px]" x-text="'$' + (item.price * item.qty).toFixed(2)"></span>
                        </div>
                    </div>
                </template>
            </div>

            <div class="p-6 border-t border-gray-100 space-y-4 bg-white">
                <div>
                    <label class="block text-[12px] text-gray-500 font-medium mb-3">Método de Pago</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button class="py-2.5 text-xs font-bold rounded-lg border border-gray-200 text-gray-700 bg-white">Efectivo</button>
                        <button class="py-2.5 text-xs font-bold rounded-lg border-2 border-blue-500 text-blue-600 bg-white">Tarjeta</button>
                        <button class="py-2.5 text-xs font-bold rounded-lg border border-gray-200 text-gray-700 bg-white">Transferencia</button>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" class="w-4 h-4 rounded text-gray-800 border-gray-300 focus:ring-gray-800">
                    <span class="text-[13px] text-gray-600">Venta con receta médica</span>
                </div>

                <div class="pt-4 border-t border-gray-50">
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm text-gray-500">Subtotal</span>
                        <span class="text-sm text-gray-600" x-text="'$' + total.toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-gray-800">Total</span>
                        <span class="text-2xl font-bold text-gray-900" x-text="'$' + total.toFixed(2)"></span>
                    </div>
                </div>

                <div class="space-y-2 pt-2">
                    <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-blue-50 transition-all text-[15px]">
                        Procesar Venta
                    </button>
                    <button @click="cart = []" class="w-full text-gray-500 hover:text-red-500 font-medium py-2 text-[14px] transition-colors">
                        Limpiar Carrito
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function posSystem() {
            return {
                cart: [
                    // Dejé estos productos por defecto en el carrito como ejemplo visual
                    { name: 'Vitamina C 1000mg', price: 25.00, qty: 1 },
                    { name: 'Ibuprofeno 400mg', price: 8.00, qty: 1 }
                ],
                get total() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
                },
                addToCart(name, price) {
                    const existing = this.cart.find(i => i.name === name);
                    if (existing) {
                        existing.qty++;
                    } else {
                        this.cart.push({ name, price, qty: 1 });
                    }
                },
                updateQty(index, amount) {
                    this.cart[index].qty += amount;
                    if (this.cart[index].qty <= 0) this.removeFromCart(index);
                },
                removeFromCart(index) {
                    this.cart.splice(index, 1);
                }
            }
        }
    </script>
</x-app-layout>
