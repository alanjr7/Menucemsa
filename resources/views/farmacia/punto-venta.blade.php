@extends('layouts.app')

@section('content')
<div class="flex h-[calc(100vh-64px)] bg-[#f8fafc] overflow-hidden font-sans" x-data="posSystem()">

        <div class="flex-1 flex flex-col min-w-0">
            <div class="p-8 pb-4">
                <h1 class="text-2xl font-bold text-gray-800 mb-5">Punto de Venta</h1>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text"
                        x-model="searchQuery"
                        class="block w-full pl-12 pr-4 py-3 border border-blue-400 rounded-xl bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm placeholder-gray-400 text-sm"
                        placeholder="Buscar por nombre o código de barras...">
                </div>
            </div>

            <div class="flex-1 overflow-y-auto px-8 pb-8 custom-scrollbar">
                @if($productos->isEmpty())
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No hay productos disponibles</h3>
                        <p class="text-gray-500">No se encontraron medicamentos en el inventario.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        <template x-for="producto in filteredProducts" :key="producto.id">
                            <div @click="addToCart(producto.nombre, producto.precio, producto.id)"
                                 class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:border-blue-400 hover:shadow-md transition-all cursor-pointer group flex flex-col justify-between">
                                <div>
                                    <div class="flex justify-between items-start mb-1">
                                        <h3 class="text-[17px] font-bold text-gray-800" x-text="producto.nombre"></h3>
                                        <template x-if="producto.requerimiento === 'Receta'">
                                            <span class="bg-red-50 text-red-500 text-[10px] font-bold px-2 py-0.5 rounded border border-red-100">Receta</span>
                                        </template>
                                    </div>
                                    <p class="text-[12px] text-gray-400 font-medium mb-1" x-text="producto.categoria"></p>
                                    <div class="text-[11px] text-gray-400 space-y-0.5 mb-4">
                                        <p x-text="'Lote: ' + producto.lote"></p>
                                        <p class="flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2"/></svg>
                                            <span x-text="'Vence: ' + producto.fecha_vencimiento"></span>
                                        </p>
                                    </div>
                                </div>
                                <div class="flex justify-between items-end">
                                    <span class="text-xl font-bold text-blue-600" x-text="'$' + parseFloat(producto.precio).toFixed(2)"></span>
                                    <span class="text-[12px] text-gray-400 font-medium" x-text="'Stock: ' + producto.stock"></span>
                                </div>
                            </div>
                        </template>
                        <div x-show="filteredProducts.length === 0 && searchQuery" class="col-span-full text-center py-12">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No se encontraron productos</h3>
                            <p class="text-gray-500">No hay productos que coincidan con "<span x-text="searchQuery"></span>"</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="w-[400px] bg-white border-l border-gray-200 flex flex-col shadow-xl z-20">
            <div class="p-6 border-b border-gray-50 flex items-center gap-2">
                <svg class="w-6 h-6 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <h2 class="text-lg font-bold text-gray-800">Carrito de Venta</h2>
            </div>

            <div class="p-5">
                <label class="block text-[12px] text-gray-500 font-medium mb-2">Cliente (Opcional)</label>
                <select x-model="selectedCliente" class="w-full border-gray-200 rounded-lg py-2.5 text-sm shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Cliente General</option>
                    @if($clientes->count() > 0)
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nombre }} - {{ $cliente->telefono ?: 'Sin teléfono' }}</option>
                        @endforeach
                    @else
                        <option value="" disabled>No hay clientes registrados</option>
                    @endif
                </select>
                @if($clientes->count() === 0)
                    <p class="text-xs text-gray-400 mt-1">No hay clientes guardados. <a href="{{ route('farmacia.clientes') }}" class="text-blue-500 hover:underline">Ir a Clientes</a> para agregar.</p>
                @endif
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
                        <button @click="metodoPago = 'Efectivo'" :class="metodoPago === 'Efectivo' ? 'border-2 border-blue-500 text-blue-600' : 'border border-gray-200 text-gray-700'" class="py-2.5 text-xs font-bold rounded-lg bg-white">Efectivo</button>
                        <button @click="metodoPago = 'Tarjeta'" :class="metodoPago === 'Tarjeta' ? 'border-2 border-blue-500 text-blue-600' : 'border border-gray-200 text-gray-700'" class="py-2.5 text-xs font-bold rounded-lg bg-white">Tarjeta</button>
                        <button @click="metodoPago = 'Transferencia'" :class="metodoPago === 'Transferencia' ? 'border-2 border-blue-500 text-blue-600' : 'border border-gray-200 text-gray-700'" class="py-2.5 text-xs font-bold rounded-lg bg-white">Transferencia</button>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" x-model="requiereReceta" class="w-4 h-4 rounded text-gray-800 border-gray-300 focus:ring-gray-800">
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
                    <button @click="procesarVenta()" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-blue-50 transition-all text-[15px]">
                        Procesar Venta
                    </button>
                    <button @click="imprimirTicket()" x-show="ultimaVenta && !mostrarImprimir" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-green-50 transition-all text-[15px]">
                        🖨️ Imprimir Ticket
                    </button>
                    <button @click="cart = []; mostrarImprimir = false; ultimaVenta = null;" class="w-full text-gray-500 hover:text-red-500 font-medium py-2 text-[14px] transition-colors">
                        Limpiar Carrito
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function posSystem() {
            return {
                productos: @json($productos),
                searchQuery: '',
                selectedCliente: '',
                metodoPago: 'Tarjeta',
                requiereReceta: false,
                cart: [],
                ultimaVenta: null, // Store last sale data
                mostrarImprimir: false, // Controla la visibilidad del botón imprimir
                init() {
                    // Initialize the component
                },
                get filteredProducts() {
                    if (!this.searchQuery) {
                        return this.productos;
                    }
                    
                    const query = this.searchQuery.toLowerCase();
                    return this.productos.filter(producto => 
                        producto.nombre.toLowerCase().includes(query) ||
                        producto.codigo_barras.toLowerCase().includes(query) ||
                        producto.categoria.toLowerCase().includes(query)
                    );
                },
                get total() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
                },
                addToCart(name, price, id) {
                    const existing = this.cart.find(i => i.id === id);
                    if (existing) {
                        existing.qty++;
                    } else {
                        this.cart.push({ name, price, qty: 1, id });
                    }
                },
                updateQty(index, amount) {
                    this.cart[index].qty += amount;
                    if (this.cart[index].qty <= 0) this.removeFromCart(index);
                },
                removeFromCart(index) {
                    this.cart.splice(index, 1);
                },
                async procesarVenta() {
                    if (this.cart.length === 0) {
                        alert('El carrito está vacío');
                        return;
                    }

                    try {
                        const response = await fetch('{{ route("farmacia.pos.procesar") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                items: this.cart.map(item => ({
                                    id: item.id,
                                    cantidad: item.qty,
                                    precio: item.price
                                })),
                                cliente_id: this.selectedCliente || null,
                                metodo_pago: this.metodoPago,
                                requiere_receta: this.requiereReceta
                            })
                        });

                        const result = await response.json();

                        if (result.success) {
                            // Store last sale data for ticket printing
                            this.ultimaVenta = {
                                codigo: result.codigo_venta,
                                total: result.total,
                                items: this.cart,
                                cliente: this.selectedCliente ? this.getClientName(this.selectedCliente) : 'Cliente General',
                                metodo_pago: this.metodoPago,
                                fecha: new Date().toLocaleString(),
                                requiere_receta: this.requiereReceta
                            };
                            
                            // Mostrar el botón de imprimir para esta venta
                            this.mostrarImprimir = false;
                            
                            alert(`Venta procesada exitosamente!\nCódigo: ${result.codigo_venta}\nTotal: $${result.total}`);
                            this.cart = [];
                            this.selectedCliente = '';
                            this.requiereReceta = false;
                        } else {
                            alert('Error: ' + result.message);
                        }
                    } catch (error) {
                        alert('Error al procesar la venta: ' + error.message);
                    }
                },
                getClientName(clienteId) {
                    // Find client name from the clientes array (if available)
                    const cliente = this.productos ? null : null; // Would need access to clients data
                    return clienteId ? `Cliente ${clienteId}` : 'Cliente General';
                },
                imprimirTicket() {
                    if (!this.ultimaVenta) {
                        alert('No hay venta registrada para imprimir');
                        return;
                    }

                    // Ocultar el botón de imprimir inmediatamente
                    this.mostrarImprimir = true;
                    
                    this.generarTicketHTML(this.ultimaVenta);
                },
                generarTicketHTML(venta) {
                    let itemsHTML = '';
                    venta.items.forEach(item => {
                        itemsHTML += `<div class="item">
                            <span>${item.qty} x ${item.name}</span>
                            <span>$${(item.price * item.qty).toFixed(2)}</span>
                        </div>`;
                    });

                    const recetaHTML = venta.requiere_receta ? '<div style="color: red;"><strong>⚠️ Requiere Receta</strong></div>' : '';
                    
                    const ticketHTML = `
                        <html>
                            <head>
                                <title>Ticket de Venta</title>
                                <style>
                                    @media print {
                                        body { margin: 0; padding: 0; font-family: 'Courier New', monospace; }
                                        .ticket { 
                                            width: 80mm; 
                                            max-width: 80mm;
                                            margin: 0 auto;
                                            padding: 10px;
                                            font-size: 12px;
                                            border: 1px solid #000;
                                            background: white;
                                        }
                                        .header { text-align: center; border-bottom: 2px dashed #000; padding-bottom: 5px; margin-bottom: 10px; }
                                        .header h1 { font-size: 16px; margin: 0; }
                                        .header p { font-size: 10px; margin: 2px 0; }
                                        .content { padding: 10px 0; }
                                        .item { display: flex; justify-content: space-between; margin: 3px 0; }
                                        .total { border-top: 2px dashed #000; padding-top: 5px; margin-top: 10px; font-weight: bold; }
                                        .footer { text-align: center; margin-top: 10px; font-size: 10px; }
                                    }
                                </style>
                            </head>
                            <body>
                                <div class="ticket">
                                    <div class="header">
                                        <h1> FARMACIA - cemsa</h1>
                                        <p>Ticket de Venta</p>
                                        <p>${venta.fecha}</p>
                                    </div>
                                    <div class="content">
                                        <div><strong>Código:</strong> ${venta.codigo}</div>
                                        <div><strong>Cliente:</strong> ${venta.cliente}</div>
                                        <div><strong>Método:</strong> ${venta.metodo_pago}</div>
                                        ${recetaHTML}
                                        <br>
                                        <strong>Productos:</strong><br>
                                        ${itemsHTML}
                                        <div class="total">
                                            <div><strong>TOTAL:</strong></div>
                                            <div>$${venta.total.toFixed(2)}</div>
                                        </div>
                                    </div>
                                    <div class="footer">
                                        <p>¡Gracias por su compra!</p>
                                        <p>Vuelva pronto</p>
                                    </div>
                                </div>
                                <script>
                                    window.onload = function() {
                                        window.print();
                                        window.close();
                                    }
                                <\/script>
                            </body>
                        </html>
                    `;

                    const printWindow = window.open('', '_blank');
                    printWindow.document.write(ticketHTML);
                    printWindow.document.close();
                }
            }
        }
    </script>
@endsection
