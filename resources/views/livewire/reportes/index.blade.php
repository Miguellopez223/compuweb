<div class="space-y-4" x-data="reportesCharts()" x-effect="initCharts()">

    {{-- Tab Navigation --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
        <nav class="flex overflow-x-auto">
            @foreach([
                'dashboard' => ['Dashboard', 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                'ventas' => ['Rendimiento Ventas', 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                'productos' => ['Inteligencia Productos', 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                'vendedores' => ['Vendedores', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                'movimientos' => ['Movimientos', 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4'],
            ] as $tab => [$label, $icon])
                <button wire:click="$set('activeTab', '{{ $tab }}')"
                        class="flex items-center gap-2 px-5 py-3.5 text-sm font-medium whitespace-nowrap border-b-2 transition
                               {{ $activeTab === $tab ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                    </svg>
                    {{ $label }}
                </button>
            @endforeach
        </nav>
    </div>

    {{-- ============================================== --}}
    {{-- TAB: DASHBOARD --}}
    {{-- ============================================== --}}
    @if($activeTab === 'dashboard')
    <div class="space-y-4">
        {{-- Period selector --}}
        <div class="flex items-center gap-3">
            <label class="text-sm font-medium text-slate-600">Periodo:</label>
            <select wire:model.live="dashPeriodo" class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="7">Últimos 7 días</option>
                <option value="14">Últimos 14 días</option>
                <option value="30">Últimos 30 días</option>
            </select>
        </div>

        {{-- Week comparison cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Semana Actual</p>
                <p class="text-2xl font-bold text-slate-800 mt-1">Bs. {{ number_format($ventasSemanaActual, 2) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Semana Anterior</p>
                <p class="text-2xl font-bold text-slate-800 mt-1">Bs. {{ number_format($ventasSemanaAnterior, 2) }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border p-5
                        {{ $cambioSemanal >= 0 ? 'border-emerald-200 bg-emerald-50' : 'border-red-200 bg-red-50' }}">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Variación</p>
                <p class="text-2xl font-bold mt-1 {{ $cambioSemanal >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                    {{ $cambioSemanal >= 0 ? '+' : '' }}{{ number_format($cambioSemanal, 1) }}%
                </p>
            </div>
        </div>

        {{-- Charts row --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- Line chart: Ventas en el tiempo --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                <h3 class="text-sm font-semibold text-slate-800 mb-4">Ingresos por Día</h3>
                <div style="height: 280px;">
                    <canvas id="chartVentasTiempo"></canvas>
                </div>
            </div>

            {{-- Pie chart: Ventas por categoría --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                <h3 class="text-sm font-semibold text-slate-800 mb-4">Ventas por Categoría</h3>
                <div style="height: 280px;">
                    <canvas id="chartCategorias"></canvas>
                </div>
            </div>
        </div>

        {{-- Heat map: Ventas por hora --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
            <h3 class="text-sm font-semibold text-slate-800 mb-4">Mapa de Calor — Ventas por Hora del Día</h3>
            <div class="grid grid-cols-12 gap-1">
                @php
                    $maxHora = max($horasData) ?: 1;
                @endphp
                @for($h = 0; $h < 24; $h++)
                    @php
                        $intensidad = $horasData[$h] / $maxHora;
                        if ($intensidad === 0) $bgClass = 'bg-slate-100';
                        elseif ($intensidad < 0.25) $bgClass = 'bg-indigo-100';
                        elseif ($intensidad < 0.5) $bgClass = 'bg-indigo-200';
                        elseif ($intensidad < 0.75) $bgClass = 'bg-indigo-400 text-white';
                        else $bgClass = 'bg-indigo-600 text-white';
                    @endphp
                    <div class="rounded-lg p-2 text-center {{ $bgClass }}" title="{{ $horasData[$h] }} ventas a las {{ str_pad($h, 2, '0', STR_PAD_LEFT) }}:00">
                        <p class="text-xs font-bold">{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}</p>
                        <p class="text-xs">{{ $horasData[$h] }}</p>
                    </div>
                @endfor
            </div>
            <p class="text-xs text-slate-400 mt-3 text-center">Hora del día (00–23) — más oscuro = más ventas</p>
        </div>
    </div>

    {{-- Chart.js data injection --}}
    <div x-ref="dashboardData" class="hidden"
         data-labels='@json($labels)'
         data-ingresos='@json($dataIngresos)'
         data-ventas='@json($dataVentas)'
         data-cat-labels='@json($ventasPorCategoria->pluck("nombre"))'
         data-cat-values='@json($ventasPorCategoria->pluck("total"))'>
    </div>
    @endif

    {{-- ============================================== --}}
    {{-- TAB: RENDIMIENTO VENTAS --}}
    {{-- ============================================== --}}
    @if($activeTab === 'ventas')
    <div class="space-y-4">
        {{-- Date filters --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Desde</label>
                <input wire:model.live="vtaFechaDesde" type="date" class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Hasta</label>
                <input wire:model.live="vtaFechaHasta" type="date" class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Total Ventas</p>
                <p class="text-2xl font-bold text-slate-800 mt-1">{{ $totalVentas }}</p>
                <p class="text-xs text-slate-400 mt-0.5">transacciones</p>
            </div>
            <div class="bg-indigo-600 rounded-xl p-5 text-white">
                <p class="text-xs font-semibold text-indigo-200 uppercase tracking-wide">Ingresos Brutos</p>
                <p class="text-2xl font-bold mt-1">Bs. {{ number_format($ingresosBrutos, 2) }}</p>
            </div>
            <div class="bg-emerald-600 rounded-xl p-5 text-white">
                <p class="text-xs font-semibold text-emerald-200 uppercase tracking-wide">Utilidad Bruta</p>
                <p class="text-2xl font-bold mt-1">Bs. {{ number_format($utilidadBruta, 2) }}</p>
                <p class="text-xs text-emerald-200 mt-0.5">Margen: {{ number_format($margenPorcentaje, 1) }}%</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Ticket Promedio</p>
                <p class="text-2xl font-bold text-slate-800 mt-1">Bs. {{ number_format($ticketPromedio, 2) }}</p>
                <p class="text-xs text-slate-400 mt-0.5">por transacción</p>
            </div>
        </div>

        {{-- Revenue breakdown --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- Ingresos vs Costo vs Utilidad --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                <h3 class="text-sm font-semibold text-slate-800 mb-4">Desglose Financiero</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600">Ingresos por Ventas</span>
                        <span class="text-sm font-semibold text-slate-800">Bs. {{ number_format($ingresosBrutos, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600">(-) Costo de Ventas</span>
                        <span class="text-sm font-semibold text-red-600">Bs. {{ number_format($costoVentas, 2) }}</span>
                    </div>
                    <div class="border-t border-slate-200 pt-3 flex justify-between items-center">
                        <span class="text-sm font-semibold text-slate-800">(=) Utilidad Bruta</span>
                        <span class="text-sm font-bold text-emerald-600">Bs. {{ number_format($utilidadBruta, 2) }}</span>
                    </div>
                    @if($ingresosBrutos > 0)
                    <div class="mt-2">
                        <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full" style="width: {{ min($margenPorcentaje, 100) }}%"></div>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Margen de utilidad: {{ number_format($margenPorcentaje, 1) }}%</p>
                    </div>
                    @endif
                </div>
                @if($costoVentas == 0 && $ingresosBrutos > 0)
                <div class="mt-4 bg-amber-50 border border-amber-200 rounded-lg p-3">
                    <p class="text-xs text-amber-700">Los precios de costo de los productos están en Bs. 0. Actualiza el campo "Precio Costo" en cada producto para ver la utilidad real.</p>
                </div>
                @endif
            </div>

            {{-- Payment methods ranking --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                <h3 class="text-sm font-semibold text-slate-800 mb-4">Ranking Métodos de Pago</h3>
                @forelse($porMetodoPago as $mp)
                <div class="flex items-center justify-between py-2.5 border-b border-slate-50 last:border-0">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold
                            {{ $loop->first ? 'bg-indigo-100 text-indigo-700' : 'bg-slate-100 text-slate-600' }}">
                            {{ $loop->iteration }}
                        </span>
                        <div>
                            <p class="text-sm font-medium text-slate-800 capitalize">{{ $mp->metodo_pago }}</p>
                            <p class="text-xs text-slate-400">{{ $mp->total_ventas }} ventas</p>
                        </div>
                    </div>
                    <span class="text-sm font-semibold text-slate-800">Bs. {{ number_format($mp->ingresos, 2) }}</span>
                </div>
                @empty
                <p class="text-sm text-slate-400 text-center py-6">Sin datos para el periodo seleccionado</p>
                @endforelse
            </div>
        </div>
    </div>
    @endif

    {{-- ============================================== --}}
    {{-- TAB: INTELIGENCIA PRODUCTOS --}}
    {{-- ============================================== --}}
    @if($activeTab === 'productos')
    <div class="space-y-4">
        {{-- Inventory valuation --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-indigo-600 rounded-xl p-5 text-white">
                <p class="text-xs font-semibold text-indigo-200 uppercase tracking-wide">Valor Inventario (Costo)</p>
                <p class="text-2xl font-bold mt-1">Bs. {{ number_format($valorizacion->valor_costo ?? 0, 2) }}</p>
                <p class="text-xs text-indigo-200 mt-0.5">capital invertido en estanterías</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Valor Inventario (Venta)</p>
                <p class="text-2xl font-bold text-slate-800 mt-1">Bs. {{ number_format($valorizacion->valor_venta ?? 0, 2) }}</p>
                <p class="text-xs text-slate-400 mt-0.5">potencial de venta</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Total Productos</p>
                <p class="text-2xl font-bold text-slate-800 mt-1">{{ $valorizacion->total_productos ?? 0 }}</p>
                <p class="text-xs text-slate-400 mt-0.5">SKUs en catálogo</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- Best Sellers --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h3 class="font-semibold text-slate-800 text-sm">Top 10 — Productos Estrella</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Mayor rotación de ventas (todo el historial)</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500">#</th>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500">Producto</th>
                                <th class="text-right px-4 py-2.5 text-xs font-semibold text-slate-500">Vendidos</th>
                                <th class="text-right px-4 py-2.5 text-xs font-semibold text-slate-500">Ingresos</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($bestSellers as $bs)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-2.5">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold
                                        {{ $loop->iteration <= 3 ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $loop->iteration }}
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 font-medium text-slate-800">{{ $bs->nombre }}</td>
                                <td class="px-4 py-2.5 text-right font-semibold text-slate-700">{{ $bs->total_vendido }}</td>
                                <td class="px-4 py-2.5 text-right text-emerald-600 font-semibold">Bs. {{ number_format($bs->ingresos, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-4 py-8 text-center text-slate-400">Sin datos de ventas</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Dead Stock --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-slate-800 text-sm">Productos Muertos (Sin Rotación)</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Sin movimiento en los últimos {{ $diasSinMovimiento }} días</p>
                    </div>
                    <select wire:model.live="diasSinMovimiento" class="px-3 py-1.5 border border-slate-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="30">30 días</option>
                        <option value="60">60 días</option>
                        <option value="90">90 días</option>
                    </select>
                </div>
                <div class="overflow-x-auto max-h-96 overflow-y-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b border-slate-200 sticky top-0">
                            <tr>
                                <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500">Producto</th>
                                <th class="text-right px-4 py-2.5 text-xs font-semibold text-slate-500">Stock</th>
                                <th class="text-right px-4 py-2.5 text-xs font-semibold text-slate-500">Capital Retenido</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($productosMuertos as $pm)
                            <tr class="hover:bg-red-50/50">
                                <td class="px-4 py-2.5 font-medium text-slate-800">{{ $pm->nombre }}</td>
                                <td class="px-4 py-2.5 text-right text-slate-600">{{ $pm->stock }} uds</td>
                                <td class="px-4 py-2.5 text-right text-red-600 font-semibold">Bs. {{ number_format($pm->stock * $pm->precio_costo, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="px-4 py-8 text-center text-emerald-500 font-medium">Todos los productos tienen rotación reciente</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($productosMuertos->isNotEmpty())
                <div class="px-5 py-3 border-t border-slate-100 bg-red-50">
                    <p class="text-xs text-red-700 font-medium">
                        {{ $productosMuertos->count() }} productos sin rotación — Capital retenido: Bs. {{ number_format($productosMuertos->sum(fn($p) => $p->stock * $p->precio_costo), 2) }}
                    </p>
                </div>
                @endif
            </div>
        </div>

        {{-- Critical Stock --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800 text-sm">Stock Crítico — Pedidos a Proveedores</h3>
                <p class="text-xs text-slate-400 mt-0.5">Productos bajo nivel mínimo o agotados</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500">Producto</th>
                            <th class="text-left px-4 py-2.5 text-xs font-semibold text-slate-500">Categoría</th>
                            <th class="text-right px-4 py-2.5 text-xs font-semibold text-slate-500">Stock Actual</th>
                            <th class="text-right px-4 py-2.5 text-xs font-semibold text-slate-500">Stock Mínimo</th>
                            <th class="text-center px-4 py-2.5 text-xs font-semibold text-slate-500">Estado</th>
                            <th class="text-right px-4 py-2.5 text-xs font-semibold text-slate-500">Pedir (sugerido)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($stockCritico as $sc)
                        <tr class="hover:bg-amber-50/50">
                            <td class="px-4 py-2.5 font-medium text-slate-800">{{ $sc->nombre }}</td>
                            <td class="px-4 py-2.5 text-slate-600">{{ $sc->categoria->nombre ?? '—' }}</td>
                            <td class="px-4 py-2.5 text-right font-semibold {{ $sc->stock === 0 ? 'text-red-600' : 'text-amber-600' }}">{{ $sc->stock }}</td>
                            <td class="px-4 py-2.5 text-right text-slate-500">{{ $sc->stock_minimo }}</td>
                            <td class="px-4 py-2.5 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $sc->stock === 0 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $sc->stock === 0 ? 'Agotado' : 'Stock bajo' }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 text-right font-semibold text-indigo-600">
                                {{ max(0, ($sc->stock_minimo * 2) - $sc->stock) }} uds
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-emerald-500 font-medium">Inventario en buen estado</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- ============================================== --}}
    {{-- TAB: VENDEDORES --}}
    {{-- ============================================== --}}
    @if($activeTab === 'vendedores')
    <div class="space-y-4">
        {{-- Date filters (shared with ventas) --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Desde</label>
                <input wire:model.live="vtaFechaDesde" type="date" class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Hasta</label>
                <input wire:model.live="vtaFechaHasta" type="date" class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
        </div>

        {{-- Sellers ranking --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-slate-800 text-sm">Ranking de Vendedores</h3>
                <p class="text-xs text-slate-400 mt-0.5">Rendimiento por volumen de ventas generado</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500">#</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500">Vendedor</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500">Rol</th>
                            <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500">Ventas</th>
                            <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500">Ingresos</th>
                            <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500">% del Total</th>
                            <th class="px-5 py-3 text-xs font-semibold text-slate-500">Participación</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($vendedores as $v)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold
                                    {{ $loop->iteration === 1 ? 'bg-amber-400 text-white' : ($loop->iteration === 2 ? 'bg-slate-300 text-slate-700' : ($loop->iteration === 3 ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-600')) }}">
                                    {{ $loop->iteration }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                        <span class="text-xs font-bold text-indigo-600">{{ strtoupper(substr($v->name, 0, 2)) }}</span>
                                    </div>
                                    <span class="font-medium text-slate-800">{{ $v->name }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500 capitalize">{{ $v->role }}</td>
                            <td class="px-5 py-3.5 text-right font-semibold text-slate-700">{{ $v->ventas_completadas }}</td>
                            <td class="px-5 py-3.5 text-right font-semibold text-emerald-600">Bs. {{ number_format($v->ingresos_generados ?? 0, 2) }}</td>
                            <td class="px-5 py-3.5 text-right text-slate-600">
                                {{ $totalIngresos > 0 ? number_format(($v->ingresos_generados / $totalIngresos) * 100, 1) : 0 }}%
                            </td>
                            <td class="px-5 py-3.5">
                                @if($totalIngresos > 0)
                                <div class="w-full bg-slate-100 rounded-full h-2">
                                    <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ ($v->ingresos_generados / $totalIngresos) * 100 }}%"></div>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="px-5 py-8 text-center text-slate-400">Sin vendedores registrados</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    {{-- ============================================== --}}
    {{-- TAB: MOVIMIENTOS --}}
    {{-- ============================================== --}}
    @if($activeTab === 'movimientos')
    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Libro de Movimientos</h2>
                <p class="text-sm text-slate-500">Auditoría completa de entradas, salidas y ajustes</p>
            </div>
            <button wire:click="exportarMovimientosPdf"
                    class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Exportar PDF
            </button>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Desde</label>
                <input wire:model.live="movFechaDesde" type="date" class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Hasta</label>
                <input wire:model.live="movFechaHasta" type="date" class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Tipo</label>
                <select wire:model.live="movTipo" class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Todos</option>
                    <option value="entrada">Entrada</option>
                    <option value="salida">Salida</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Motivo</label>
                <select wire:model.live="movMotivo" class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Todos</option>
                    <option value="compra">Compra</option>
                    <option value="venta">Venta</option>
                    <option value="ajuste_positivo">Ajuste (+)</option>
                    <option value="ajuste_negativo">Ajuste (-)</option>
                    <option value="devolucion">Devolución</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Usuario</label>
                <select wire:model.live="movUsuario" class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Todos</option>
                    @foreach($usuarios as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Fecha</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Tipo</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Motivo</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Producto</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Cantidad</th>
                            <th class="text-right px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Precio Unit.</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Proveedor / Ref.</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Registrado por</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($movimientos as $m)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 text-slate-500 text-xs">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ $m->tipo === 'entrada' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $m->tipo === 'entrada' ? '+ Entrada' : '- Salida' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-600 text-xs capitalize">{{ $m->motivo ?? '—' }}</td>
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $m->producto->nombre ?? '—' }}</td>
                            <td class="px-4 py-3 text-right font-semibold {{ $m->tipo === 'entrada' ? 'text-emerald-600' : 'text-slate-600' }}">
                                {{ $m->tipo === 'entrada' ? '+' : '-' }}{{ $m->cantidad }}
                            </td>
                            <td class="px-4 py-3 text-right text-slate-600">
                                {{ $m->precio_unitario ? 'Bs. '.number_format($m->precio_unitario, 2) : '—' }}
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500">
                                {{ $m->proveedor ?: '' }}
                                @if($m->referencia) <span class="text-slate-400">{{ $m->proveedor ? ' · ' : '' }}{{ $m->referencia }}</span> @endif
                                @if(!$m->proveedor && !$m->referencia) — @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600 text-xs">{{ $m->user->name ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-slate-400">
                                <p class="font-medium">No hay movimientos para los filtros seleccionados</p>
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
    </div>
    @endif

    {{-- Chart.js CDN --}}
    @if($activeTab === 'dashboard')
    @script
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    @endscript
    @endif

</div>

@if($activeTab === 'dashboard')
@script
<script>
    function reportesCharts() {
        return {
            charts: {},
            initCharts() {
                this.$nextTick(() => {
                    const dataEl = this.$refs.dashboardData;
                    if (!dataEl) return;

                    // Destroy existing charts
                    Object.values(this.charts).forEach(c => c.destroy());
                    this.charts = {};

                    const labels = JSON.parse(dataEl.dataset.labels || '[]');
                    const ingresos = JSON.parse(dataEl.dataset.ingresos || '[]');
                    const catLabels = JSON.parse(dataEl.dataset.catLabels || '[]');
                    const catValues = JSON.parse(dataEl.dataset.catValues || '[]');

                    // Line chart
                    const ctxLine = document.getElementById('chartVentasTiempo');
                    if (ctxLine && typeof Chart !== 'undefined') {
                        this.charts.line = new Chart(ctxLine, {
                            type: 'line',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Ingresos (Bs.)',
                                    data: ingresos,
                                    borderColor: '#4f46e5',
                                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                                    fill: true,
                                    tension: 0.3,
                                    pointRadius: 4,
                                    pointBackgroundColor: '#4f46e5',
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: {
                                    y: { beginAtZero: true, ticks: { callback: v => 'Bs. ' + v.toLocaleString() } }
                                }
                            }
                        });
                    }

                    // Pie chart
                    const ctxPie = document.getElementById('chartCategorias');
                    if (ctxPie && typeof Chart !== 'undefined' && catLabels.length > 0) {
                        const colors = ['#4f46e5', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16', '#f97316', '#6366f1'];
                        this.charts.pie = new Chart(ctxPie, {
                            type: 'doughnut',
                            data: {
                                labels: catLabels,
                                datasets: [{
                                    data: catValues,
                                    backgroundColor: colors.slice(0, catLabels.length),
                                    borderWidth: 2,
                                    borderColor: '#fff',
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { position: 'right', labels: { boxWidth: 12, padding: 12, font: { size: 11 } } }
                                }
                            }
                        });
                    }
                });
            }
        };
    }
</script>
@endscript
@else
@script
<script>
    function reportesCharts() {
        return { charts: {}, initCharts() {} };
    }
</script>
@endscript
@endif
