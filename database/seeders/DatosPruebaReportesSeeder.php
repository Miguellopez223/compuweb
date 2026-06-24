<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\DetalleVenta;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Tienda;
use App\Models\User;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Genera datos de prueba ADICIONALES sobre la tienda demo "Tercer Tiempo"
 * para poblar y validar todos los reportes (rentabilidad/ABC, clientes,
 * proveedores, libro de ventas, rotación, anulaciones, heatmaps).
 *
 * Ejecutar con:  php artisan db:seed --class=DatosPruebaReportesSeeder
 * Es seguro: si detecta que ya corrió, no duplica datos.
 */
class DatosPruebaReportesSeeder extends Seeder
{
    private int $tid;

    public function run(): void
    {
        $tienda = Tienda::where('slug', 'tercertiempo')->first();
        if (! $tienda) {
            $this->command->warn('No se encontró la tienda "tercertiempo". Ejecuta primero: php artisan db:seed --class=TercerTiempoSeeder');
            return;
        }
        $this->tid = $tienda->id;

        // Guard anti-duplicado
        if (Cliente::where('tienda_id', $this->tid)->where('nombre', 'LIKE', '[Demo]%')->exists()) {
            $this->command->warn('Los datos de prueba de reportes ya existen. Nada que hacer.');
            return;
        }

        $users     = User::where('tienda_id', $this->tid)->get();
        $productos = Producto::where('tienda_id', $this->tid)->where('precio', '>', 0)->get();

        if ($users->isEmpty() || $productos->isEmpty()) {
            $this->command->warn('Faltan usuarios o productos en la tienda demo.');
            return;
        }

        $this->command->info('Generando clientes demo...');
        $clientesDemo = $this->crearClientesDemo();

        $this->command->info('Generando entradas de mercadería (proveedores)...');
        $this->crearEntradasProveedores($productos, $users);

        $this->command->info('Generando ventas distribuidas en 120 días...');
        $ventas = $this->crearVentasMasivas($productos, $users);

        $this->command->info('Anulando algunas ventas...');
        $this->anularAlgunasVentas($ventas, $users);

        $this->command->info('✓ Datos de prueba de reportes generados correctamente.');
    }

    private function crearClientesDemo(): array
    {
        $nombres = [
            'Camila Rojas', 'Sebastián Ortiz', 'Daniela Flores', 'Mateo Gutiérrez',
            'Gabriela Mendoza', 'Nicolás Téllez', 'Antonella Cruz', 'Joaquín Vargas',
            'Renata Suárez', 'Emiliano Paz', 'Isabella Ríos', 'Thiago Morales',
            'Mariana Cabrera', 'Benjamín Soliz', 'Victoria Aguilar',
        ];

        $clientes = [];
        foreach ($nombres as $n) {
            $clientes[] = Cliente::create([
                'tienda_id' => $this->tid,
                'nombre'    => '[Demo] ' . $n,
                'ci_nit'    => rand(1, 100) <= 70 ? (string) rand(1000000, 9999999) : null,
                'telefono'  => '7' . rand(1000000, 9999999),
                'email'     => rand(1, 100) <= 40 ? strtolower(str_replace(' ', '.', $n)) . '@gmail.com' : null,
                'estado'    => true,
            ]);
        }

        return $clientes;
    }

    private function crearEntradasProveedores($productos, $users): void
    {
        $proveedores = [
            'Distribuidora La Paz', 'CBN Mayorista', 'Coca-Cola FEMSA',
            'Importadora Andina', 'Bebidas del Sur', 'Licorería Central',
        ];

        for ($k = 0; $k < 45; $k++) {
            $p     = $productos->random();
            $prov  = $proveedores[array_rand($proveedores)];
            $cant  = rand(12, 80);
            $fecha = Carbon::now()->subDays(rand(1, 100))->setTime(rand(8, 17), rand(0, 59));
            $costo = $p->precio_costo > 0 ? $p->precio_costo : round($p->precio * 0.6, 2);

            // Reponer stock para que haya inventario que vender
            $p->increment('stock', $cant);
            if ($p->stock > 0 && $p->estado === 'Agotado') {
                $p->update(['estado' => 'Disponible']);
            }

            $mov = MovimientoInventario::create([
                'tienda_id'       => $this->tid,
                'producto_id'     => $p->id,
                'user_id'         => $users->random()->id,
                'tipo'            => 'entrada',
                'cantidad'        => $cant,
                'precio_unitario' => $costo,
                'proveedor'       => $prov,
                'referencia'      => 'Compra mayorista',
            ]);

            DB::table('movimiento_inventarios')->where('id', $mov->id)
                ->update(['created_at' => $fecha, 'updated_at' => $fecha]);
        }
    }

    private function crearVentasMasivas($productos, $users): array
    {
        $clientes = Cliente::where('tienda_id', $this->tid)->get();
        $metodos  = ['efectivo', 'efectivo', 'qr', 'qr', 'transferencia']; // efectivo y qr más frecuentes
        $creadas  = [];

        for ($k = 0; $k < 140; $k++) {
            $diasAtras = rand(0, 120);
            // Más ventas en fin de semana y en la noche (realismo para heatmaps)
            $hora    = rand(1, 100) <= 70 ? rand(18, 23) : rand(11, 17);
            $fecha   = Carbon::now()->subDays($diasAtras)->setTime($hora, rand(0, 59));
            $vendedor = $users->random();
            $conCliente = rand(1, 100) <= 75;
            $cliente = $conCliente ? $clientes->random() : null;
            $metodo  = $metodos[array_rand($metodos)];

            $numItems = rand(1, 4);
            $items = [];
            $usados = [];
            for ($j = 0; $j < $numItems; $j++) {
                $p = $productos->random();
                if (in_array($p->id, $usados, true)) {
                    continue;
                }
                $usados[] = $p->id;
                $items[]  = [$p, rand(1, 6)];
            }
            if (empty($items)) {
                continue;
            }

            $total = 0;
            foreach ($items as [$p, $cant]) {
                $total += $p->precio * $cant;
            }

            // NIT: del cliente si lo tiene, o aleatorio (factura a consumidor)
            $nit = $cliente?->ci_nit;
            if (! $nit && rand(1, 100) <= 35) {
                $nit = (string) rand(1000000, 9999999);
            }

            $codigo = 'VTA-' . $this->tid . $fecha->format('ymdHis') . str_pad($k + 1, 3, '0', STR_PAD_LEFT);

            $venta = Venta::create([
                'tienda_id'        => $this->tid,
                'vendedor_id'      => $vendedor->id,
                'codigo_pedido'    => $codigo,
                'cliente_id'       => $cliente?->id,
                'cliente_nombre'   => $cliente?->nombre ?? 'Cliente genérico',
                'cliente_telefono' => $cliente?->telefono,
                'cliente_email'    => $cliente?->email,
                'cliente_nit'      => $nit,
                'total'            => $total,
                'metodo_pago'      => $metodo,
                'estado_venta'     => 'Completada',
            ]);

            DB::table('ventas')->where('id', $venta->id)
                ->update(['created_at' => $fecha, 'updated_at' => $fecha]);

            foreach ($items as [$p, $cant]) {
                DetalleVenta::create([
                    'venta_id'        => $venta->id,
                    'producto_id'     => $p->id,
                    'cantidad'        => $cant,
                    'precio_unitario' => $p->precio,
                ]);

                $nuevoStock = max(0, $p->stock - $cant);
                $p->update([
                    'stock'  => $nuevoStock,
                    'estado' => $nuevoStock <= 0 ? 'Agotado' : 'Disponible',
                ]);

                $mov = MovimientoInventario::create([
                    'tienda_id'       => $this->tid,
                    'producto_id'     => $p->id,
                    'user_id'         => $vendedor->id,
                    'tipo'            => 'salida',
                    'cantidad'        => $cant,
                    'precio_unitario' => $p->precio,
                    'referencia'      => 'Venta #' . $venta->id,
                ]);

                DB::table('movimiento_inventarios')->where('id', $mov->id)
                    ->update(['created_at' => $fecha, 'updated_at' => $fecha]);
            }

            $creadas[] = $venta->id;
        }

        return $creadas;
    }

    private function anularAlgunasVentas(array $ventaIds, $users): void
    {
        // Anular ~8% de las ventas generadas, reintegrando stock
        $aAnular = collect($ventaIds)->shuffle()->take((int) ceil(count($ventaIds) * 0.08));

        foreach ($aAnular as $vid) {
            $venta = Venta::with('detalles')->find($vid);
            if (! $venta || $venta->estado_venta === 'Anulada') {
                continue;
            }

            foreach ($venta->detalles as $detalle) {
                $producto = Producto::find($detalle->producto_id);
                if ($producto) {
                    $producto->update([
                        'stock'  => $producto->stock + $detalle->cantidad,
                        'estado' => 'Disponible',
                    ]);
                    MovimientoInventario::create([
                        'tienda_id'       => $this->tid,
                        'producto_id'     => $producto->id,
                        'user_id'         => $users->random()->id,
                        'tipo'            => 'entrada',
                        'cantidad'        => $detalle->cantidad,
                        'precio_unitario' => $detalle->precio_unitario,
                        'referencia'      => 'Anulación de venta #' . $venta->id,
                    ]);
                }
            }

            $venta->update(['estado_venta' => 'Anulada']);
        }
    }
}
