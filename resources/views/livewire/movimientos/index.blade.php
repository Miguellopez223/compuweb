<div class="space-y-4">
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">
        {{ session('success') }}
    </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Movimientos de Inventario</h2>
            <p class="text-sm text-slate-500">Registro de entradas de mercancía y salidas por ventas</p>
        </div>
        <button wire:click="openEntrada"
                class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Registrar Entrada
        </button>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 flex flex-wrap gap-3">
        <input wire:model.live.debounce.300ms="search" type="text"
               placeholder="Buscar por producto..."
               class="flex-1 min-w-52 px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <select wire:model.live="filtroTipo"
                class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Todos los tipos</option>
            <option value="entrada">Entradas</option>
            <option value="salida">Salidas</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Tipo</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Producto</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Cantidad</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Precio Unit.</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Proveedor / Referencia</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Registrado por</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Fecha</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($movimientos as $m)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-3.5">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold
                                {{ $m->tipo === 'entrada' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                                {{ $m->tipo === 'entrada' ? '↑ Entrada' : '↓ Salida' }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 font-medium text-slate-800">{{ $m->producto->nombre ?? '—' }}</td>
                        <td class="px-5 py-3.5 font-semibold {{ $m->tipo === 'entrada' ? 'text-emerald-600' : 'text-slate-600' }}">
                            {{ $m->tipo === 'entrada' ? '+' : '-' }}{{ $m->cantidad }}
                        </td>
                        <td class="px-5 py-3.5 text-slate-600">
                            {{ $m->precio_unitario ? 'Bs. '.number_format($m->precio_unitario, 2) : '—' }}
                        </td>
                        <td class="px-5 py-3.5">
                            @if($m->proveedor)
                                <p class="text-slate-700 font-medium">{{ $m->proveedor }}</p>
                            @endif
                            @if($m->referencia)
                                <p class="text-xs text-slate-400">Ref: {{ $m->referencia }}</p>
                            @endif
                            @if(!$m->proveedor && !$m->referencia)
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-slate-600">{{ $m->user->name ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-slate-500 text-xs">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-slate-400">
                            <p class="text-3xl mb-2">↔️</p>
                            <p class="font-medium">No hay movimientos registrados</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($movimientos->hasPages())
        <div class="px-5 py-3 border-t border-slate-100">{{ $movimientos->links() }}</div>
        @endif
    </div>

    {{-- Entrada Modal --}}
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40" wire:click="$set('showModal', false)"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="px-6 py-5 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <h3 class="text-base font-semibold text-slate-800">Registrar Entrada de Mercancía</h3>
                </div>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Producto *</label>
                    <select wire:model="producto_id"
                            class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                                   {{ $errors->has('producto_id') ? 'border-red-400' : 'border-slate-300' }}">
                        <option value="">Seleccionar producto...</option>
                        @foreach($productos as $p)
                            <option value="{{ $p->id }}">{{ $p->nombre }} (Stock: {{ $p->stock }})</option>
                        @endforeach
                    </select>
                    @error('producto_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Cantidad *</label>
                        <input wire:model="cantidad" type="number" min="1"
                               class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500
                                      {{ $errors->has('cantidad') ? 'border-red-400' : 'border-slate-300' }}">
                        @error('cantidad') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Precio Unitario (Bs.)</label>
                        <input wire:model="precio_unitario" type="number" step="0.01" min="0" placeholder="0.00"
                               class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Proveedor</label>
                    <input wire:model="proveedor" type="text" placeholder="Nombre del proveedor"
                           class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Referencia / Factura</label>
                    <input wire:model="referencia" type="text" placeholder="Nro. de factura o referencia"
                           class="w-full px-3 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 flex justify-end gap-3">
                <button wire:click="$set('showModal', false)"
                        class="px-4 py-2 text-sm text-slate-600 hover:text-slate-800 font-medium transition">
                    Cancelar
                </button>
                <button wire:click="registrarEntrada"
                        class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition">
                    Registrar Entrada
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
