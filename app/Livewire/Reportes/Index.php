<?php

namespace App\Livewire\Reportes;

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

        return compact('totalVentas', 'ingresosBrutos', 'costoVentas', 'utilidadBruta', 'ticketPromedio', 'margenPorcentaje', 'porMetodoPago');
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

        return compact('bestSellers', 'productosMuertos', 'valorizacion', 'stockCritico');
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

        return compact(
            'labels', 'dataVentas', 'dataIngresos',
            'ventasPorCategoria', 'horasData',
            'ventasSemanaActual', 'ventasSemanaAnterior', 'cambioSemanal'
        );
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

        return view('livewire.reportes.index', $data);
    }
}
