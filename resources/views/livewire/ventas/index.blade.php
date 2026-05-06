<div class="space-y-4">
    @if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">
        {{ session('success') }}
    </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Historial de Ventas</h2>
            <p class="text-sm text-slate-500">Consulta y gestiona todas las transacciones</p>
        </div>
        <a href="{{ route('ventas.registrar') }}"
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Registrar Venta
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 flex flex-wrap gap-3">
        <input wire:model.live.debounce.300ms="search" type="text"
               placeholder="Buscar por cliente..."
               class="flex-1 min-w-52 px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <input wire:model.live="filtroFecha" type="date"
               class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <select wire:model.live="filtroEstado"
                class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Todos los estados</option>
            <option value="Completada">Completada</option>
            <option value="Anulada">Anulada</option>
        </select>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">#</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Cliente</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Vendedor</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Total</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Pago</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Estado</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Fecha</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wide">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($ventas as $v)
                    <tr class="hover:bg-slate-50 transition {{ $v->estado_venta === 'Anulada' ? 'opacity-60' : '' }}">
                        <td class="px-5 py-3.5 text-slate-400 font-mono text-xs">#{{ $v->id }}</td>
                        <td class="px-5 py-3.5">
                            <p class="font-medium text-slate-800">{{ $v->cliente_nombre }}</p>
                            @if($v->cliente_telefono)
                                <p class="text-xs text-slate-400">{{ $v->cliente_telefono }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-slate-600">{{ $v->vendedor->name ?? '—' }}</td>
                        <td class="px-5 py-3.5 font-bold text-slate-800">Bs. {{ number_format($v->total, 2) }}</td>
                        <td class="px-5 py-3.5">
                            <span class="px-2 py-1 bg-slate-100 text-slate-700 rounded text-xs font-medium capitalize">
                                {{ $v->metodo_pago }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                {{ $v->estado_venta === 'Completada' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                {{ $v->estado_venta }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-slate-500 text-xs">{{ $v->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-5 py-3.5 text-right space-x-2">
                            <a href="{{ route('ventas.recibo', $v->id) }}" target="_blank"
                               class="text-xs text-slate-500 hover:text-slate-700 font-medium transition inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                Recibo
                            </a>
                            @if($v->estado_venta === 'Completada')
                            <button wire:click="confirmCancel({{ $v->id }})"
                                    class="text-xs text-red-500 hover:text-red-700 font-medium transition">
                                Anular
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center text-slate-400">
                            <p class="text-3xl mb-2">🧾</p>
                            <p class="font-medium">No hay ventas registradas</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($ventas->hasPages())
        <div class="px-5 py-3 border-t border-slate-100">{{ $ventas->links() }}</div>
        @endif
    </div>

    {{-- Cancel Modal --}}
    @if($showCancelModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/40" wire:click="$set('showCancelModal', false)"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
            <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-base font-semibold text-slate-800 mb-2">¿Anular esta venta?</h3>
            <p class="text-sm text-slate-500 mb-6">El stock de todos los productos será reintegrado automáticamente.</p>
            <div class="flex gap-3">
                <button wire:click="$set('showCancelModal', false)"
                        class="flex-1 px-4 py-2 border border-slate-300 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-50 transition">
                    Cancelar
                </button>
                <button wire:click="cancelar"
                        class="flex-1 px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition">
                    Sí, anular
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
