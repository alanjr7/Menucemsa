@extends('layouts.app')

@section('content')
<div class="h-[calc(100vh-64px)] overflow-hidden font-sans"
     style="display:grid; grid-template-columns: 1fr 1fr 1fr;"
     x-data="posSystem()">

    {{-- ══════════════════════════════════════════════════════
         COLUMNA 1: Catálogo de productos
    ══════════════════════════════════════════════════════ --}}
    <div class="flex flex-col overflow-hidden bg-[#f8fafc] border-r border-gray-200">

        {{-- Header + buscador --}}
        <div class="p-6 pb-3 border-b border-gray-100">
            <h1 class="text-base font-bold text-gray-800 mb-3">Productos</h1>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text"
                    x-model="searchQuery"
                    class="block w-full pl-9 pr-3 py-2.5 border border-blue-300 rounded-xl bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm placeholder-gray-400 text-[13px]"
                    placeholder="Buscar por nombre o código...">
            </div>
        </div>

        {{-- Grid de productos --}}
        <div class="flex-1 overflow-y-auto p-4 custom-scrollbar">
            @if($productos->isEmpty())
                <div class="text-center py-12">
                    <svg class="w-14 h-14 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <h3 class="text-sm font-medium text-gray-900 mb-1">No hay productos disponibles</h3>
                    <p class="text-xs text-gray-400">No se encontraron medicamentos en el inventario.</p>
                </div>
            @else
                <div class="grid grid-cols-2 gap-3">
                    <template x-for="producto in filteredProducts" :key="producto.id">
                        <div @click="addToCart(producto.nombre, producto.precio, producto.id, producto.stock, producto.requiere_receta)"
                             class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:border-blue-400 hover:shadow-md transition-all cursor-pointer flex flex-col justify-between"
                             :class="{ 'opacity-50 cursor-not-allowed hover:border-gray-100 hover:shadow-none': producto.stock <= 0 }">
                            <div>
                                <div class="flex justify-between items-start mb-1">
                                    <h3 class="text-[13px] font-bold text-gray-800 leading-tight pr-1" x-text="producto.nombre"></h3>
                                    <template x-if="producto.requerimiento === 'Receta'">
                                        <span class="bg-red-50 text-red-500 text-[9px] font-bold px-1.5 py-0.5 rounded border border-red-100 shrink-0">Receta</span>
                                    </template>
                                </div>
                                <p class="text-[11px] text-gray-400 font-medium mb-1" x-text="producto.categoria"></p>
                                <div class="text-[10px] text-gray-400 space-y-0.5 mb-3">
                                    <p x-text="'Lote: ' + producto.lote"></p>
                                    <p class="flex items-center gap-1">
                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2"/>
                                        </svg>
                                        <span x-text="'Vence: ' + producto.vencimiento"></span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex justify-between items-end">
                                <span class="text-base font-bold text-blue-600" x-text="'Bs' + parseFloat(producto.precio).toFixed(2)"></span>
                                <span class="text-[11px] font-medium"
                                      :class="producto.stock > 0 ? 'text-green-600' : 'text-red-500'"
                                      x-text="'Stock: ' + producto.stock"></span>
                            </div>
                        </div>
                    </template>

                    <div x-show="filteredProducts.length === 0 && searchQuery" class="col-span-full text-center py-10">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <h3 class="text-sm font-medium text-gray-900 mb-1">Sin resultados</h3>
                        <p class="text-xs text-gray-400">Ningún producto coincide con "<span x-text="searchQuery"></span>"</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         COLUMNA 2: Items del carrito
    ══════════════════════════════════════════════════════ --}}
    <div class="flex flex-col overflow-hidden bg-white border-r border-gray-200">

        {{-- Header --}}
        <div class="p-5 pb-4 border-b border-gray-100 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <h2 class="text-base font-bold text-gray-800">Carrito</h2>
            <span class="ml-auto bg-blue-100 text-blue-600 text-[11px] font-bold px-2 py-0.5 rounded-full" x-text="cart.length + ' items'"></span>
        </div>

        {{-- Selector de cliente --}}
        <div class="px-5 py-3 border-b border-gray-50">
            <label class="block text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-1.5">Cliente</label>
            <select x-model="selectedCliente"
                    class="w-full border border-gray-200 rounded-lg py-2 px-3 text-[13px] text-gray-700 shadow-sm focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
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
                <p class="text-[10px] text-gray-400 mt-1">
                    <a href="{{ route('farmacia.clientes') }}" class="text-blue-500 hover:underline">Agregar clientes →</a>
                </p>
            @endif
        </div>

        {{-- Lista de items --}}
        <div class="flex-1 overflow-y-auto px-4 py-3 space-y-3 custom-scrollbar">
            <template x-if="cart.length === 0">
                <div class="text-center py-16">
                    <svg class="w-12 h-12 mx-auto text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p class="text-[12px] text-gray-400">Selecciona productos del catálogo</p>
                </div>
            </template>

            <template x-for="(item, index) in cart" :key="index">
                <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-100 shadow-sm">
                    <div class="flex justify-between items-start mb-2">
                        <div class="pr-2 flex-1 min-w-0">
                            <p class="font-bold text-gray-800 text-[13px] leading-tight truncate" x-text="item.name"></p>
                            <p class="text-[11px] text-gray-400 mt-0.5" x-text="'Bs' + item.price.toFixed(2) + ' c/u'"></p>
                        </div>
                        <button @click="removeFromCart(index)"
                                class="text-red-400 hover:text-red-600 p-1 transition-colors rounded-lg hover:bg-red-50 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden bg-white shadow-sm">
                            <button @click="updateQty(index, -1)" class="px-2.5 py-1 text-gray-500 hover:bg-gray-100 font-bold transition-colors text-sm">−</button>
                            <span class="text-[13px] font-bold w-7 text-center text-gray-700 bg-gray-50 py-1" x-text="item.qty"></span>
                            <button @click="updateQty(index, 1)" class="px-2.5 py-1 text-gray-500 hover:bg-gray-100 font-bold transition-colors text-sm">+</button>
                        </div>
                        <span class="font-bold text-gray-900 text-[15px]" x-text="'Bs' + (item.price * item.qty).toFixed(2)"></span>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         COLUMNA 3: Método de pago, total y acciones
    ══════════════════════════════════════════════════════ --}}
    <div class="flex flex-col overflow-hidden bg-white">

        {{-- Header --}}
        <div class="p-5 pb-4 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-800">Pago</h2>
        </div>

        {{-- Contenido scrollable --}}
        <div class="flex-1 overflow-y-auto px-5 py-4 space-y-5 custom-scrollbar">

            {{-- Método de pago --}}
            <div>
                <label class="block text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-2">Método de Pago</label>
                <div class="grid grid-cols-3 gap-2">
                    <button @click="metodoPago = 'efectivo'"
                            :class="metodoPago === 'efectivo'
                                ? 'border-2 border-blue-500 text-blue-600 bg-blue-50'
                                : 'border border-gray-200 text-gray-600 bg-white hover:bg-gray-50'"
                            class="py-2.5 text-[11px] font-bold rounded-xl transition-all">
                        <div class="text-center">
                            <svg class="w-4 h-4 mx-auto mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Efectivo
                        </div>
                    </button>
                    <button @click="metodoPago = 'tarjeta'"
                            :class="metodoPago === 'tarjeta'
                                ? 'border-2 border-blue-500 text-blue-600 bg-blue-50'
                                : 'border border-gray-200 text-gray-600 bg-white hover:bg-gray-50'"
                            class="py-2.5 text-[11px] font-bold rounded-xl transition-all">
                        <div class="text-center">
                            <svg class="w-4 h-4 mx-auto mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            Tarjeta
                        </div>
                    </button>
                    <button @click="metodoPago = 'transferencia'"
                            :class="metodoPago === 'transferencia'
                                ? 'border-2 border-blue-500 text-blue-600 bg-blue-50'
                                : 'border border-gray-200 text-gray-600 bg-white hover:bg-gray-50'"
                            class="py-2.5 text-[11px] font-bold rounded-xl transition-all">
                        <div class="text-center">
                            <svg class="w-4 h-4 mx-auto mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            Transfer.
                        </div>
                    </button>
                </div>
            </div>

            {{-- Receta --}}
            <div class="flex items-center gap-3 p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                <input type="checkbox"
                       x-model="requiereReceta"
                       class="w-4 h-4 rounded text-blue-600 border-gray-300 focus:ring-blue-500">
                <span class="text-[12px] text-gray-600 font-medium">Venta con receta médica</span>
            </div>

            {{-- Resumen / totales --}}
            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                <div class="space-y-2 mb-3">
                    <div class="flex justify-between">
                        <span class="text-[12px] text-gray-500">Subtotal</span>
                        <span class="text-[12px] text-gray-700" x-text="'Bs' + total.toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[12px] text-gray-500">Items</span>
                        <span class="text-[12px] text-gray-700" x-text="cart.reduce((s,i) => s + i.qty, 0) + ' unidades'"></span>
                    </div>
                </div>
                <div class="border-t border-dashed border-gray-200 pt-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-bold text-gray-700">TOTAL</span>
                        <span class="text-2xl font-extrabold text-gray-900" x-text="'Bs' + total.toFixed(2)"></span>
                    </div>
                </div>
            </div>

            {{-- Método seleccionado badge --}}
            <div class="flex items-center gap-2 text-[11px] text-gray-500">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Pago por: <span class="font-semibold text-gray-700 capitalize" x-text="metodoPago"></span>
            </div>
        </div>

        {{-- Botones fijos al fondo --}}
        <div class="p-5 border-t border-gray-100 space-y-2 bg-white">
            <button @click="procesarVenta()"
                    class="w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold py-4 rounded-2xl shadow-lg shadow-blue-100 transition-all text-[14px] flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Procesar Venta
            </button>
            <button @click="cart = []; mostrarImprimir = false; ultimaVenta = null;"
                    class="w-full text-gray-400 hover:text-red-500 font-medium py-2 text-[12px] transition-colors">
                Limpiar Carrito
            </button>
        </div>
    </div>

</div>

<script>
    function posSystem() {
        return {
            productos: @json($productos),
            clientes: @json($clientes),
            searchQuery: '',
            selectedCliente: '',
            metodoPago: 'tarjeta',
            requiereReceta: false,
            cart: [],
            ultimaVenta: null,
            mostrarImprimir: false,
            init() {},
            get filteredProducts() {
                if (!this.searchQuery) return this.productos;
                const query = this.searchQuery.toLowerCase();
                return this.productos.filter(p =>
                    p.nombre.toLowerCase().includes(query) ||
                    p.codigo_barras.toLowerCase().includes(query) ||
                    p.categoria.toLowerCase().includes(query)
                );
            },
            get total() {
                return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
            },
            addToCart(name, price, id, stock, requiereReceta) {
                if (stock <= 0) return;
                const existing = this.cart.find(i => i.id === id);
                if (existing) {
                    if (existing.qty >= stock) return;
                    existing.qty++;
                } else {
                    this.cart.push({ name, price: parseFloat(price) || 0, qty: 1, id, stock, requiereReceta });
                }
            },
            updateQty(index, amount) {
                const item = this.cart[index];
                const newQty = item.qty + amount;
                if (amount > 0 && newQty > item.stock) return;
                if (newQty <= 0) { this.removeFromCart(index); return; }
                item.qty = newQty;
            },
            removeFromCart(index) {
                this.cart.splice(index, 1);
            },
            async procesarVenta() {
                if (this.cart.length === 0) {
                    alert('El carrito está vacío');
                    return;
                }
                const productosConReceta = this.cart.filter(item => item.requiereReceta);
                if (productosConReceta.length > 0 && !this.requiereReceta) {
                    alert('⚠️ Hay productos que requieren receta médica. Marque la casilla antes de continuar.');
                    return;
                }
                try {
                    const response = await fetch('{{ route("farmacia.pos.procesar") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
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
                        this.ultimaVenta = {
                            codigo: result.codigo_venta,
                            total: result.total,
                            items: this.cart,
                            cliente: this.selectedCliente ? this.getClientName(this.selectedCliente) : 'Cliente General',
                            metodo_pago: this.metodoPago,
                            fecha: new Date().toLocaleString(),
                            requiere_receta: this.requiereReceta
                        };
                        this.generarTicketHTML(this.ultimaVenta);
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
                const cliente = this.clientes.find(c => c.id == clienteId);
                return cliente ? cliente.nombre : 'Cliente General';
            },
            generarTicketHTML(venta) {
                let itemsHTML = '';
                venta.items.forEach(item => {
                    itemsHTML += `<tr>
                        <td>${item.qty} x ${item.name}</td>
                        <td style="text-align:right">Bs${(item.price * item.qty).toFixed(2)}</td>
                    </tr>`;
                });

                const recetaHTML = venta.requiere_receta
                    ? '<p style="color:red;font-weight:bold;text-align:center">⚠️ Requiere Receta</p>'
                    : '';

                const html = `
                    <style>
                        body { font-family: 'Courier New', monospace; font-size: 12px; margin: 0; padding: 10px; width: 80mm; }
                        h2 { font-size: 14px; margin: 0 0 2px; }
                        hr { border: none; border-top: 1px dashed #000; margin: 6px 0; }
                        table { width: 100%; border-collapse: collapse; }
                        td { padding: 1px 0; vertical-align: top; }
                        .total td { font-weight: bold; font-size: 13px; border-top: 1px dashed #000; padding-top: 4px; }
                        .center { text-align: center; }
                    </style>
                    <div class="center">
                        <h2>FARMACIA CEMSA</h2>
                        <p style="margin:0;font-size:10px">Ticket de Venta</p>
                        <p style="margin:2px 0;font-size:10px">${venta.fecha}</p>
                    </div>
                    <hr>
                    <p style="margin:2px 0"><strong>Código:</strong> ${venta.codigo}</p>
                    <p style="margin:2px 0"><strong>Cliente:</strong> ${venta.cliente}</p>
                    <p style="margin:2px 0"><strong>Método:</strong> ${venta.metodo_pago}</p>
                    ${recetaHTML}
                    <hr>
                    <table>${itemsHTML}</table>
                    <table class="total">
                        <tr><td>TOTAL</td><td style="text-align:right">Bs${parseFloat(venta.total).toFixed(2)}</td></tr>
                    </table>
                    <hr>
                    <p class="center" style="font-size:10px;margin-top:6px">¡Gracias por su compra!</p>
                `;

                const win = window.open('', '_blank', 'width=420,height=600,toolbar=0,menubar=0,location=0');
                win.document.write(html);
                win.document.close();
                win.focus();
                setTimeout(() => win.print(), 300);
            }
        }
    }
</script>
@endsection