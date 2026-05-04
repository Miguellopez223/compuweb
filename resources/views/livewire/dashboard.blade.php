<div class="space-y-6">

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Total Productos</p>
            <p class="text-3xl font-bold text-slate-800 mt-2">{{ $totalProductos }}</p>
            <p class="text-xs text-slate-400 mt-1">en inventario</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Movimientos</p>
            <p class="text-3xl font-bold text-slate-800 mt-2">{{ $totalMovimientos }}</p>
            <p class="text-xs text-slate-400 mt-1">entradas y salidas</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-red-100 p-5 {{ $sinStock > 0 ? 'bg-red-50' : '' }}">
            <p class="text-xs font-semibold {{ $sinStock > 0 ? 'text-red-500' : 'text-slate-500' }} uppercase tracking-wide">Sin Stock</p>
            <p class="text-3xl font-bold {{ $sinStock > 0 ? 'text-red-600' : 'text-slate-800' }} mt-2">{{ $sinStock }}</p>
            <p class="text-xs text-slate-400 mt-1">productos agotados</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-amber-100 p-5 {{ $stockBajo > 0 ? 'bg-amber-50' : '' }}">
            <p class="text-xs font-semibold {{ $stockBajo > 0 ? 'text-amber-500' : 'text-slate-500' }} uppercase tracking-wide">Stock Bajo</p>
            <p class="text-3xl font-bold {{ $stockBajo > 0 ? 'text-amber-600' : 'text-slate-800' }} mt-2">{{ $stockBajo }}</p>
            <p class="text-xs text-slate-400 mt-1">por reponer</p>
        </div>
    </div>

    {{-- Ventas hoy --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="bg-indigo-600 rounded-xl p-5 text-white">
            <p class="text-xs font-semibold text-indigo-200 uppercase tracking-wide">Ventas de Hoy</p>
            <p class="text-3xl font-bold mt-2">{{ $ventasHoy }}</p>
            <p class="text-sm text-indigo-200 mt-1">transacciones completadas</p>
        </div>
        <div class="bg-emerald-600 rounded-xl p-5 text-white">
            <p class="text-xs font-semibold text-emerald-200 uppercase tracking-wide">Ingresos de Hoy</p>
            <p class="text-3xl font-bold mt-2">Bs. {{ number_format($ingresosHoy, 2) }}</p>
            <p class="text-sm text-emerald-200 mt-1">en ventas confirmadas</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Alertas de stock --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-slate-800 text-sm">⚠️ Alertas de Stock</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Productos que necesitan reposición</p>
                </div>
                <a href="{{ route('productos') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Ver todos →</a>
            </div>
            <div class="p-4">
                @forelse($alertas as $p)
                <div class="flex items-center justify-between py-2.5 border-b border-slate-50 last:border-0">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-800 truncate">{{ $p->nombre }}</p>
                        <p class="text-xs text-slate-400">{{ $p->categoria->nombre ?? '—' }}</p>
                    </div>
                    <div class="flex items-center gap-3 ml-3">
                        <span class="text-sm font-semibold {{ $p->stock === 0 ? 'text-red-600' : 'text-amber-600' }}">
                            {{ $p->stock }} uds
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $p->estado === 'Agotado' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $p->estado === 'Agotado' ? 'Agotado' : 'Stock bajo' }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <p class="text-2xl mb-2">✅</p>
                    <p class="text-sm text-slate-500 font-medium">Inventario en buen estado</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Últimas ventas --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-slate-800 text-sm">🧾 Últimas Ventas</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Transacciones más recientes</p>
                </div>
                <a href="{{ route('ventas') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Ver todas →</a>
            </div>
            <div class="p-4">
                @forelse($ultimasVentas as $v)
                <div class="flex items-center justify-between py-2.5 border-b border-slate-50 last:border-0">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-800 truncate">{{ $v->cliente_nombre }}</p>
                        <p class="text-xs text-slate-400">{{ $v->vendedor->name ?? '—' }} · {{ $v->created_at->format('d/m H:i') }}</p>
                    </div>
                    <span class="text-sm font-semibold text-emerald-600 ml-3">Bs. {{ number_format($v->total, 2) }}</span>
                </div>
                @empty
                <div class="text-center py-8">
                    <p class="text-2xl mb-2">🛒</p>
                    <p class="text-sm text-slate-500 font-medium">Sin ventas registradas</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
