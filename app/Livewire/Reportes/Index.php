<?php

namespace App\Livewire\Reportes;

use App\Models\Cliente;
use App\Models\DetalleVenta;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Reportes')]
class Index extends Component
{
    use WithPagination;

    public string $activeTab = 'dashboard';

    // Movimientos filters
    public string $movFechaDesde = '';
    public string $movFechaHasta = '';
    public string $movTipo = '';
    public string $movUsuario = '';

    // Ventas filters
    public string $vtaFechaDesde = '';
    public string $vtaFechaHasta = '';

    // Productos filters
    public int $diasSinMovimiento = 30;

    // Clientes filters
    public int $diasClienteInactivo = 30;

    // Dashboard filters
    public string $dashPeriodo = '7';

    public function mount(): void
    {
        $this->vtaFechaDesde = now()->startOfMonth()->format('Y-m-d');
        $this->vtaFechaHasta = now()->format('Y-m-d');
        $this->movFechaDesde = now()->startOfMonth()->format('Y-m-d');
        $this->movFechaHasta = now()->format('Y-m-d');
    }

    public function updatingActiveTab(): void
    {
        $this->resetPage();
    }

    public function updatingMovFechaDesde(): void { $this->resetPage(); }
    public function updatingMovFechaHasta(): void { $this->resetPage(); }
    public function updatingMovTipo(): void { $this->resetPage(); }
    public function updatingMovUsuario(): void { $this->resetPage(); }

    public function exportarMovimientosPdf()
    {
        $movimientos = $this->getMovimientosQuery()->get();
        $filtros = [
            'fecha_desde' => $this->movFechaDesde,
            'fecha_hasta' => $this->movFechaHasta,
            'tipo' => $this->movTipo,
            'usuario' => $this->movUsuario,
        ];
        $tienda = auth()->user()->tienda;

        $pdf = Pdf::loadView('pdf.reporte-movimientos', compact('movimientos', 'filtros', 'tienda'))
            ->setPaper('letter', 'landscape');

        return response()->streamDownload(
            fn() => print($pdf->output()),
            'movimientos-' . now()->format('Y-m-d') . '.pdf'
        );
    }

    private function getMovimientosQuery()
    {
        return MovimientoInventario::with(['producto', 'user'])
            ->when($this->movFechaDesde, fn($q) => $q->whereDate('created_at', '>=', $this->movFechaDesde))
            ->when($this->movFechaHasta, fn($q) => $q->whereDate('created_at', '<=', $this->movFechaHasta))
            ->when($this->movTipo, fn($q) => $q->where('tipo', $this->movTipo))
            ->when($this->movUsuario, fn($q) => $q->where('user_id', $this->movUsuario))
            ->latest();
    }

    private function getVentasData(): array
    {
        $query = Venta::where('estado_venta', 'Completada')
            ->when($this->vtaFechaDesde, fn($q) => $q->whereDate('created_at', '>=', $this->vtaFechaDesde))
            ->when($this->vtaFechaHasta, fn($q) => $q->whereDate('created_at', '<=', $this->vtaFechaHasta));

        $totalVentas = (clone $query)->count();
        $ingresosBrutos = (clone $query)->sum('total');

        $costoVentas = DetalleVenta::whereHas('venta', function ($q) {
                $q->where('estado_venta', 'Completada')
                    ->when($this->vtaFechaDesde, fn($q2) => $q2->whereDate('created_at', '>=', $this->vtaFechaDesde))
                    ->when($this->vtaFechaHasta, fn($q2) => $q2->whereDate('created_at', '<=', $this->vtaFechaHasta));
            })
            ->join('productos', 'detalle_ventas.producto_id', '=', 'productos.id')
            ->sum(DB::raw('detalle_ventas.cantidad * productos.precio_costo'));

        $utilidadBruta = $ingresosBrutos - $costoVentas;
        $ticketPromedio = $totalVentas > 0 ? $ingresosBrutos / $totalVentas : 0;
        $margenPorcentaje = $ingresosBrutos > 0 ? ($utilidadBruta / $ingresosBrutos) * 100 : 0;

        $porMetodoPago = (clone $query)
            ->select('metodo_pago', DB::raw('COUNT(*) as total_ventas'), DB::raw('SUM(total) as ingresos'))
            ->groupBy('metodo_pago')
            ->orderByDesc('ingresos')
            ->get();

        // Anulaciones en el periodo
        $anuladas = Venta::where('estado_venta', 'Anulada')
            ->when($this->vtaFechaDesde, fn($q) => $q->whereDate('created_at', '>=', $this->vtaFechaDesde))
            ->when($this->vtaFechaHasta, fn($q) => $q->whereDate('created_at', '<=', $this->vtaFechaHasta));
        $numAnuladas = (clone $anuladas)->count();
        $montoAnulado = (clone $anuladas)->sum('total');

        // Unidades vendidas e items promedio por venta
        $unidadesVendidas = DetalleVenta::whereHas('venta', function ($q) {
                $q->where('estado_venta', 'Completada')
                    ->when($this->vtaFechaDesde, fn($q2) => $q2->whereDate('created_at', '>=', $this->vtaFechaDesde))
                    ->when($this->vtaFechaHasta, fn($q2) => $q2->whereDate('created_at', '<=', $this->vtaFechaHasta));
            })->sum('cantidad');
        $unidadesPromedio = $totalVentas > 0 ? $unidadesVendidas / $totalVentas : 0;

        // Comparativa mes actual vs mes anterior
        $inicioMesActual = now()->startOfMonth();
        $inicioMesAnterior = now()->subMonthNoOverflow()->startOfMonth();
        $finMesAnterior = now()->subMonthNoOverflow()->endOfMonth();
        $ventasMesActual = Venta::where('estado_venta', 'Completada')
            ->where('created_at', '>=', $inicioMesActual)->sum('total');
        $ventasMesAnterior = Venta::where('estado_venta', 'Completada')
            ->whereBetween('created_at', [$inicioMesAnterior, $finMesAnterior])->sum('total');
        $cambioMensual = $ventasMesAnterior > 0
            ? (($ventasMesActual - $ventasMesAnterior) / $ventasMesAnterior) * 100
            : ($ventasMesActual > 0 ? 100 : 0);

        return compact(
            'totalVentas', 'ingresosBrutos', 'costoVentas', 'utilidadBruta', 'ticketPromedio',
            'margenPorcentaje', 'porMetodoPago', 'numAnuladas', 'montoAnulado',
            'unidadesVendidas', 'unidadesPromedio',
            'ventasMesActual', 'ventasMesAnterior', 'cambioMensual'
        );
    }

    private function getProductosData(): array
    {
        $tiendaId = auth()->user()->tienda_id;

        $bestSellers = DetalleVenta::join('ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
            ->join('productos', 'detalle_ventas.producto_id', '=', 'productos.id')
            ->where('ventas.tienda_id', $tiendaId)
            ->where('ventas.estado_venta', 'Completada')
            ->select(
                'productos.id',
                'productos.nombre',
                'productos.stock',
                'productos.precio',
                DB::raw('SUM(detalle_ventas.cantidad) as total_vendido'),
                DB::raw('SUM(detalle_ventas.cantidad * detalle_ventas.precio_unitario) as ingresos')
            )
            ->groupBy('productos.id', 'productos.nombre', 'productos.stock', 'productos.precio')
            ->orderByDesc('total_vendido')
            ->limit(10)
            ->get();

        $fechaCorte = now()->subDays($this->diasSinMovimiento);
        $productosConMovimiento = MovimientoInventario::where('created_at', '>=', $fechaCorte)
            ->pluck('producto_id')
            ->unique();
        $ventasRecientes = DetalleVenta::join('ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
            ->where('ventas.tienda_id', $tiendaId)
            ->where('ventas.created_at', '>=', $fechaCorte)
            ->pluck('detalle_ventas.producto_id')
            ->unique();
        $idsActivos = $productosConMovimiento->merge($ventasRecientes)->unique();

        $productosMuertos = Producto::whereNotIn('id', $idsActivos)
            ->where('stock', '>', 0)
            ->orderByDesc(DB::raw('stock * precio_costo'))
            ->get();

        $valorizacion = Producto::select(
                DB::raw('SUM(stock * precio_costo) as valor_costo'),
                DB::raw('SUM(stock * precio) as valor_venta'),
                DB::raw('COUNT(*) as total_productos')
            )->first();

        $stockCritico = Producto::with('categoria')
            ->where(function ($q) {
                $q->whereColumn('stock', '<=', 'stock_minimo')
                  ->orWhere('estado', 'Agotado');
            })
            ->orderBy('stock')
            ->get();

        // Rotacion de inventario y dias de cobertura (ultimos 30 dias)
        $vendidos30 = DetalleVenta::join('ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
            ->where('ventas.tienda_id', $tiendaId)
            ->where('ventas.estado_venta', 'Completada')
            ->where('ventas.created_at', '>=', now()->subDays(30))
            ->select('detalle_ventas.producto_id', DB::raw('SUM(detalle_ventas.cantidad) as vendidos'))
            ->groupBy('detalle_ventas.producto_id')
            ->pluck('vendidos', 'detalle_ventas.producto_id');

        $rotacionProductos = Producto::select('id', 'nombre', 'stock')->get()
            ->map(function ($p) use ($vendidos30) {
                $vendidos = (int) ($vendidos30[$p->id] ?? 0);
                $promDiario = $vendidos / 30;
                $rotacion = $p->stock > 0 ? $vendidos / $p->stock : 0;
                $cobertura = $promDiario > 0 ? $p->stock / $promDiario : null;
                return (object) [
                    'nombre'    => $p->nombre,
                    'stock'     => $p->stock,
                    'vendidos'  => $vendidos,
                    'rotacion'  => $rotacion,
                    'cobertura' => $cobertura,
                ];
            })
            ->filter(fn ($p) => $p->vendidos > 0)
            ->sortByDesc('rotacion')
            ->take(15)
            ->values();

        return compact('bestSellers', 'productosMuertos', 'valorizacion', 'stockCritico', 'rotacionProductos');
    }

    private function getVendedoresData(): array
    {
        $tiendaId = auth()->user()->tienda_id;

        $vendedores = User::where('tienda_id', $tiendaId)
            ->withCount(['ventas as ventas_completadas' => function ($q) {
                $q->where('estado_venta', 'Completada')
                    ->when($this->vtaFechaDesde, fn($q2) => $q2->whereDate('created_at', '>=', $this->vtaFechaDesde))
                    ->when($this->vtaFechaHasta, fn($q2) => $q2->whereDate('created_at', '<=', $this->vtaFechaHasta));
            }])
            ->withSum(['ventas as ingresos_generados' => function ($q) {
                $q->where('estado_venta', 'Completada')
                    ->when($this->vtaFechaDesde, fn($q2) => $q2->whereDate('created_at', '>=', $this->vtaFechaDesde))
                    ->when($this->vtaFechaHasta, fn($q2) => $q2->whereDate('created_at', '<=', $this->vtaFechaHasta));
            }], 'total')
            ->orderByDesc('ingresos_generados')
            ->get();

        $totalIngresos = $vendedores->sum('ingresos_generados');

        return compact('vendedores', 'totalIngresos');
    }

    private function getDashboardData(): array
    {
        $dias = (int) $this->dashPeriodo;
        $fechaInicio = now()->subDays($dias - 1)->startOfDay();

        $ventasPorDia = Venta::where('estado_venta', 'Completada')
            ->where('created_at', '>=', $fechaInicio)
            ->select(DB::raw('DATE(created_at) as fecha'), DB::raw('COUNT(*) as total'), DB::raw('SUM(total) as ingresos'))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get()
            ->keyBy('fecha');

        $labels = [];
        $dataVentas = [];
        $dataIngresos = [];
        for ($i = 0; $i < $dias; $i++) {
            $fecha = now()->subDays($dias - 1 - $i)->format('Y-m-d');
            $labels[] = now()->subDays($dias - 1 - $i)->format('d/m');
            $dataVentas[] = $ventasPorDia[$fecha]->total ?? 0;
            $dataIngresos[] = (float) ($ventasPorDia[$fecha]->ingresos ?? 0);
        }

        $ventasPorCategoria = DetalleVenta::join('ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
            ->join('productos', 'detalle_ventas.producto_id', '=', 'productos.id')
            ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->where('ventas.tienda_id', auth()->user()->tienda_id)
            ->where('ventas.estado_venta', 'Completada')
            ->where('ventas.created_at', '>=', $fechaInicio)
            ->select('categorias.nombre', DB::raw('SUM(detalle_ventas.cantidad * detalle_ventas.precio_unitario) as total'))
            ->groupBy('categorias.nombre')
            ->orderByDesc('total')
            ->get();

        $ventasPorHora = Venta::where('estado_venta', 'Completada')
            ->where('created_at', '>=', $fechaInicio)
            ->select(DB::raw('HOUR(created_at) as hora'), DB::raw('COUNT(*) as total'))
            ->groupBy('hora')
            ->orderBy('hora')
            ->get()
            ->keyBy('hora');

        $horasData = [];
        for ($h = 0; $h < 24; $h++) {
            $horasData[$h] = $ventasPorHora[$h]->total ?? 0;
        }

        // Semana actual vs anterior
        $inicioSemanaActual = now()->startOfWeek();
        $inicioSemanaAnterior = now()->subWeek()->startOfWeek();
        $finSemanaAnterior = now()->subWeek()->endOfWeek();

        $ventasSemanaActual = Venta::where('estado_venta', 'Completada')
            ->where('created_at', '>=', $inicioSemanaActual)
            ->sum('total');
        $ventasSemanaAnterior = Venta::where('estado_venta', 'Completada')
            ->whereBetween('created_at', [$inicioSemanaAnterior, $finSemanaAnterior])
            ->sum('total');

        $cambioSemanal = $ventasSemanaAnterior > 0
            ? (($ventasSemanaActual - $ventasSemanaAnterior) / $ventasSemanaAnterior) * 100
            : ($ventasSemanaActual > 0 ? 100 : 0);

        // Ventas por dia de la semana (MySQL DAYOFWEEK: 1=Domingo ... 7=Sabado)
        $ventasPorDow = Venta::where('estado_venta', 'Completada')
            ->where('created_at', '>=', $fechaInicio)
            ->select(DB::raw('DAYOFWEEK(created_at) as dow'), DB::raw('COUNT(*) as total'))
            ->groupBy('dow')
            ->get()
            ->keyBy('dow');
        $dowData = [];
        for ($d = 1; $d <= 7; $d++) {
            $dowData[$d] = $ventasPorDow[$d]->total ?? 0;
        }

        return compact(
            'labels', 'dataVentas', 'dataIngresos',
            'ventasPorCategoria', 'horasData', 'dowData',
            'ventasSemanaActual', 'ventasSemanaAnterior', 'cambioSemanal'
        );
    }

    private function getClientesData(): array
    {
        $desde = $this->vtaFechaDesde;
        $hasta = $this->vtaFechaHasta;

        // Top compradores del periodo
        $topClientes = Venta::where('estado_venta', 'Completada')
            ->whereNotNull('cliente_id')
            ->when($desde, fn ($q) => $q->whereDate('created_at', '>=', $desde))
            ->when($hasta, fn ($q) => $q->whereDate('created_at', '<=', $hasta))
            ->select('cliente_id', DB::raw('COUNT(*) as num_compras'), DB::raw('SUM(total) as total_gastado'))
            ->groupBy('cliente_id')
            ->orderByDesc('total_gastado')
            ->with('cliente:id,nombre,telefono')
            ->limit(10)
            ->get();

        // Nuevos vs recurrentes (sobre todo el historial)
        $comprasPorCliente = Venta::where('estado_venta', 'Completada')
            ->whereNotNull('cliente_id')
            ->select('cliente_id', DB::raw('COUNT(*) as c'))
            ->groupBy('cliente_id')
            ->get();
        $recurrentes = $comprasPorCliente->where('c', '>', 1)->count();
        $nuevos = $comprasPorCliente->where('c', 1)->count();

        // Inactivos: clientes que compraron alguna vez pero no en los ultimos N dias
        $fechaCorte = now()->subDays($this->diasClienteInactivo);
        $clientesActivos = Venta::where('estado_venta', 'Completada')
            ->where('created_at', '>=', $fechaCorte)
            ->whereNotNull('cliente_id')
            ->pluck('cliente_id')
            ->unique();
        $clientesInactivos = Cliente::whereNotIn('id', $clientesActivos->all() ?: [0])
            ->whereHas('ventas')
            ->withCount('ventas')
            ->get();

        $totalClientes = Cliente::count();

        return compact('topClientes', 'recurrentes', 'nuevos', 'clientesInactivos', 'totalClientes');
    }

    private function getProveedoresData(): array
    {
        $desde = $this->vtaFechaDesde;
        $hasta = $this->vtaFechaHasta;

        $compras = MovimientoInventario::where('tipo', 'entrada')
            ->whereNotNull('proveedor')
            ->where('proveedor', '!=', '')
            ->when($desde, fn ($q) => $q->whereDate('created_at', '>=', $desde))
            ->when($hasta, fn ($q) => $q->whereDate('created_at', '<=', $hasta))
            ->select(
                'proveedor',
                DB::raw('COUNT(*) as num_entradas'),
                DB::raw('SUM(cantidad) as unidades'),
                DB::raw('SUM(cantidad * COALESCE(precio_unitario, 0)) as costo_total')
            )
            ->groupBy('proveedor')
            ->orderByDesc('costo_total')
            ->get();

        $totalCompras = $compras->sum('costo_total');

        return compact('compras', 'totalCompras');
    }

    private function getRentabilidadData(): array
    {
        $tiendaId = auth()->user()->tienda_id;
        $desde = $this->vtaFechaDesde;
        $hasta = $this->vtaFechaHasta;

        $base = DetalleVenta::join('ventas', 'detalle_ventas.venta_id', '=', 'ventas.id')
            ->join('productos', 'detalle_ventas.producto_id', '=', 'productos.id')
            ->where('ventas.tienda_id', $tiendaId)
            ->where('ventas.estado_venta', 'Completada')
            ->when($desde, fn ($q) => $q->whereDate('ventas.created_at', '>=', $desde))
            ->when($hasta, fn ($q) => $q->whereDate('ventas.created_at', '<=', $hasta));

        $rentProductos = (clone $base)
            ->select(
                'productos.id',
                'productos.nombre',
                DB::raw('SUM(detalle_ventas.cantidad) as unidades'),
                DB::raw('SUM(detalle_ventas.cantidad * detalle_ventas.precio_unitario) as ingresos'),
                DB::raw('SUM(detalle_ventas.cantidad * productos.precio_costo) as costo'),
                DB::raw('SUM(detalle_ventas.cantidad * (detalle_ventas.precio_unitario - productos.precio_costo)) as utilidad')
            )
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('utilidad')
            ->get();

        $rentCategorias = (clone $base)
            ->join('categorias', 'productos.categoria_id', '=', 'categorias.id')
            ->select(
                'categorias.nombre',
                DB::raw('SUM(detalle_ventas.cantidad * detalle_ventas.precio_unitario) as ingresos'),
                DB::raw('SUM(detalle_ventas.cantidad * (detalle_ventas.precio_unitario - productos.precio_costo)) as utilidad')
            )
            ->groupBy('categorias.nombre')
            ->orderByDesc('utilidad')
            ->get();

        // Analisis ABC (Pareto) por ingresos acumulados
        $ordenados = $rentProductos->sortByDesc('ingresos')->values();
        $totalIngresos = $ordenados->sum('ingresos');
        $acumulado = 0;
        $abc = $ordenados->map(function ($p) use (&$acumulado, $totalIngresos) {
            $acumulado += $p->ingresos;
            $pctAcum = $totalIngresos > 0 ? ($acumulado / $totalIngresos) * 100 : 0;
            $clase = $pctAcum <= 80 ? 'A' : ($pctAcum <= 95 ? 'B' : 'C');
            return (object) [
                'nombre'        => $p->nombre,
                'ingresos'      => $p->ingresos,
                'pct_acumulado' => $pctAcum,
                'clase'         => $clase,
            ];
        });
        $resumenAbc = [
            'A' => $abc->where('clase', 'A')->count(),
            'B' => $abc->where('clase', 'B')->count(),
            'C' => $abc->where('clase', 'C')->count(),
        ];

        return compact('rentProductos', 'rentCategorias', 'abc', 'resumenAbc');
    }

    private function getFiscalData(): array
    {
        $desde = $this->vtaFechaDesde;
        $hasta = $this->vtaFechaHasta;

        $ventasFacturadas = Venta::where('estado_venta', 'Completada')
            ->whereNotNull('cliente_nit')
            ->where('cliente_nit', '!=', '')
            ->when($desde, fn ($q) => $q->whereDate('created_at', '>=', $desde))
            ->when($hasta, fn ($q) => $q->whereDate('created_at', '<=', $hasta))
            ->orderBy('created_at')
            ->get();

        $totalFacturado = $ventasFacturadas->sum('total');
        $countFacturado = $ventasFacturadas->count();

        $sinNit = Venta::where('estado_venta', 'Completada')
            ->when($desde, fn ($q) => $q->whereDate('created_at', '>=', $desde))
            ->when($hasta, fn ($q) => $q->whereDate('created_at', '<=', $hasta))
            ->where(function ($q) {
                $q->whereNull('cliente_nit')->orWhere('cliente_nit', '');
            })
            ->count();

        return compact('ventasFacturadas', 'totalFacturado', 'countFacturado', 'sinNit');
    }

    public function exportarFiscalCsv()
    {
        $data = $this->getFiscalData();
        $ventas = $data['ventasFacturadas'];

        $filename = 'libro-ventas-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($ventas) {
            $out = fopen('php://output', 'w');
            // BOM para que Excel lea acentos correctamente
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($out, ['Fecha', 'Codigo', 'Cliente', 'NIT/CI', 'Metodo Pago', 'Total (Bs.)']);
            foreach ($ventas as $v) {
                fputcsv($out, [
                    $v->created_at->format('d/m/Y H:i'),
                    $v->codigo_pedido,
                    $v->cliente_nombre,
                    $v->cliente_nit,
                    ucfirst($v->metodo_pago),
                    number_format($v->total, 2, '.', ''),
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function render()
    {
        $usuarios = User::where('tienda_id', auth()->user()->tienda_id)->orderBy('name')->get();

        $data = ['usuarios' => $usuarios];

        if ($this->activeTab === 'movimientos') {
            $data['movimientos'] = $this->getMovimientosQuery()->paginate(20);
        }

        if ($this->activeTab === 'ventas') {
            $data = array_merge($data, $this->getVentasData());
        }

        if ($this->activeTab === 'productos') {
            $data = array_merge($data, $this->getProductosData());
        }

        if ($this->activeTab === 'vendedores') {
            $data = array_merge($data, $this->getVendedoresData());
        }

        if ($this->activeTab === 'dashboard') {
            $data = array_merge($data, $this->getDashboardData());
        }

        if ($this->activeTab === 'clientes') {
            $data = array_merge($data, $this->getClientesData());
        }

        if ($this->activeTab === 'proveedores') {
            $data = array_merge($data, $this->getProveedoresData());
        }

        if ($this->activeTab === 'rentabilidad') {
            $data = array_merge($data, $this->getRentabilidadData());
        }

        if ($this->activeTab === 'fiscal') {
            $data = array_merge($data, $this->getFiscalData());
        }

        return view('livewire.reportes.index', $data);
    }
}
