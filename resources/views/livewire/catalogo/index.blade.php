<div>
{{-- ══════════════════════════════════════════════════
     404 / API ERROR
══════════════════════════════════════════════════ --}}
@if($notFound)
<div class="flex flex-col items-center justify-center min-h-[70vh] text-center px-4">
    <div class="w-24 h-24 rounded-3xl bg-zinc-900 border border-zinc-800 flex items-center justify-center mb-6 shadow-inner">
        <svg class="w-12 h-12 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <h2 class="text-3xl font-extrabold text-white mb-2">Tienda no encontrada</h2>
    <p class="text-zinc-500 max-w-sm">El catálogo que buscas no existe o no está disponible actualmente.</p>
</div>

@else
{{-- ══════════════════════════════════════════════════
     HERO
══════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden">
    {{-- Gradient bg --}}
    <div class="absolute inset-0 bg-gradient-to-br from-indigo-950 via-zinc-950 to-zinc-950"></div>
    <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_rgba(99,102,241,0.18)_0%,_transparent_70%)]"></div>
    {{-- Grid texture --}}
    <div class="absolute inset-0 opacity-[0.03]" style="background-image:linear-gradient(rgba(255,255,255,.8) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.8) 1px,transparent 1px);background-size:40px 40px"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 pt-12 pb-10">
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8">

            {{-- Store info --}}
            <div class="max-w-xl">
                <div class="inline-flex items-center gap-2 bg-indigo-500/10 border border-indigo-500/20 px-3 py-1.5 rounded-full mb-4">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse"></span>
                    <span class="text-indigo-300 text-xs font-semibold tracking-wider uppercase">Catálogo en línea</span>
                </div>
                <h1 class="text-4xl sm:text-5xl font-extrabold text-white leading-none tracking-tight">
                    {{ $tienda->nombre ?? '' }}
                </h1>
                <p class="text-zinc-400 mt-3 text-base max-w-md leading-relaxed">
                    Explorá nuestra selección de productos y realizá tu pedido directamente por WhatsApp con atención personalizada.
                </p>

                {{-- Stats row --}}
                <div class="flex items-center gap-6 mt-5">
                    <div>
                        <span class="text-2xl font-extrabold text-white">{{ $meta['total'] }}</span>
                        <span class="text-zinc-500 text-sm ml-1">{{ $meta['total'] === 1 ? 'producto' : 'productos' }}</span>
                    </div>
                    @if(count($categorias) > 0)
                    <div class="w-px h-6 bg-zinc-800"></div>
                    <div>
                        <span class="text-2xl font-extrabold text-white">{{ count($categorias) }}</span>
                        <span class="text-zinc-500 text-sm ml-1">{{ count($categorias) === 1 ? 'categoría' : 'categorías' }}</span>
                    </div>
                    @endif
                    @if(!empty($tienda->telefono_principal))
                    <div class="w-px h-6 bg-zinc-800"></div>
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $tienda->telefono_principal) }}"
                       target="_blank"
                       class="inline-flex items-center gap-1.5 text-green-400 hover:text-green-300 text-sm font-semibold transition">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Contactar
                    </a>
                    @endif
                </div>
            </div>

            {{-- Search --}}
            <div class="lg:w-96 flex-shrink-0">
                <div class="relative">
                    <div class="absolute inset-0 rounded-2xl bg-indigo-500/10 blur-xl"></div>
                    <div class="relative">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-zinc-500 z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input wire:model.live.debounce.400ms="busqueda"
                               type="search"
                               placeholder="Buscar productos, SKU..."
                               class="w-full pl-11 pr-10 py-3.5 bg-zinc-900/80 backdrop-blur border border-zinc-700/80 rounded-2xl text-white placeholder-zinc-600 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/70 focus:border-indigo-500/50 transition">
                        @if($busqueda)
                        <button wire:click="$set('busqueda', '')"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 w-6 h-6 flex items-center justify-center rounded-full bg-zinc-700 hover:bg-zinc-600 text-zinc-400 hover:text-white transition">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom fade --}}
    <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-indigo-800/40 to-transparent"></div>
</section>

{{-- ══════════════════════════════════════════════════
     BODY (sidebar + grid)
══════════════════════════════════════════════════ --}}
<div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 flex gap-8">

    {{-- ── Sidebar ─────────────────────────────────── --}}
    <aside class="hidden lg:block w-52 flex-shrink-0 space-y-7">

        {{-- Categories --}}
        @if(count($categorias) > 0)
        <div>
            <p class="text-[10px] font-black uppercase tracking-[0.15em] text-zinc-600 mb-3 pl-1">Categorías</p>
            <nav class="space-y-0.5">
                <button wire:click="$set('filtroCategoria', '')"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-sm font-semibold transition-all
                               {{ $filtroCategoria === ''
                                  ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/50'
                                  : 'text-zinc-500 hover:text-white hover:bg-zinc-800/70' }}">
                    <span>Todos</span>
                    <span class="text-xs tabular-nums {{ $filtroCategoria === '' ? 'text-indigo-200' : 'text-zinc-700' }}">{{ $meta['total'] }}</span>
                </button>
                @foreach($categorias as $cat)
                <button wire:click="$set('filtroCategoria', '{{ $cat->id }}')"
                        class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-sm font-semibold transition-all
                               {{ (string)$filtroCategoria === (string)$cat->id
                                  ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/50'
                                  : 'text-zinc-500 hover:text-white hover:bg-zinc-800/70' }}">
                    <span class="truncate text-left">{{ $cat->nombre }}</span>
                </button>
                @endforeach
            </nav>
        </div>
        @endif

        {{-- Sort --}}
        <div>
            <p class="text-[10px] font-black uppercase tracking-[0.15em] text-zinc-600 mb-3 pl-1">Ordenar</p>
            <div class="space-y-0.5">
                @foreach(['nombre' => 'Nombre A–Z', 'precio_asc' => 'Menor precio', 'precio_desc' => 'Mayor precio'] as $val => $label)
                <button wire:click="$set('ordenar', '{{ $val }}')"
                        class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-sm font-semibold transition-all
                               {{ $ordenar === $val
                                  ? 'text-indigo-400 bg-indigo-500/10'
                                  : 'text-zinc-600 hover:text-white hover:bg-zinc-800/70' }}">
                    <span class="w-4 h-4 flex-shrink-0 flex items-center justify-center">
                        @if($ordenar === $val)
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        @endif
                    </span>
                    {{ $label }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- Clear filters --}}
        @if($busqueda || $filtroCategoria)
        <button wire:click="$set('busqueda', ''); $set('filtroCategoria', '')"
                class="flex items-center gap-2 text-xs text-red-400 hover:text-red-300 transition font-semibold px-1">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            Limpiar filtros
        </button>
        @endif
    </aside>

    {{-- ── Main area ───────────────────────────────── --}}
    <main class="flex-1 min-w-0">

        {{-- Mobile controls --}}
        <div class="lg:hidden flex gap-2 mb-5">
            <select wire:model.live="filtroCategoria"
                    class="flex-1 px-3 py-2.5 bg-zinc-900 border border-zinc-800 rounded-xl text-sm text-zinc-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Todas las categorías</option>
                @foreach($categorias as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                @endforeach
            </select>
            <select wire:model.live="ordenar"
                    class="px-3 py-2.5 bg-zinc-900 border border-zinc-800 rounded-xl text-sm text-zinc-300 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="nombre">A–Z</option>
                <option value="precio_asc">↑ Precio</option>
                <option value="precio_desc">↓ Precio</option>
            </select>
        </div>

        {{-- Filter chips --}}
        @if($busqueda || $filtroCategoria)
        <div class="flex flex-wrap gap-2 mb-5">
            @if($busqueda)
            <span class="inline-flex items-center gap-1.5 pl-3 pr-2 py-1.5 bg-indigo-500/10 border border-indigo-500/30 text-indigo-300 text-xs rounded-full font-semibold">
                <svg class="w-3 h-3 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                "{{ $busqueda }}"
                <button wire:click="$set('busqueda', '')" class="ml-0.5 w-4 h-4 flex items-center justify-center rounded-full hover:bg-indigo-400/20 transition">
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </span>
            @endif
            @if($filtroCategoria)
            @php $catNombre = collect($categorias)->firstWhere('id', (int)$filtroCategoria)['nombre'] ?? $filtroCategoria; @endphp
            <span class="inline-flex items-center gap-1.5 pl-3 pr-2 py-1.5 bg-indigo-500/10 border border-indigo-500/30 text-indigo-300 text-xs rounded-full font-semibold">
                <svg class="w-3 h-3 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                {{ $catNombre }}
                <button wire:click="$set('filtroCategoria', '')" class="ml-0.5 w-4 h-4 flex items-center justify-center rounded-full hover:bg-indigo-400/20 transition">
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </span>
            @endif
        </div>
        @endif

        {{-- Loading bar --}}
        <div wire:loading.delay.shortest class="mb-5">
            <div class="h-0.5 bg-zinc-800 rounded-full overflow-hidden">
                <div class="h-full w-1/2 bg-gradient-to-r from-indigo-600 to-violet-500 rounded-full animate-pulse"></div>
            </div>
        </div>

        {{-- ── Empty state ──────────────────────────── --}}
        @if(count($productos) === 0)
        <div class="flex flex-col items-center justify-center py-28 text-center">
            <div class="w-20 h-20 rounded-3xl bg-zinc-900 border border-zinc-800 flex items-center justify-center mb-5 shadow-xl">
                <svg class="w-9 h-9 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <p class="text-white font-bold text-xl mb-2">Sin resultados</p>
            <p class="text-zinc-600 text-sm mb-6">No encontramos productos que coincidan con tu búsqueda.</p>
            @if($busqueda || $filtroCategoria)
            <button wire:click="$set('busqueda', ''); $set('filtroCategoria', '')"
                    class="px-5 py-2.5 bg-zinc-800 hover:bg-zinc-700 border border-zinc-700 text-zinc-300 hover:text-white text-sm font-semibold rounded-xl transition">
                Ver todos los productos
            </button>
            @endif
        </div>

        @else
        {{-- ── Product grid ─────────────────────────── --}}
        <div wire:loading.class="opacity-40 pointer-events-none" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 transition-opacity duration-200">
            @foreach($productos as $p)
            @php $isLowStock = $p->stock <= 3; @endphp
            <article class="group flex flex-col bg-zinc-900 rounded-2xl overflow-hidden border border-zinc-800
                            hover:border-indigo-500/40 hover:shadow-2xl hover:shadow-indigo-950/60
                            transition-all duration-300 ease-out">

                {{-- Image --}}
                <a href="{{ route('catalogo.producto', [$slug, $p->id]) }}"
                   class="relative block flex-shrink-0 bg-zinc-800/60 overflow-hidden"
                   style="aspect-ratio:4/3">

                    @if($p->imagen)
                    <img src="{{ $p->imagen_url }}" alt="{{ $p->nombre }}"
                         loading="lazy"
                         class="absolute inset-0 w-full h-full object-contain p-4
                                group-hover:scale-105 transition-transform duration-500 ease-out">
                    @else
                    <div class="absolute inset-0 flex items-center justify-center">
                        <svg class="w-14 h-14 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    @endif

                    {{-- Gradient overlay for readability of badges --}}
                    <div class="absolute inset-x-0 top-0 h-16 bg-gradient-to-b from-black/40 to-transparent pointer-events-none"></div>

                    {{-- Badges --}}
                    <div class="absolute top-2.5 left-2.5 right-2.5 flex items-start justify-between gap-1">
                        @if(!empty($p->categoria))
                        <span class="text-[10px] font-bold uppercase tracking-wide
                                     bg-black/50 backdrop-blur-sm text-zinc-300 border border-white/10
                                     px-2 py-1 rounded-full leading-none">
                            {{ $p->categoria->nombre }}
                        </span>
                        @else
                        <span></span>
                        @endif
                        @if($isLowStock)
                        <span class="text-[10px] font-bold uppercase tracking-wide
                                     bg-amber-500 text-white px-2 py-1 rounded-full leading-none shadow-lg shadow-amber-900/50 flex-shrink-0">
                            ¡Últimas {{ $p->stock }}!
                        </span>
                        @endif
                    </div>
                </a>

                {{-- Info --}}
                <div class="flex flex-col flex-1 p-4">

                    <a href="{{ route('catalogo.producto', [$slug, $p->id]) }}"
                       class="font-bold text-white text-sm leading-snug hover:text-indigo-300 transition-colors line-clamp-2 mb-1.5">
                        {{ $p->nombre }}
                    </a>

                    @if(!empty($p->sku))
                    <p class="text-zinc-700 text-[10px] font-mono mb-2">SKU: {{ $p->sku }}</p>
                    @endif

                    @if(!empty($p->descripcion))
                    <p class="text-zinc-600 text-xs line-clamp-2 leading-relaxed mb-3">{{ $p->descripcion }}</p>
                    @else
                    <div class="mb-3"></div>
                    @endif

                    {{-- Price --}}
                    <div class="mt-auto mb-4">
                        <div class="flex items-end justify-between gap-2">
                            <span class="text-2xl font-extrabold tracking-tight bg-gradient-to-r from-white to-zinc-300 bg-clip-text text-transparent">
                                Bs. {{ number_format($p->precio, 2) }}
                            </span>
                            <span class="text-[10px] font-semibold px-2 py-1 rounded-lg
                                         {{ $isLowStock
                                            ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20'
                                            : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' }}">
                                {{ $p->stock }} en stock
                            </span>
                        </div>
                    </div>

                    {{-- Action --}}
                    <button x-data
                            @click="$store.cart.add({
                                id: {{ $p->id }},
                                nombre: {{ json_encode($p->nombre) }},
                                precio: {{ $p->precio }}
                            })"
                            class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl text-xs font-bold transition-all
                                   bg-zinc-800 hover:bg-indigo-600 border border-zinc-700 hover:border-indigo-500
                                   text-zinc-300 hover:text-white shadow-sm">
                        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Agregar al carrito
                    </button>
                </div>
            </article>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($meta['last_page'] > 1)
        <div class="flex items-center justify-center gap-1.5 mt-12">
            <button wire:click="paginar({{ $meta['current_page'] - 1 }})"
                    @disabled($meta['current_page'] <= 1)
                    class="flex items-center gap-1.5 px-4 py-2.5 bg-zinc-900 border border-zinc-800 text-zinc-400 rounded-xl text-sm font-semibold
                           hover:bg-zinc-800 hover:text-white disabled:opacity-30 disabled:cursor-not-allowed transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Anterior
            </button>

            @for($i = max(1, $meta['current_page'] - 2); $i <= min($meta['last_page'], $meta['current_page'] + 2); $i++)
            <button wire:click="paginar({{ $i }})"
                    class="w-10 h-10 rounded-xl text-sm font-bold transition
                           {{ $i == $meta['current_page']
                              ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/50 border border-indigo-500/50'
                              : 'bg-zinc-900 border border-zinc-800 text-zinc-500 hover:bg-zinc-800 hover:text-white' }}">
                {{ $i }}
            </button>
            @endfor

            <button wire:click="paginar({{ $meta['current_page'] + 1 }})"
                    @disabled($meta['current_page'] >= $meta['last_page'])
                    class="flex items-center gap-1.5 px-4 py-2.5 bg-zinc-900 border border-zinc-800 text-zinc-400 rounded-xl text-sm font-semibold
                           hover:bg-zinc-800 hover:text-white disabled:opacity-30 disabled:cursor-not-allowed transition">
                Siguiente
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </div>
        <p class="text-center text-xs text-zinc-700 mt-3">
            Página {{ $meta['current_page'] }} de {{ $meta['last_page'] }} · {{ $meta['total'] }} productos
        </p>
        @endif
        @endif

    </main>
</div>

{{-- ── Footer ───────────────────────────────────────── --}}
<footer class="mt-16 border-t border-zinc-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-7 h-7 bg-indigo-600 rounded-lg flex items-center justify-center shadow-lg shadow-indigo-900/50">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
            <span class="text-zinc-500 text-sm font-semibold">{{ $tienda->nombre ?? '' }}</span>
        </div>
        @if(!empty($tienda->telefono_principal))
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $tienda->telefono_principal) }}"
           target="_blank"
           class="flex items-center gap-2 text-zinc-600 hover:text-green-400 text-xs font-medium transition">
            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            Atención por WhatsApp
        </a>
        @endif
    </div>
</footer>

@endif
</div>
