<div>
    {{-- Tienda no encontrada --}}
    @if($notFound)
    <div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
        <p class="text-6xl mb-4">🔍</p>
        <h2 class="text-2xl font-bold text-white mb-2">Tienda no encontrada</h2>
        <p class="text-zinc-400">El catálogo que buscas no existe o no está disponible.</p>
    </div>

    {{-- Error de conexión --}}
    @elseif($apiError)
    <div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
        <p class="text-6xl mb-4">⚡</p>
        <h2 class="text-2xl font-bold text-white mb-2">Servicio no disponible</h2>
        <p class="text-zinc-400">No pudimos conectarnos al servidor. Intenta de nuevo más tarde.</p>
        <button wire:click="$refresh" class="mt-6 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition">
            Reintentar
        </button>
    </div>

    @else

    {{-- Hero de la tienda --}}
    <div class="bg-gradient-to-b from-zinc-900 to-zinc-950 border-b border-zinc-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white">{{ $tienda['nombre'] ?? '' }}</h1>
            <p class="text-zinc-400 mt-2 text-sm">Explorá nuestro catálogo y pedí por WhatsApp</p>

            {{-- Search bar --}}
            <div class="mt-5 relative max-w-lg">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input wire:model.live.debounce.350ms="busqueda"
                       type="text"
                       placeholder="Buscar productos por nombre..."
                       class="w-full pl-10 pr-4 py-3 bg-zinc-800 border border-zinc-700 rounded-xl text-white placeholder-zinc-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                @if($busqueda)
                <button wire:click="$set('busqueda', '')" class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-500 hover:text-white transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                @endif
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">
        <div class="flex gap-7">

            {{-- Sidebar --}}
            <aside class="hidden lg:block w-52 flex-shrink-0">
                <div class="sticky top-24 space-y-1">
                    <p class="text-xs font-semibold uppercase tracking-widest text-zinc-500 mb-3 px-2">Categoría</p>

                    <button wire:click="$set('filtroCategoria', '')"
                            class="w-full text-left px-3 py-2.5 rounded-xl text-sm font-medium transition
                                   {{ $filtroCategoria === '' ? 'bg-indigo-600 text-white' : 'text-zinc-300 hover:bg-zinc-800' }}">
                        Todos los productos
                        <span class="float-right text-xs opacity-60">{{ $meta['total'] }}</span>
                    </button>

                    @foreach($categorias as $cat)
                    <button wire:click="$set('filtroCategoria', '{{ $cat['id'] }}')"
                            class="w-full text-left px-3 py-2.5 rounded-xl text-sm font-medium transition
                                   {{ (string)$filtroCategoria === (string)$cat['id'] ? 'bg-indigo-600 text-white' : 'text-zinc-300 hover:bg-zinc-800' }}">
                        {{ $cat['nombre'] }}
                    </button>
                    @endforeach
                </div>
            </aside>

            {{-- Products area --}}
            <main class="flex-1 min-w-0">

                {{-- Top bar: mobile category + sort --}}
                <div class="flex flex-wrap items-center gap-3 mb-6">
                    {{-- Mobile category select --}}
                    <select wire:model.live="filtroCategoria"
                            class="lg:hidden flex-1 px-3 py-2 bg-zinc-800 border border-zinc-700 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Todas las categorías</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat['id'] }}">{{ $cat['nombre'] }}</option>
                        @endforeach
                    </select>

                    <div class="ml-auto flex items-center gap-2">
                        <span class="text-zinc-500 text-xs hidden sm:block">{{ $meta['total'] }} productos</span>
                        <select wire:model.live="ordenar"
                                class="px-3 py-2 bg-zinc-800 border border-zinc-700 rounded-xl text-sm text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="nombre">A – Z</option>
                            <option value="precio_asc">Menor precio</option>
                            <option value="precio_desc">Mayor precio</option>
                        </select>
                    </div>
                </div>

                {{-- Loading overlay --}}
                <div wire:loading.delay class="fixed inset-0 z-30 bg-zinc-950/40 flex items-center justify-center">
                    <div class="bg-zinc-800 border border-zinc-700 rounded-2xl px-6 py-4 flex items-center gap-3">
                        <svg class="animate-spin w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span class="text-zinc-300 text-sm font-medium">Cargando...</span>
                    </div>
                </div>

                {{-- Product grid --}}
                @if(count($productos) === 0)
                <div class="flex flex-col items-center justify-center py-24 text-center">
                    <p class="text-4xl mb-3">🔍</p>
                    <p class="text-zinc-400 font-medium">No encontramos productos</p>
                    @if($busqueda || $filtroCategoria)
                    <button wire:click="$set('busqueda', ''); $set('filtroCategoria', '')"
                            class="mt-4 text-indigo-400 hover:text-indigo-300 text-sm transition">
                        Limpiar filtros
                    </button>
                    @endif
                </div>
                @else
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($productos as $p)
                    <div class="group bg-zinc-900 border border-zinc-800 rounded-2xl overflow-hidden hover:border-indigo-500/50 hover:shadow-xl hover:shadow-indigo-900/10 transition-all duration-200">

                        {{-- Image --}}
                        <a href="{{ route('catalogo.producto', [$slug, $p['id']]) }}" class="block">
                            @if($p['imagen_url'])
                                <img src="{{ $p['imagen_url'] }}" alt="{{ $p['nombre'] }}"
                                     class="w-full h-44 object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-44 bg-zinc-800 flex items-center justify-center">
                                    <svg class="w-14 h-14 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                </div>
                            @endif
                        </a>

                        <div class="p-4">
                            {{-- Category badge --}}
                            @if(!empty($p['categoria']))
                            <span class="inline-block text-xs font-medium text-indigo-400 bg-indigo-500/10 px-2 py-0.5 rounded-full mb-2">
                                {{ $p['categoria']['nombre'] }}
                            </span>
                            @endif

                            {{-- Name --}}
                            <a href="{{ route('catalogo.producto', [$slug, $p['id']]) }}"
                               class="block font-bold text-white leading-snug hover:text-indigo-300 transition line-clamp-2 mb-1">
                                {{ $p['nombre'] }}
                            </a>

                            {{-- Description --}}
                            @if(!empty($p['descripcion']))
                            <p class="text-zinc-500 text-xs line-clamp-2 mb-3">{{ $p['descripcion'] }}</p>
                            @endif

                            {{-- Price + stock --}}
                            <div class="flex items-end justify-between mb-4">
                                <span class="text-2xl font-extrabold text-amber-400">Bs. {{ number_format($p['precio'], 2) }}</span>
                                <span class="text-xs text-zinc-600 bg-zinc-800 px-2 py-0.5 rounded-full">
                                    {{ $p['stock'] }} en stock
                                </span>
                            </div>

                            {{-- Actions --}}
                            <div class="space-y-2">
                                {{-- WhatsApp --}}
                                @php
                                    $waMsg = urlencode("Hola *{$tienda['nombre']}*, me interesa:\n\n• {$p['nombre']} — Bs. " . number_format($p['precio'], 2) . "\n\n¿Está disponible?");
                                    $waPhone = preg_replace('/[^0-9]/', '', $tienda['telefono_principal'] ?? '');
                                @endphp
                                <a href="https://wa.me/{{ $waPhone }}?text={{ $waMsg }}"
                                   target="_blank"
                                   class="flex items-center justify-center gap-2 w-full py-2.5 bg-green-600 hover:bg-green-500 text-white text-sm font-semibold rounded-xl transition">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                    Pedir por WhatsApp
                                </a>

                                {{-- Add to cart (Alpine.js) --}}
                                <button @click="$store.cart.add({
                                            id: {{ $p['id'] }},
                                            nombre: {{ json_encode($p['nombre']) }},
                                            precio: {{ $p['precio'] }},
                                            imagen_url: {{ json_encode($p['imagen_url']) }}
                                        })"
                                        class="flex items-center justify-center gap-2 w-full py-2.5 bg-zinc-800 hover:bg-indigo-600 border border-zinc-700 hover:border-indigo-600 text-zinc-200 hover:text-white text-sm font-semibold rounded-xl transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    Agregar al carrito
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($meta['last_page'] > 1)
                <div class="flex items-center justify-center gap-2 mt-10">
                    <button wire:click="paginar({{ $meta['current_page'] - 1 }})"
                            @if($meta['current_page'] <= 1) disabled @endif
                            class="px-4 py-2 bg-zinc-800 border border-zinc-700 text-zinc-300 rounded-xl text-sm hover:bg-zinc-700 disabled:opacity-40 disabled:cursor-not-allowed transition">
                        ← Anterior
                    </button>

                    @for($i = max(1, $meta['current_page'] - 2); $i <= min($meta['last_page'], $meta['current_page'] + 2); $i++)
                    <button wire:click="paginar({{ $i }})"
                            class="w-10 h-10 rounded-xl text-sm font-semibold transition
                                   {{ $i == $meta['current_page'] ? 'bg-indigo-600 text-white' : 'bg-zinc-800 border border-zinc-700 text-zinc-300 hover:bg-zinc-700' }}">
                        {{ $i }}
                    </button>
                    @endfor

                    <button wire:click="paginar({{ $meta['current_page'] + 1 }})"
                            @if($meta['current_page'] >= $meta['last_page']) disabled @endif
                            class="px-4 py-2 bg-zinc-800 border border-zinc-700 text-zinc-300 rounded-xl text-sm hover:bg-zinc-700 disabled:opacity-40 disabled:cursor-not-allowed transition">
                        Siguiente →
                    </button>
                </div>
                @endif
                @endif

            </main>
        </div>
    </div>

    {{-- Footer --}}
    <footer class="border-t border-zinc-800 mt-16 py-8 text-center">
        <p class="text-zinc-600 text-xs">
            {{ $tienda['nombre'] ?? '' }} · Catálogo en línea
            @if(!empty($tienda['telefono_principal']))
                · <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $tienda['telefono_principal']) }}"
                     target="_blank"
                     class="text-green-600 hover:text-green-400 transition">
                    WhatsApp: {{ $tienda['telefono_principal'] }}
                </a>
            @endif
        </p>
    </footer>

    @endif
</div>
