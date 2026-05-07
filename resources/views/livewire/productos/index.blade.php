<div class="space-y-4">
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">
        {{ session('success') }}
    </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Productos</h2>
            <p class="text-sm text-slate-500">Administra el catálogo de productos de tu tienda</p>
        </div>
        <button wire:click="openCreate"
                class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nuevo Producto
        </button>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 flex flex-wrap gap-3">
        <input wire:model.live.debounce.300ms="search" type="text"
               placeholder="Buscar por nombre o SKU..."
               class="flex-1 min-w-52 px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <select wire:model.live="filtroCategoria"
                class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Todas las categorías</option>
            @foreach($categorias as $cat)
                <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
            @endforeach
        </select>
        <select wire:model.live="filtroEstado"
                class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Todos los estados</option>
            <option value="Disponible">Disponible</option>
            <option value="Agotado">Agotado</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide w-16">Img</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Producto</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Categoría</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Precio</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Stock</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Estado</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($productos as $p)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-3">
                            @if($p->imagen)
                                <img src="{{ $p->imagen_url }}"
                                     alt="{{ $p->nombre }}"
                                     class="w-10 h-10 rounded-lg object-cover border border-slate-200">
                            @else
                                <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center border border-slate-200">
                                    <svg class="w-5 h-5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <p class="font-medium text-slate-800">{{ $p->nombre }}</p>
                            @if($p->sku)
                                <p class="text-xs text-slate-400 font-mono">SKU: {{ $p->sku }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-slate-600">{{ $p->categoria->nombre ?? '—' }}</td>
                        <td class="px-5 py-3.5 font-semibold text-slate-800">Bs. {{ number_format($p->precio, 2) }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold {{ $p->stock <= $p->stock_minimo ? 'text-amber-600' : 'text-slate-800' }}">
                                    {{ $p->stock }}
                                </span>
                                @if($p->stock > 0 && $p->stock <= $p->stock_minimo)
                                    <span class="text-xs text-amber-500 font-medium">⚠ bajo</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-400">mín: {{ $p->stock_minimo }}</p>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                {{ $p->estado === 'Disponible' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                {{ $p->estado }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="openEdit({{ $p->id }})"
                                        class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button wire:click="confirmDelete({{ $p->id }})"
                                        class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                            <p class="text-3xl mb-2">📦</p>
                            <p class="font-medium">No se encontraron productos</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($productos->hasPages())
        <div class="px-5 py-3 border-t border-slate-100">{{ $productos->links() }}</div>
        @endif
    </div>

    {{-- Create/Edit Modal --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40" wire:click="$set('showModal', false)"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] flex flex-col">
            <div class="px-6 py-5 border-b border-slate-100">
                <h3 class="text-base font-semibold text-slate-800">
                    {{ $editingId ? 'Editar Producto' : 'Nuevo Producto' }}
                </h3>
            </div>

            <div class="px-6 py-5 space-y-4 overflow-y-auto flex-1">

                {{-- Image --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Imagen del producto</label>

                    {{-- Preview (archivo subido, imagen actual, o URL) --}}
                    @php
                        $previewUrl = $imagen
                            ? $imagen->temporaryUrl()
                            : ($imagenUrl ?: ($imagenActual ? $this->imagenDisplayUrl($imagenActual) : null));
                    @endphp

                    @if($previewUrl)
                    <div class="relative mb-3">
                        <div class="w-full h-40 rounded-xl border border-slate-200 overflow-hidden bg-slate-100">
                            <img src="{{ $previewUrl }}" alt="Preview"
                                 class="w-full h-full object-contain">
                        </div>
                        <button type="button" wire:click="removeImagen"
                                class="absolute top-2 right-2 w-7 h-7 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center shadow transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                        @if($imagen)
                            <p class="mt-1 text-xs text-slate-400">{{ $imagen->getClientOriginalName() }} · {{ round($imagen->getSize() / 1024) }} KB</p>
                        @elseif($imagenUrl)
                            <p class="mt-1 text-xs text-slate-400 truncate">🔗 {{ $imagenUrl }}</p>
                        @endif
                    </div>
                    @endif

                    {{-- Subir archivo --}}
                    @if(!$previewUrl)
                    <label for="img-upload"
                           class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-slate-300 rounded-xl cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition group mb-2">
                        <svg class="w-7 h-7 text-slate-300 group-hover:text-indigo-400 mb-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <p class="text-xs text-slate-500 group-hover:text-indigo-600 font-medium transition">Subir desde mi PC</p>
                        <p class="text-xs text-slate-400">PNG, JPG, WEBP · Máx 2 MB</p>
                    </label>
                    <input id="img-upload" wire:model="imagen" type="file" accept="image/*" class="hidden">

                    {{-- Separador --}}
                    <div class="flex items-center gap-2 my-2">
                        <div class="flex-1 h-px bg-slate-200"></div>
                        <span class="text-xs text-slate-400 font-medium">o pega un enlace</span>
                        <div class="flex-1 h-px bg-slate-200"></div>
                    </div>

                    {{-- URL --}}
                    <input wire:model.blur="imagenUrl" type="url"
                           placeholder="https://ejemplo.com/imagen.jpg"
                           class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ $errors->has('imagenUrl') ? 'border-red-400' : '' }}">
                    @error('imagenUrl') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    @endif

                    @error('imagen') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror

                    <div wire:loading wire:target="imagen" class="mt-2 flex items-center gap-2 text-xs text-slate-500">
                        <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Subiendo imagen...
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Nombre *</label>
                        <input wire:model="nombre" type="text" placeholder="Nombre del producto"
                               class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                                      {{ $errors->has('nombre') ? 'border-red-400' : 'border-slate-300' }}">
                        @error('nombre') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">SKU</label>
                        <input wire:model="sku" type="text" placeholder="Ej: CPU-001"
                               class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Categoría *</label>
                        <select wire:model="categoria_id"
                                class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                                       {{ $errors->has('categoria_id') ? 'border-red-400' : 'border-slate-300' }}">
                            <option value="">Seleccionar...</option>
                            @foreach($categorias as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                        @error('categoria_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Descripción técnica</label>
                        <textarea wire:model="descripcion" rows="2" placeholder="Especificaciones técnicas, características..."
                                  class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Precio venta (Bs.) *</label>
                        <input wire:model="precio" type="number" step="0.01" min="0" placeholder="0.00"
                               class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                                      {{ $errors->has('precio') ? 'border-red-400' : 'border-slate-300' }}">
                        @error('precio') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Precio costo (Bs.)</label>
                        <input wire:model="precio_costo" type="number" step="0.01" min="0" placeholder="0.00"
                               class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('precio_costo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Estado</label>
                        <select wire:model="estado"
                                class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="Disponible">Disponible</option>
                            <option value="Agotado">Agotado</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Unidad de medida *</label>
                        <div class="flex gap-2">
                            <select wire:model="unidad_medida"
                                    class="flex-1 px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                                           {{ $errors->has('unidad_medida') ? 'border-red-400' : 'border-slate-300' }}">
                                <option value="">Seleccionar...</option>
                                @foreach($unidades as $u)
                                    <option value="{{ strtolower($u->nombre) }}">{{ $u->nombre }} ({{ $u->abreviatura }})</option>
                                @endforeach
                            </select>
                            <button type="button" wire:click="openUnidadCreate"
                                    class="px-2.5 py-2 bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded-lg text-slate-500 hover:text-indigo-600 transition"
                                    title="Gestionar unidades">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </button>
                        </div>
                        @error('unidad_medida') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Stock actual *</label>
                        <input wire:model="stock" type="number" min="0"
                               class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                                      {{ $errors->has('stock') ? 'border-red-400' : 'border-slate-300' }}">
                        @error('stock') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Stock mínimo *</label>
                        <input wire:model="stock_minimo" type="number" min="0"
                               class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <p class="mt-1 text-xs text-slate-400">Umbral para alerta de stock bajo</p>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-slate-100 flex justify-end gap-3">
                <button wire:click="$set('showModal', false)"
                        class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800 font-medium transition">
                    Cancelar
                </button>
                <button wire:click="save"
                        class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition flex items-center gap-2">
                    <span wire:loading.remove wire:target="save">{{ $editingId ? 'Actualizar' : 'Crear Producto' }}</span>
                    <span wire:loading wire:target="save" class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        Guardando...
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Delete Modal --}}
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40" wire:click="$set('showDeleteModal', false)"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-base font-semibold text-slate-800 mb-2">¿Eliminar producto?</h3>
            <p class="text-sm text-slate-500 mb-6">Se eliminará también la imagen asociada. Esta acción no se puede deshacer.</p>
            <div class="flex gap-3">
                <button wire:click="$set('showDeleteModal', false)"
                        class="flex-1 px-4 py-2 border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 transition">
                    Cancelar
                </button>
                <button wire:click="delete"
                        class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                    Sí, eliminar
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Unidades de Medida Modal --}}
    @if($showUnidadModal)
    <div class="fixed inset-0 flex items-center justify-center p-4" style="z-index: 60">
        <div class="absolute inset-0 bg-black/40" wire:click="$set('showUnidadModal', false)"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[85vh] flex flex-col">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-semibold text-slate-800">Unidades de Medida</h3>
                <button wire:click="$set('showUnidadModal', false)" class="text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="px-6 py-4 overflow-y-auto flex-1">
                @if(session('unidad_error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded-lg text-xs mb-3">
                    {{ session('unidad_error') }}
                </div>
                @endif

                {{-- Form --}}
                <div class="flex gap-2 mb-4">
                    <div class="flex-1">
                        <input wire:model="unidadNombre" type="text" placeholder="Nombre (ej: Pieza)"
                               class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('unidadNombre') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="w-20">
                        <input wire:model="unidadAbreviatura" type="text" placeholder="Abrev."
                               class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @error('unidadAbreviatura') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <button wire:click="saveUnidad"
                            class="px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition flex-shrink-0">
                        {{ $editingUnidadId ? 'Guardar' : 'Agregar' }}
                    </button>
                </div>

                {{-- List --}}
                <div class="space-y-1.5">
                    @foreach($unidades as $u)
                    <div class="flex items-center justify-between px-3 py-2.5 bg-slate-50 rounded-lg group">
                        <div>
                            <span class="text-sm font-medium text-slate-800">{{ $u->nombre }}</span>
                            <span class="text-xs text-slate-400 ml-1.5">({{ $u->abreviatura }})</span>
                        </div>
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition">
                            <button wire:click="openUnidadEdit({{ $u->id }})"
                                    class="p-1 text-slate-400 hover:text-indigo-600 rounded transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button wire:click="deleteUnidad({{ $u->id }})"
                                    wire:confirm="¿Eliminar esta unidad de medida?"
                                    class="p-1 text-slate-400 hover:text-red-600 rounded transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
