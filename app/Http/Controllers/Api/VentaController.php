<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetalleVenta;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user('api');

        $ventas = Venta::withoutGlobalScopes()
            ->with(['vendedor:id,name', 'detalles.producto:id,nombre'])
            ->where('tienda_id', $user->tienda_id)
            ->when($request->filled('estado'), fn($q) => $q->where('estado_venta', $request->estado))
            ->when($request->filled('fecha_desde'), fn($q) => $q->whereDate('created_at', '>=', $request->fecha_desde))
            ->when($request->filled('fecha_hasta'), fn($q) => $q->whereDate('created_at', '<=', $request->fecha_hasta))
            ->latest()
            ->paginate(20);

        return response()->json($ventas);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'cliente_nombre'      => 'required|string|max:255',
            'cliente_telefono'    => 'nullable|string|max:50',
            'cliente_email'       => 'nullable|email|max:255',
            'cliente_nit'         => 'nullable|string|max:20',
            'metodo_pago'         => 'required|in:efectivo,qr,transferencia',
            'items'               => 'required|array|min:1',
            'items.*.producto_id' => 'required|integer|exists:productos,id',
            'items.*.cantidad'    => 'required|integer|min:1',
        ]);

        $user     = $request->user('api');
        $tiendaId = $user->tienda_id;
        $total    = 0;
        $items    = [];

        foreach ($request->items as $item) {
            $producto = Producto::withoutGlobalScopes()
                ->where('tienda_id', $tiendaId)
                ->findOrFail($item['producto_id']);

            if ($producto->stock < $item['cantidad']) {
                return response()->json([
                    'message' => "Stock insuficiente para '{$producto->nombre}'. Disponible: {$producto->stock}.",
                ], 422);
            }

            $subtotal = $producto->precio * $item['cantidad'];
            $total   += $subtotal;

            $items[] = [
                'producto'        => $producto,
                'cantidad'        => $item['cantidad'],
                'precio_unitario' => $producto->precio,
            ];
        }

        $venta = DB::transaction(function () use ($user, $tiendaId, $request, $total, $items) {
            $codigo = 'VTA-' . $tiendaId . now()->format('ymdHis');

            $venta = Venta::create([
                'tienda_id'         => $tiendaId,
                'vendedor_id'       => $user->id,
                'codigo_pedido'     => $codigo,
                'cliente_nombre'    => $request->cliente_nombre,
                'cliente_telefono'  => $request->cliente_telefono,
                'cliente_email'     => $request->cliente_email,
                'cliente_nit'       => $request->cliente_nit,
                'total'             => $total,
                'metodo_pago'       => $request->metodo_pago,
                'estado_venta'      => 'Completada',
            ]);

            foreach ($items as $item) {
                DetalleVenta::create([
                    'venta_id'        => $venta->id,
                    'producto_id'     => $item['producto']->id,
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                ]);

                $item['producto']->decrement('stock', $item['cantidad']);

                if ($item['producto']->fresh()->stock <= 0) {
                    $item['producto']->update(['estado' => 'Agotado']);
                }

                MovimientoInventario::create([
                    'tienda_id'       => $tiendaId,
                    'producto_id'     => $item['producto']->id,
                    'user_id'         => $user->id,
                    'tipo'            => 'salida',
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'referencia'      => "Venta #{$venta->id}",
                ]);
            }

            return $venta;
        });

        return response()->json([
            'message' => 'Venta registrada correctamente.',
            'venta'   => $venta->load(['detalles.producto:id,nombre']),
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user('api');

        $venta = Venta::withoutGlobalScopes()
            ->with(['vendedor:id,name', 'detalles.producto:id,nombre,sku'])
            ->where('tienda_id', $user->tienda_id)
            ->findOrFail($id);

        return response()->json($venta);
    }
}
