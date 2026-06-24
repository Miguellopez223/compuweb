<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tiendaNombre ?? 'Catálogo' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-zinc-950 text-white antialiased min-h-screen" x-data>

{{-- Alpine.js Cart Store --}}
<script>
    const CW_TIENDA_NOMBRE = @json($tiendaNombre ?? 'la tienda');
    const CW_TIENDA_PHONE  = @json($tiendaPhone ?? '');
    const CW_VENDEDORES    = @json($vendedores ?? []);

    document.addEventListener('alpine:init', () => {
        Alpine.store('cart', {
            open: false,
            vendorPicker: false,
            items: JSON.parse(localStorage.getItem('cw_cart') || '[]'),

            add(product) {
                const found = this.items.find(i => i.id === product.id);
                if (found) { found.cantidad++; } else { this.items.push({...product, cantidad: 1}); }
                this.save();
                this.open = true;
                this.vendorPicker = false;
            },
            remove(id) { this.items = this.items.filter(i => i.id !== id); this.save(); },
            increment(id) { const i = this.items.find(i => i.id === id); if (i) { i.cantidad++; this.save(); } },
            decrement(id) {
                const i = this.items.find(i => i.id === id);
                if (i) { i.cantidad > 1 ? i.cantidad-- : this.remove(id); this.save(); }
            },
            clear() { this.items = []; this.save(); this.vendorPicker = false; },
            save() { localStorage.setItem('cw_cart', JSON.stringify(this.items)); },
            get total() { return this.items.reduce((s, i) => s + parseFloat(i.precio) * i.cantidad, 0); },
            get count() { return this.items.reduce((s, i) => s + i.cantidad, 0); },

            buildWaUrl(phone) {
                const fecha = new Date().toLocaleString('es-BO', {
                    weekday: 'long', day: 'numeric', month: 'long', year: 'numeric',
                    hour: '2-digit', minute: '2-digit'
                });

                let m = `🛒 *NUEVO PEDIDO*\n`;
                m += `🏪 ${CW_TIENDA_NOMBRE}\n`;
                m += `🗓️ ${fecha}\n\n`;
                m += `¡Hola! Quiero realizar el siguiente pedido:\n\n`;

                this.items.forEach((i, idx) => {
                    const precio   = parseFloat(i.precio);
                    const subtotal = (precio * i.cantidad).toFixed(2);
                    m += `${idx + 1}. *${i.nombre}*\n`;
                    m += `    ${i.cantidad} x Bs. ${precio.toFixed(2)}  =  Bs. ${subtotal}\n`;
                });

                m += `\n━━━━━━━━━━━━━━━\n`;
                m += `📦 Artículos: ${this.count}  (${this.items.length} producto${this.items.length !== 1 ? 's' : ''})\n`;
                m += `💰 *TOTAL: Bs. ${this.total.toFixed(2)}*\n`;
                m += `━━━━━━━━━━━━━━━\n\n`;
                m += `📌 _El precio es referencial; el vendedor confirmará disponibilidad, el total final y coordinará el pago (efectivo / QR) y la entrega._\n\n`;
                m += `¡Gracias! Quedo atento(a) a su respuesta. 🙌`;

                return `https://wa.me/${(phone||'').replace(/[^0-9]/g,'')}?text=${encodeURIComponent(m)}`;
            },

            requestOrder() {
                if (CW_VENDEDORES.length === 0) {
                    // fallback: usar teléfono de la tienda
                    window.open(this.buildWaUrl(CW_TIENDA_PHONE), '_blank');
                } else if (CW_VENDEDORES.length === 1) {
                    window.open(this.buildWaUrl(CW_VENDEDORES[0].whatsapp_number), '_blank');
                } else {
                    this.vendorPicker = true;
                }
            },

            selectVendor(phone) {
                window.open(this.buildWaUrl(phone), '_blank');
                this.vendorPicker = false;
            }
        });
    });
</script>

{{-- Sticky navbar --}}
<header class="sticky top-0 z-40 bg-zinc-950/95 backdrop-blur border-b border-zinc-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center gap-4">
        <a href="/catalogo/{{ $slug ?? '' }}" class="flex items-center gap-2 flex-shrink-0">
            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <span class="font-bold text-white text-lg hidden sm:block truncate max-w-[160px]">{{ $tiendaNombre ?? 'Catálogo' }}</span>
        </a>

        <div class="flex-1"></div>

        {{-- Cart button --}}
        <button @click="$store.cart.open = true"
                class="relative flex items-center gap-2 px-4 py-2 bg-zinc-800 hover:bg-zinc-700 rounded-xl transition text-sm font-medium">
            <svg class="w-5 h-5 text-zinc-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <span class="text-zinc-200" x-text="$store.cart.count > 0 ? $store.cart.count + ' items' : 'Carrito'"></span>
            <span x-show="$store.cart.count > 0"
                  x-text="$store.cart.count"
                  class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-indigo-600 text-white text-xs font-bold rounded-full flex items-center justify-center"></span>
        </button>
    </div>
</header>

{{-- Main content --}}
{{ $slot }}

{{-- Cart slide-over --}}
<div x-show="$store.cart.open"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click.self="$store.cart.open = false"
     class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50"
     style="display:none">

    <div x-show="$store.cart.open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="absolute right-0 top-0 h-full w-full max-w-sm bg-zinc-900 shadow-2xl flex flex-col border-l border-zinc-800">

        <div class="flex items-center justify-between px-5 py-4 border-b border-zinc-800">
            <h2 class="font-bold text-white text-base flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Tu carrito
                <span class="text-zinc-400 font-normal text-sm" x-show="$store.cart.count > 0" x-text="'(' + $store.cart.count + ')'"></span>
            </h2>
            <button @click="$store.cart.open = false" class="text-zinc-500 hover:text-white transition rounded-lg p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-3">
            <template x-if="$store.cart.items.length === 0">
                <div class="flex flex-col items-center justify-center h-full text-zinc-600 py-16">
                    <svg class="w-16 h-16 mb-4 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <p class="font-semibold text-zinc-400">Tu carrito está vacío</p>
                    <p class="text-sm text-zinc-600 mt-1">Agrega productos del catálogo</p>
                </div>
            </template>

            <template x-for="item in $store.cart.items" :key="item.id">
                <div class="bg-zinc-800/60 border border-zinc-700/50 rounded-xl p-3">
                    <div class="flex items-start justify-between gap-2 mb-2.5">
                        <p class="font-semibold text-white text-sm leading-snug" x-text="item.nombre"></p>
                        <button @click="$store.cart.remove(item.id)"
                                class="flex-shrink-0 w-5 h-5 flex items-center justify-center rounded-full text-zinc-600 hover:text-red-400 hover:bg-red-400/10 transition mt-0.5">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5">
                            <button @click="$store.cart.decrement(item.id)"
                                    class="w-7 h-7 rounded-lg bg-zinc-700 hover:bg-zinc-600 text-white flex items-center justify-center text-base leading-none transition font-bold">−</button>
                            <span class="text-white font-bold text-sm w-6 text-center tabular-nums" x-text="item.cantidad"></span>
                            <button @click="$store.cart.increment(item.id)"
                                    class="w-7 h-7 rounded-lg bg-zinc-700 hover:bg-zinc-600 text-white flex items-center justify-center text-base leading-none transition font-bold">+</button>
                        </div>
                        <div class="text-right">
                            <p class="text-white font-extrabold text-sm tabular-nums" x-text="'Bs. ' + (parseFloat(item.precio) * item.cantidad).toFixed(2)"></p>
                            <p class="text-zinc-600 text-[10px] tabular-nums" x-text="'Bs. ' + parseFloat(item.precio).toFixed(2) + ' c/u'"></p>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div class="border-t border-zinc-800 p-4 space-y-3" x-show="$store.cart.items.length > 0">

            {{-- Resumen total --}}
            <div x-show="!$store.cart.vendorPicker" class="flex justify-between items-center">
                <span class="text-zinc-400 font-medium text-sm">Total estimado</span>
                <span class="text-xl font-bold text-white" x-text="'Bs. ' + $store.cart.total.toFixed(2)"></span>
            </div>

            {{-- Botón principal: Pedir por WhatsApp --}}
            <div x-show="!$store.cart.vendorPicker">
                <button @click="$store.cart.requestOrder()"
                        class="flex items-center justify-center gap-2 w-full py-3 bg-green-600 hover:bg-green-500 text-white font-bold rounded-xl transition text-sm">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Pedir por WhatsApp
                </button>
                <button @click="$store.cart.clear()"
                        class="w-full py-2 text-zinc-500 hover:text-red-400 text-xs transition">
                    Vaciar carrito
                </button>
            </div>

            {{-- Vendor picker --}}
            <div x-show="$store.cart.vendorPicker" x-transition>
                <div class="flex items-center gap-2 mb-3">
                    <button @click="$store.cart.vendorPicker = false"
                            class="text-zinc-500 hover:text-white transition p-1 rounded-lg hover:bg-zinc-800">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <p class="text-sm font-semibold text-white">¿Con quién quieres hablar?</p>
                </div>
                <div class="space-y-2">
                    <template x-for="v in CW_VENDEDORES" :key="v.id">
                        <button @click="$store.cart.selectVendor(v.whatsapp_number)"
                                class="w-full flex items-center gap-3 px-4 py-3 bg-zinc-800 hover:bg-green-600/20 hover:border-green-500/40 border border-zinc-700 rounded-xl transition group">
                            <div class="w-8 h-8 rounded-full bg-zinc-700 group-hover:bg-green-600/30 flex items-center justify-center flex-shrink-0 transition">
                                <svg class="w-4 h-4 text-zinc-400 group-hover:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div class="flex-1 text-left">
                                <p class="text-sm font-semibold text-white group-hover:text-green-300 transition" x-text="v.name"></p>
                                <p class="text-xs text-zinc-500 group-hover:text-green-500/70 transition flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    WhatsApp
                                </p>
                            </div>
                            <svg class="w-4 h-4 text-zinc-600 group-hover:text-green-500 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </template>
                </div>
            </div>

        </div>
    </div>
</div>

@livewireScripts
</body>
</html>
