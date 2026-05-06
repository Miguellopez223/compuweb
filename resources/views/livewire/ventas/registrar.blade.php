<div class="space-y-4">
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
        {{ session('error') }}
    </div>
    @endif

    {{-- Success state --}}
    @if($showSuccess)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-12 text-center">
        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <h3 class="text-xl font-bold text-slate-800 mb-2">Venta registrada!</h3>
        <p class="text-slate-500 mb-6">La venta #{{ $ventaId }} fue registrada correctamente y el stock fue actualizado.</p>
        <div class="flex justify-center gap-3">
            <a href="{{ route('ventas.recibo', $ventaId) }}" target="_blank"
               class="px-5 py-2.5 bg-slate-700 hover:bg-slate-800 text-white text-sm font-medium rounded-lg transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Imprimir Recibo
            </a>
            <a href="{{ route('ventas') }}"
               class="px-5 py-2.5 border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 transition">
                Ver historial
            </a>
            <button wire:click="$set('showSuccess', false)"
                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                Nueva venta
            </button>
        </div>
    </div>
    @else

    <div>
        <h2 class="text-xl font-bold text-slate-800">Registrar Venta</h2>
        <p class="text-sm text-slate-500">Selecciona productos y completa los datos del cliente</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Product catalog --}}
        <div class="lg:col-span-2 space-y-3">
            {{-- Search --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-3 flex gap-3">
                <input wire:model.live.debounce.300ms="busqueda" type="text"
                       placeholder="Buscar producto por nombre o SKU..."
                       class="flex-1 px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <select wire:model.live="filtroCategoria"
                        class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Todas</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Product grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                @forelse($productos as $p)
                <div class="bg-white rounded-xl border border-slate-200 hover:border-indigo-300 hover:shadow-md transition cursor-pointer overflow-hidden"
                     wire:click="agregarProducto({{ $p->id }})">
                    @if($p->imagen)
                        <img src="{{ Storage::url($p->imagen) }}" alt="{{ $p->nombre }}"
                             class="w-full h-32 object-cover">
                    @else
                        <div class="w-full h-32 bg-slate-100 flex items-center justify-center">
                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    @endif
                    <div class="p-3">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <p class="font-semibold text-slate-800 text-sm leading-tight">{{ $p->nombre }}</p>
                            <span class="text-xs text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded flex-shrink-0">{{ $p->categoria->nombre ?? '' }}</span>
                        </div>
                        @if($p->sku)
                            <p class="text-xs text-slate-400 font-mono mb-1">{{ $p->sku }}</p>
                        @endif
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-base font-bold text-indigo-600">Bs. {{ number_format($p->precio, 2) }}</span>
                            <span class="text-xs text-slate-500">{{ $p->stock }} uds</span>
                        </div>
                        <button class="mt-2 w-full py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 text-xs font-semibold rounded-lg transition">
                            + Agregar
                        </button>
                    </div>
                </div>
                @empty
                <div class="col-span-full bg-white rounded-xl border border-slate-200 py-12 text-center text-slate-400">
                    <p class="text-2xl mb-2">🔍</p>
                    <p class="font-medium">No se encontraron productos disponibles</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Cart --}}
        <div class="space-y-3">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200">
                <div class="px-4 py-3.5 border-b border-slate-100">
                    <h3 class="font-semibold text-slate-800 text-sm flex items-center gap-2">
                        Carrito
                        @if(count($carrito) > 0)
                            <span class="bg-indigo-600 text-white text-xs px-2 py-0.5 rounded-full">{{ count($carrito) }}</span>
                        @endif
                    </h3>
                </div>
                <div class="p-3 space-y-2 max-h-80 overflow-y-auto">
                    @forelse($carrito as $i => $item)
                    <div class="flex items-center gap-2 p-2 bg-slate-50 rounded-lg">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-slate-800 truncate">{{ $item['nombre'] }}</p>
                            <p class="text-xs text-indigo-600 font-medium">Bs. {{ number_format($item['precio'], 2) }}</p>
                        </div>
                        <div class="flex items-center gap-1 flex-shrink-0">
                            <button wire:click="cambiarCantidad({{ $i }}, {{ $item['cantidad'] - 1 }})"
                                    class="w-6 h-6 flex items-center justify-center rounded-full border border-slate-300 text-slate-600 hover:bg-slate-200 text-sm font-bold transition">-</button>
                            <span class="w-7 text-center text-xs font-bold text-slate-800">{{ $item['cantidad'] }}</span>
                            <button wire:click="cambiarCantidad({{ $i }}, {{ $item['cantidad'] + 1 }})"
                                    class="w-6 h-6 flex items-center justify-center rounded-full border border-slate-300 text-slate-600 hover:bg-slate-200 text-sm font-bold transition">+</button>
                        </div>
                        <div class="w-16 text-right flex-shrink-0">
                            <p class="text-xs font-bold text-slate-800">Bs. {{ number_format($item['subtotal'], 2) }}</p>
                        </div>
                        <button wire:click="quitarItem({{ $i }})"
                                class="text-red-400 hover:text-red-600 transition flex-shrink-0">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    @empty
                    <div class="py-8 text-center text-slate-400">
                        <p class="text-2xl mb-1">🛒</p>
                        <p class="text-xs">Selecciona productos del catalogo</p>
                    </div>
                    @endforelse
                </div>
                @if(count($carrito) > 0)
                <div class="px-4 py-3 border-t border-slate-100 bg-slate-50 rounded-b-xl">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-semibold text-slate-700">Total</span>
                        <span class="text-xl font-bold text-slate-900">Bs. {{ number_format($total, 2) }}</span>
                    </div>
                </div>
                @endif
            </div>

            {{-- Proceed to checkout button --}}
            <button wire:click="abrirCheckout"
                    @if(empty($carrito)) disabled @endif
                    class="w-full py-3 rounded-xl font-semibold text-sm transition
                           {{ !empty($carrito)
                               ? 'bg-emerald-600 hover:bg-emerald-700 text-white'
                               : 'bg-slate-200 text-slate-400 cursor-not-allowed' }}">
                Continuar a Facturar - Bs. {{ number_format($total, 2) }}
            </button>
        </div>
    </div>
    @endif

    {{-- Checkout Modal --}}
    @if($showCheckout)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background-color: rgba(0,0,0,0.5);">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" @click.outside="$wire.cerrarCheckout()">
            {{-- Modal Header --}}
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between sticky top-0 bg-white rounded-t-2xl z-10">
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Finalizar Venta</h3>
                    <p class="text-sm text-slate-500">Ingresa los datos del cliente para facturar</p>
                </div>
                <button wire:click="cerrarCheckout" class="text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 space-y-5">
                {{-- Resumen del carrito --}}
                <div class="bg-slate-50 rounded-xl p-4">
                    <h4 class="text-sm font-semibold text-slate-700 mb-3">Resumen de Productos</h4>
                    <div class="space-y-2 max-h-40 overflow-y-auto">
                        @foreach($carrito as $item)
                        <div class="flex justify-between items-center text-sm">
                            <div class="flex-1 min-w-0">
                                <span class="text-slate-800 truncate block">{{ $item['nombre'] }}</span>
                            </div>
                            <span class="text-slate-500 mx-3">x{{ $item['cantidad'] }}</span>
                            <span class="font-semibold text-slate-800 w-24 text-right">Bs. {{ number_format($item['subtotal'], 2) }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="border-t border-slate-200 mt-3 pt-3 flex justify-between items-center">
                        <span class="font-bold text-slate-800">TOTAL</span>
                        <span class="text-2xl font-bold text-indigo-600">Bs. {{ number_format($total, 2) }}</span>
                    </div>
                </div>

                {{-- Datos del cliente --}}
                <div>
                    <h4 class="text-sm font-semibold text-slate-700 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Datos del Cliente
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-medium text-slate-600 mb-1">Nombre del cliente *</label>
                            <input wire:model="cliente_nombre" type="text" placeholder="Nombre completo"
                                   class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                                          {{ $errors->has('cliente_nombre') ? 'border-red-400' : 'border-slate-300' }}">
                            @error('cliente_nombre') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">NIT / CI</label>
                            <input wire:model="cliente_nit" type="text" placeholder="Ej: 12345678"
                                   class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Telefono</label>
                            <input wire:model="cliente_telefono" type="text" placeholder="+591 7..."
                                   class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Email</label>
                            <input wire:model="cliente_email" type="email" placeholder="correo@ejemplo.com"
                                   class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                                          {{ $errors->has('cliente_email') ? 'border-red-400' : 'border-slate-300' }}">
                            @error('cliente_email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Direccion de entrega</label>
                            <input wire:model="cliente_direccion" type="text" placeholder="Calle, zona..."
                                   class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                {{-- Metodo de pago --}}
                <div>
                    <h4 class="text-sm font-semibold text-slate-700 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        Metodo de Pago
                    </h4>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach(['efectivo' => 'Efectivo', 'qr' => 'QR', 'transferencia' => 'Transferencia'] as $val => $label)
                        <button type="button" wire:click="$set('metodo_pago', '{{ $val }}')"
                                class="py-3 text-sm font-medium rounded-xl border-2 transition flex flex-col items-center gap-1
                                       {{ $metodo_pago === $val
                                           ? 'bg-indigo-50 border-indigo-600 text-indigo-700'
                                           : 'border-slate-200 text-slate-600 hover:border-indigo-300' }}">
                            @if($val === 'efectivo')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            @elseif($val === 'qr')
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            @else
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
                            @endif
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="px-6 py-4 border-t border-slate-200 flex items-center justify-between sticky bottom-0 bg-white rounded-b-2xl">
                <button wire:click="cerrarCheckout"
                        class="px-5 py-2.5 border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 transition">
                    Volver al carrito
                </button>
                <button wire:click="confirmarVenta"
                        class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition flex items-center gap-2">
                    <span wire:loading.remove wire:target="confirmarVenta">
                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Confirmar Venta - Bs. {{ number_format($total, 2) }}
                    </span>
                    <span wire:loading wire:target="confirmarVenta">Procesando...</span>
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
