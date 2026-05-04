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
    document.addEventListener('alpine:init', () => {
        Alpine.store('cart', {
            open: false,
            items: JSON.parse(localStorage.getItem('cw_cart') || '[]'),

            add(product) {
                const found = this.items.find(i => i.id === product.id);
                if (found) { found.cantidad++; } else { this.items.push({...product, cantidad: 1}); }
                this.save();
                this.open = true;
            },
            remove(id) { this.items = this.items.filter(i => i.id !== id); this.save(); },
            increment(id) { const i = this.items.find(i => i.id === id); if (i) { i.cantidad++; this.save(); } },
            decrement(id) {
                const i = this.items.find(i => i.id === id);
                if (i) { i.cantidad > 1 ? i.cantidad-- : this.remove(id); this.save(); }
            },
            clear() { this.items = []; this.save(); },
            save() { localStorage.setItem('cw_cart', JSON.stringify(this.items)); },
            get total() { return this.items.reduce((s, i) => s + parseFloat(i.precio) * i.cantidad, 0); },
            get count() { return this.items.reduce((s, i) => s + i.cantidad, 0); },
            waUrl(phone, nombre) {
                let m = `Hola *${nombre}*, quiero realizar el siguiente pedido:\n\n`;
                this.items.forEach(i => {
                    m += `• ${i.nombre} x${i.cantidad} = Bs. ${(parseFloat(i.precio)*i.cantidad).toFixed(2)}\n`;
                });
                m += `\n*Total: Bs. ${this.total.toFixed(2)}*`;
                return `https://wa.me/${(phone||'').replace(/[^0-9]/g,'')}?text=${encodeURIComponent(m)}`;
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
                <div class="flex gap-3 bg-zinc-800 rounded-xl p-3 border border-zinc-700/50">
                    <div class="w-14 h-14 rounded-lg overflow-hidden flex-shrink-0 bg-zinc-700 flex items-center justify-center">
                        <img :src="item.imagen_url" :alt="item.nombre"
                             class="w-full h-full object-contain p-1"
                             x-show="item.imagen_url"
                             x-on:error="$el.style.display='none'">
                        <svg x-show="!item.imagen_url" class="w-6 h-6 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-white text-sm leading-tight truncate" x-text="item.nombre"></p>
                        <p class="text-amber-400 font-bold text-xs mt-0.5" x-text="'Bs. ' + parseFloat(item.precio).toFixed(2) + ' c/u'"></p>
                        <div class="flex items-center gap-1.5 mt-2">
                            <button @click="$store.cart.decrement(item.id)"
                                    class="w-6 h-6 rounded-full bg-zinc-700 hover:bg-zinc-600 text-white flex items-center justify-center text-sm leading-none transition">−</button>
                            <span class="text-white font-bold text-sm w-5 text-center" x-text="item.cantidad"></span>
                            <button @click="$store.cart.increment(item.id)"
                                    class="w-6 h-6 rounded-full bg-zinc-700 hover:bg-zinc-600 text-white flex items-center justify-center text-sm leading-none transition">+</button>
                            <span class="ml-auto text-zinc-300 font-bold text-sm"
                                  x-text="'Bs. ' + (parseFloat(item.precio) * item.cantidad).toFixed(2)"></span>
                        </div>
                    </div>
                    <button @click="$store.cart.remove(item.id)" class="text-zinc-600 hover:text-red-400 transition self-start mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </template>
        </div>

        <div class="border-t border-zinc-800 p-4 space-y-3" x-show="$store.cart.items.length > 0">
            <div class="flex justify-between items-center">
                <span class="text-zinc-400 font-medium text-sm">Total estimado</span>
                <span class="text-xl font-bold text-white" x-text="'Bs. ' + $store.cart.total.toFixed(2)"></span>
            </div>
            <a :href="$store.cart.waUrl('{{ $tiendaPhone ?? '' }}', '{{ $tiendaNombre ?? 'la tienda' }}')"
               target="_blank"
               class="flex items-center justify-center gap-2 w-full py-3 bg-green-600 hover:bg-green-500 text-white font-bold rounded-xl transition text-sm">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Pedir todo por WhatsApp
            </a>
            <button @click="$store.cart.clear()"
                    class="w-full py-2 text-zinc-500 hover:text-red-400 text-xs transition">
                Vaciar carrito
            </button>
        </div>
    </div>
</div>

@livewireScripts
</body>
</html>
