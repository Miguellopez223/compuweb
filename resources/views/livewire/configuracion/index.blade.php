<div class="max-w-2xl space-y-6">

    @if(session('success'))
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg px-4 py-3 text-sm">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- URL del catálogo --}}
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            </div>
            <div>
                <h2 class="text-base font-semibold text-slate-800">URL del Catálogo Público</h2>
                <p class="text-xs text-slate-500">Comparte este enlace con tus clientes para que vean tu catálogo</p>
            </div>
        </div>

        @php $catalogUrl = url('/catalogo/' . (auth()->user()->tienda->slug ?? '')); @endphp

        <div x-data="{ copied: false }"
             class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-lg px-4 py-3">
            <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
            <span class="flex-1 text-sm text-slate-700 font-mono break-all">{{ $catalogUrl }}</span>
            <button @click="navigator.clipboard.writeText('{{ $catalogUrl }}'); copied = true; setTimeout(() => copied = false, 2000)"
                    class="flex-shrink-0 flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-medium transition"
                    :class="copied ? 'bg-emerald-100 text-emerald-700' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-100'">
                <svg x-show="!copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                <svg x-show="copied" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span x-text="copied ? 'Copiado' : 'Copiar'"></span>
            </button>
        </div>

        <div class="mt-3 flex items-center gap-2">
            <a href="{{ $catalogUrl }}" target="_blank"
               class="inline-flex items-center gap-1.5 text-xs text-indigo-600 hover:text-indigo-800 font-medium transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Abrir en nueva pestaña
            </a>
        </div>
    </div>

    {{-- Editar slug --}}
    <div class="bg-white border border-slate-200 rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-slate-800">Slug de la URL</h2>
                    <p class="text-xs text-slate-500">Identificador único de tu tienda en la URL</p>
                </div>
            </div>
            @if(!$editingSlug)
            <button wire:click="$set('editingSlug', true)"
                    class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Editar
            </button>
            @endif
        </div>

        @if(!$editingSlug)
        <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-lg px-4 py-3">
            <span class="text-sm text-slate-500 font-mono">/catalogo/</span>
            <span class="text-sm font-semibold text-slate-800 font-mono">{{ auth()->user()->tienda->slug }}</span>
        </div>
        @else
        <form wire:submit="saveSlug" class="space-y-4">
            <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg flex gap-2">
                <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <p class="text-xs text-amber-700">
                    <strong>Atención:</strong> cambiar el slug modifica la URL del catálogo. Los links compartidos anteriormente dejarán de funcionar.
                </p>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Nuevo slug</label>
                <div class="flex items-center gap-0">
                    <span class="px-3 py-2 bg-slate-100 border border-r-0 border-slate-300 rounded-l-lg text-xs text-slate-500 font-mono">/catalogo/</span>
                    <input wire:model="slug" type="text"
                           placeholder="mi-tienda"
                           class="flex-1 border border-slate-300 rounded-r-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('slug') border-red-400 @enderror">
                </div>
                @error('slug')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-slate-400 mt-1">Solo letras minúsculas, números y guiones. Ej: <code class="bg-slate-100 px-1 rounded">mi-tienda-2024</code></p>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                    Guardar
                </button>
                <button type="button" wire:click="cancelEdit"
                        class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 text-sm font-medium rounded-lg transition">
                    Cancelar
                </button>
            </div>
        </form>
        @endif
    </div>

</div>
