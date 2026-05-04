<?php

namespace App\Livewire;

use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Venta;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.admin')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function render()
    {
        $totalProductos     = Producto::count();
        $totalMovimientos   = MovimientoInventario::count();
        $sinStock           = Producto::where('estado', 'Agotado')->count();
        $stockBajo          = Producto::whereColumn('stock', '<=', 'stock_minimo')
                                ->where('estado', 'Disponible')->count();

        $alertas = Producto::whereColumn('stock', '<=', 'stock_minimo')
                    ->orWhere('estado', 'Agotado')
                    ->with('categoria')
                    ->orderBy('stock')
                    ->take(8)
                    ->get();

        $ventasHoy   = Venta::whereDate('created_at', today())->where('estado_venta', 'Completada')->count();
        $ingresosHoy = Venta::whereDate('created_at', today())->where('estado_venta', 'Completada')->sum('total');

        $ultimasVentas = Venta::with('vendedor')
                            ->where('estado_venta', 'Completada')
                            ->latest()
                            ->take(5)
                            ->get();

        return view('livewire.dashboard', compact(
            'totalProductos', 'totalMovimientos', 'sinStock', 'stockBajo',
            'alertas', 'ventasHoy', 'ingresosHoy', 'ultimasVentas'
        ));
    }
}
