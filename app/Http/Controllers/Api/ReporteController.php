<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @tags Reportes (solo Admin)
 */
class ReporteController extends Controller
{
    /**
     * Reporte de ventas
     *
     * Genera un resumen de ventas de la tienda: totales, ingresos, desglose
     * por día, por método de pago y por vendedor. Solo accesible para usuarios
     * con rol `admin`.
     *
     * @queryParam fecha_desde string Fecha de inicio en formato Y-m-d. Example: 2025-01-01
     * @queryParam fecha_hasta string Fecha de fin en formato Y-m-d. Example: 2025-12-31
     */
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

    /**
     * Reporte de inventario
     *
     * Genera un resumen del estado del inventario: total de productos,
     * productos sin stock, productos con stock bajo y valor total del inventario.
     * Incluye una lista de alertas para productos que han alcanzado su stock mínimo.
     * Solo accesible para usuarios con rol `admin`.
     */
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
