<div class="max-w-5xl mx-auto px-4 sm:px-6 py-10">

    @if($notFound)
    <div class="flex flex-col items-center justify-center py-24 text-center">
        <p class="text-5xl mb-4">📦</p>
        <h2 class="text-2xl font-bold text-white mb-2">Producto no encontrado</h2>
        <p class="text-zinc-400 mb-6">Este producto no existe o ya no esta disponible.</p>
        <a href="{{ route('catalogo', $slug) }}"
           class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition text-sm">
            Volver al catalogo
        </a>
    </div>
    @else

    <nav class="flex items-center gap-2 text-sm text-zinc-500 mb-8">
        <a href="{{ route('catalogo', $slug) }}" class="hover:text-white transition">Catalogo</a>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        @if($producto->categoria)
        <span class="text-zinc-500">{{ $producto->categoria->nombre }}</span>
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        @endif
        <span class="text-zinc-300 truncate">{{ $producto->nombre }}</span>
    </nav>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

        <div class="rounded-2xl overflow-hidden bg-zinc-900 border border-zinc-800 aspect-square flex items-center justify-center p-4">
            @if($producto->imagen)
                <img src="{{ str_starts_with($producto->imagen, 'http') ? $producto->imagen : Storage::url($producto->imagen) }}"
                     alt="{{ $producto->nombre }}"
                     class="w-full h-full object-contain">
            @else
                <svg class="w-24 h-24 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            @endif
        </div>

        <div class="flex flex-col">
            @if($producto->categoria)
            <span class="inline-block text-xs font-semibold text-indigo-400 bg-indigo-500/10 px-3 py-1 rounded-full w-fit mb-3">
                {{ $producto->categoria->nombre }}
            </span>
            @endif

            <h1 class="text-3xl font-extrabold text-white leading-tight mb-2">
                {{ $producto->nombre }}
            </h1>

            @if($producto->sku)
            <p class="text-zinc-600 text-xs font-mono mb-4">SKU: {{ $producto->sku }}</p>
            @endif

            <div class="flex items-center gap-4 mb-5">
                <span class="text-4xl font-extrabold text-amber-400">
                    Bs. {{ number_format($producto->precio, 2) }}
                </span>
                <span class="px-3 py-1 rounded-full text-xs font-semibold
                             {{ $producto->stock > 0 ? 'bg-emerald-500/10 text-emerald-400' : 'bg-red-500/10 text-red-400' }}">
                    {{ $producto->stock > 0 ? ($producto->stock . ' ' . $producto->unidad_medida . ' en stock') : 'Sin stock' }}
                </span>
            </div>

            @if($producto->descripcion)
            <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4 mb-4">
                <p class="text-zinc-300 text-sm leading-relaxed">{{ $producto->descripcion }}</p>
            </div>
            @endif

            {{-- Atributos dinamicos --}}
            @if($producto->atributos->count() > 0)
            <div class="bg-zinc-900 border border-zinc-800 rounded-xl p-4 mb-4">
                <p class="text-xs font-bold text-zinc-500 uppercase tracking-wide mb-2">Especificaciones</p>
                <div class="grid grid-cols-2 gap-2">
                    @foreach($producto->atributos as $attr)
                    <div class="flex justify-between items-center py-1 border-b border-zinc-800 last:border-0">
                        <span class="text-xs text-zinc-500">{{ $attr->nombre }}</span>
                        <span class="text-xs font-semibold text-zinc-300">{{ $attr->valor }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Selector de vendedores/asesores --}}
            @if($producto->stock > 0)
            <div class="space-y-3 mt-auto">
                @if(count($vendedores) > 0)
                <div x-data="{ open: false }">
                    <p class="text-xs font-bold text-zinc-500 uppercase tracking-wide mb-2">Habla con un asesor</p>
                    <div class="space-y-2">
                        @foreach($vendedores as $v)
                        @php
                            $waMsg = urlencode("Hola *{$v['name']}*, estoy viendo el producto:\n\n*{$producto->nombre}*\nPrecio: Bs. " . number_format($producto->precio, 2) . "\n\nMe gustaria mas informacion.");
                            $waPhone = preg_replace('/[^0-9]/', '', $v['whatsapp_number']);
                        @endphp
                        <a href="https://wa.me/{{ $waPhone }}?text={{ $waMsg }}"
                           target="_blank"
                           class="flex items-center gap-3 w-full py-3 px-4 bg-zinc-900 hover:bg-green-600/10 border border-zinc-800 hover:border-green-500/30 rounded-xl transition group">
                            <div class="w-9 h-9 rounded-full bg-green-600/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-green-400" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-white group-hover:text-green-300 transition truncate">{{ $v['name'] }}</p>
                                <p class="text-xs text-zinc-500">{{ ucfirst($v['role']) }}</p>
                            </div>
                            <svg class="w-4 h-4 text-zinc-600 group-hover:text-green-400 transition flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                        @endforeach
                    </div>
                </div>
                @else
                @php
                    $waMsg = urlencode("Hola *{$tienda->nombre}*, me interesa:\n\n*{$producto->nombre}* - Bs. " . number_format($producto->precio, 2) . "\n\nEsta disponible?");
                    $waPhone = preg_replace('/[^0-9]/', '', $tienda->telefono_principal ?? '');
                @endphp
                <a href="https://wa.me/{{ $waPhone }}?text={{ $waMsg }}"
                   target="_blank"
                   class="flex items-center justify-center gap-3 w-full py-3.5 bg-green-600 hover:bg-green-500 text-white font-bold rounded-xl transition text-base">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Pedir por WhatsApp
                </a>
                @endif

                <button @click="$store.cart.add({
                            id: {{ $producto->id }},
                            nombre: {{ json_encode($producto->nombre) }},
                            precio: {{ $producto->precio }}
                        })"
                        class="flex items-center justify-center gap-3 w-full py-3.5 bg-zinc-800 hover:bg-indigo-600 border border-zinc-700 hover:border-indigo-600 text-zinc-200 hover:text-white font-bold rounded-xl transition-all text-base">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    Agregar al carrito
                </button>
            </div>
            @else
            <div class="mt-auto p-4 bg-zinc-900 border border-zinc-700 rounded-xl text-center">
                <p class="text-zinc-400 font-medium">Este producto no esta disponible actualmente</p>
                <a href="{{ route('catalogo', $slug) }}" class="inline-block mt-3 text-indigo-400 hover:text-indigo-300 text-sm transition">
                    Ver otros productos
                </a>
            </div>
            @endif

            <a href="{{ route('catalogo', $slug) }}"
               class="mt-5 flex items-center gap-1.5 text-zinc-500 hover:text-zinc-300 text-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Volver al catalogo
            </a>
        </div>
    </div>

    @endif
</div>
