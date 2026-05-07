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

/**
 * @tags Ventas
 */
class VentaController extends Controller
{
    /**
     * Listar ventas
     *
     * Retorna el historial paginado de ventas de la tienda del usuario autenticado.
     * Solo se ven las ventas de la propia tienda (multitenencia).
     *
     * @queryParam estado string Filtrar por estado: `Completada`, `Cancelada`. Example: Completada
     * @queryParam fecha_desde string Fecha de inicio en formato Y-m-d. Example: 2025-01-01
     * @queryParam fecha_hasta string Fecha de fin en formato Y-m-d. Example: 2025-12-31
     * @queryParam page integer Número de página (20 resultados por página). Example: 1
     */
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

    /**
     * Registrar venta
     *
     * Crea una nueva venta, descuenta el stock de cada producto y registra
     * los movimientos de inventario correspondientes.
     * Requiere token de vendedor o admin.
     *
     * @bodyParam cliente_nombre string required Nombre completo del cliente. Example: Juan Pérez
     * @bodyParam cliente_telefono string Teléfono del cliente. Example: 70012345
     * @bodyParam cliente_email string Email del cliente. Example: juan@email.com
     * @bodyParam cliente_nit string CI o NIT del cliente. Example: 1234567
     * @bodyParam metodo_pago string required Método de pago: `efectivo`, `qr`, `transferencia`. Example: efectivo
     * @bodyParam items array required Lista de productos a vender.
     * @bodyParam items[].producto_id integer required ID del producto. Example: 5
     * @bodyParam items[].cantidad integer required Cantidad a vender (mínimo 1). Example: 2
     */
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

    /**
     * Detalle de una venta
     *
     * Retorna la información completa de una venta, incluyendo todos los
     * productos vendidos con sus cantidades y precios unitarios.
     */
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
