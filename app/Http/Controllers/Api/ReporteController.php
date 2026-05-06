<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function ventas(Request $request): JsonResponse
    {
        $request->validate([
            'fecha_desde' => 'nullable|date',
            'fecha_hasta' => 'nullable|date|after_or_equal:fecha_desde',
        ]);

        $query = Venta::where('estado_venta', 'Completada')
            ->when($request->filled('fecha_desde'), fn($q) => $q->whereDate('created_at', '>=', $request->fecha_desde))
            ->when($request->filled('fecha_hasta'), fn($q) => $q->whereDate('created_at', '<=', $request->fecha_hasta));

        $resumen = [
            'total_ventas'    => (clone $query)->count(),
            'ingresos_total'  => (clone $query)->sum('total'),
            'por_dia'         => (clone $query)
                ->select(DB::raw('DATE(created_at) as fecha'), DB::raw('COUNT(*) as ventas'), DB::raw('SUM(total) as ingresos'))
                ->groupBy('fecha')
                ->orderBy('fecha')
                ->get(),
            'por_metodo_pago' => (clone $query)
                ->select('metodo_pago', DB::raw('COUNT(*) as ventas'), DB::raw('SUM(total) as ingresos'))
                ->groupBy('metodo_pago')
                ->get(),
            'por_vendedor'    => (clone $query)
                ->join('users', 'ventas.vendedor_id', '=', 'users.id')
                ->select('users.name as vendedor', DB::raw('COUNT(*) as ventas'), DB::raw('SUM(ventas.total) as ingresos'))
                ->groupBy('users.name')
                ->orderByDesc('ingresos')
                ->get(),
        ];

        return response()->json($resumen);
    }

    public function inventario(): JsonResponse
    {
        $productos = Producto::with('categoria:id,nombre')->get();

        $resumen = [
            'total_productos'  => $productos->count(),
            'sin_stock'        => $productos->where('stock', 0)->count(),
            'stock_bajo'       => $productos->filter(fn($p) => $p->stock > 0 && $p->stock <= $p->stock_minimo)->count(),
            'valor_inventario' => $productos->sum(fn($p) => $p->stock * $p->precio),
            'alertas'          => $productos
                ->filter(fn($p) => $p->stock <= $p->stock_minimo)
                ->map(fn($p) => [
                    'id'           => $p->id,
                    'nombre'       => $p->nombre,
                    'stock'        => $p->stock,
                    'stock_minimo' => $p->stock_minimo,
                    'estado'       => $p->estado,
                    'categoria'    => $p->categoria?->nombre,
                ])
                ->values(),
        ];

        return response()->json($resumen);
    }
}
