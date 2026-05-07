<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\DetalleVenta;
use App\Models\MovimientoInventario;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TercerTiempoSeeder extends Seeder
{
    private int $tid = 2; // tienda_id

    public function run(): void
    {
        $this->limpiar();
        $users    = $this->crearUsuarios();
        $prods    = $this->crearProductos();
        $this->crearMovimientosIniciales($prods, $users['admin']);
        $clientes = $this->crearClientes();
        $this->crearVentas($prods, $clientes, $users);
    }

    // ─────────────────────────────────────────────────────────────
    //  LIMPIEZA
    // ─────────────────────────────────────────────────────────────
    private function limpiar(): void
    {
        $ventaIds = DB::table('ventas')->where('tienda_id', $this->tid)->pluck('id');
        DB::table('detalle_ventas')->whereIn('venta_id', $ventaIds)->delete();
        DB::table('ventas')->where('tienda_id', $this->tid)->delete();
        DB::table('movimiento_inventarios')->where('tienda_id', $this->tid)->delete();
        DB::table('clientes')->where('tienda_id', $this->tid)->delete();
        // atributo_productos tiene cascade delete desde productos
        DB::table('productos')->where('tienda_id', $this->tid)->delete();
        DB::table('categorias')->where('tienda_id', $this->tid)->delete();
        DB::table('users')->where('tienda_id', $this->tid)->delete();
    }

    // ─────────────────────────────────────────────────────────────
    //  USUARIOS
    // ─────────────────────────────────────────────────────────────
    private function crearUsuarios(): array
    {
        $admin = User::create([
            'tienda_id'        => $this->tid,
            'name'             => 'Alejandro Rodriguez',
            'email'            => 'alejandro@tercertiempo.com',
            'password'         => Hash::make('admin123'),
            'role'             => 'admin',
            'whatsapp_number'  => '59171234567',
            'visible_catalogo' => true,
        ]);

        $v1 = User::create([
            'tienda_id'        => $this->tid,
            'name'             => 'Carlos Mendoza',
            'email'            => 'carlos@tercertiempo.com',
            'password'         => Hash::make('vendedor123'),
            'role'             => 'vendedor',
            'whatsapp_number'  => '59178901234',
            'visible_catalogo' => true,
        ]);

        $v2 = User::create([
            'tienda_id'        => $this->tid,
            'name'             => 'Ana Torrez',
            'email'            => 'ana@tercertiempo.com',
            'password'         => Hash::make('vendedor123'),
            'role'             => 'vendedor',
            'whatsapp_number'  => '59176543210',
            'visible_catalogo' => true,
        ]);

        return ['admin' => $admin, 'v1' => $v1, 'v2' => $v2];
    }

    // ─────────────────────────────────────────────────────────────
    //  PRODUCTOS
    //  stock = stock INICIAL (antes de las ventas demo)
    // ─────────────────────────────────────────────────────────────
    private function crearProductos(): array
    {
        $catalogo = [
            'Cervezas' => [
                ['nombre' => 'Amstel 269ml',                         'precio' =>  10.00, 'costo' =>  6.00, 'stock' =>  48],
            ],
            'Tragos por Vasos' => [
                ['nombre' => 'Tequila Jarana',                       'precio' =>  15.00, 'costo' =>  8.00, 'stock' => 100],
                ['nombre' => 'Jägermeister',                         'precio' =>  15.00, 'costo' =>  9.00, 'stock' =>  80],
                ['nombre' => 'Bacardi Gold',                         'precio' =>  25.00, 'costo' => 14.00, 'stock' =>  80],
                ['nombre' => 'Gin Greenalls - Clasico',               'precio' =>  25.00, 'costo' => 14.00, 'stock' =>  60],
                ['nombre' => 'Gin Greenalls - Frutos Rojos',          'precio' =>  25.00, 'costo' => 14.00, 'stock' =>  60],
                ['nombre' => 'Fernet Branca',                        'precio' =>  25.00, 'costo' => 15.00, 'stock' =>  70],
                ['nombre' => 'Vodka',                                'precio' =>  25.00, 'costo' => 13.00, 'stock' =>  80],
                ['nombre' => 'Singani Casa Real',                    'precio' =>  25.00, 'costo' => 13.00, 'stock' =>  70],
                ['nombre' => 'Mojito',                               'precio' =>  25.00, 'costo' => 12.00, 'stock' =>  60],
                ['nombre' => 'Martini Spritz',                       'precio' =>  25.00, 'costo' => 13.00, 'stock' =>  60],
                ['nombre' => 'Moscow Mule',                          'precio' =>  25.00, 'costo' => 13.00, 'stock' =>  60],
                ['nombre' => 'Negroni',                              'precio' =>  25.00, 'costo' => 14.00, 'stock' =>  50],
            ],
            'Otros Tragos' => [
                ['nombre' => 'Miks Vodka Ice 275 ml',                'precio' =>  25.00, 'costo' => 15.00, 'stock' =>  24],
            ],
            'Tragos en COMBO' => [
                ['nombre' => 'Botella Ron + 1 Coca Cola 2 Lts',      'precio' => 250.00, 'costo' => 160.00, 'stock' =>  8],
                ['nombre' => 'Botella Tequila + Limón & Sal',        'precio' => 250.00, 'costo' => 155.00, 'stock' =>  6],
                ['nombre' => 'Botella Jägermeister',                 'precio' => 280.00, 'costo' => 185.00, 'stock' =>  5],
                ['nombre' => 'Botella Gin + 1 Agua Tónica 1Lt',      'precio' => 280.00, 'costo' => 180.00, 'stock' =>  5],
                ['nombre' => 'Botella Fernet + 1 Coca Cola 2 Lts',   'precio' => 250.00, 'costo' => 158.00, 'stock' =>  7],
                ['nombre' => 'Botella Singani + 1 Ginger 1,5 Lt',    'precio' => 210.00, 'costo' => 130.00, 'stock' =>  8],
            ],
            'Hidratantes' => [
                ['nombre' => 'Powerade',                             'precio' =>  12.00, 'costo' =>  7.00, 'stock' =>  24],
                ['nombre' => 'Agua sin gas 500 ml',                  'precio' =>  10.00, 'costo' =>  4.00, 'stock' =>  36],
            ],
            'Energizantes' => [
                ['nombre' => 'Red Bull 250ml',                       'precio' =>  15.00, 'costo' =>  9.00, 'stock' =>  24],
            ],
            'Gaseosas 500 ml' => [
                ['nombre' => 'Coca Cola 500 ml',                     'precio' =>  10.00, 'costo' =>  5.00, 'stock' =>  48],
                ['nombre' => 'Coca Cola Zero 500 ml',                'precio' =>  10.00, 'costo' =>  5.00, 'stock' =>  24],
                ['nombre' => 'Sprite 500ml',                         'precio' =>  10.00, 'costo' =>  5.00, 'stock' =>  36],
                ['nombre' => 'Fanta 500 ml',                         'precio' =>  10.00, 'costo' =>  5.00, 'stock' =>  24],
            ],
            'Bebidas para Tragos' => [
                ['nombre' => 'Ginger Ale 1.5 Lt',                   'precio' =>  20.00, 'costo' => 12.00, 'stock' =>  12],
                ['nombre' => 'Agua tónica 1 Lt',                    'precio' =>  20.00, 'costo' => 11.00, 'stock' =>  12],
            ],
            'Cigarros' => [
                ['nombre' => 'CAMELL 20 unidades',                   'precio' =>  25.00, 'costo' => 18.00, 'stock' =>  20],
                ['nombre' => 'LM 20 unidades',                       'precio' =>  25.00, 'costo' => 17.00, 'stock' =>  20],
            ],
            'Packs' => [
                ['nombre' => 'PACK HIDRATACION - 12Aguas/12Powerade','precio' => 240.00, 'costo' => 154.00,'stock' =>   5],
            ],
            'Parqueos' => [
                ['nombre' => 'Parqueo Coliseo',                      'precio' =>   5.00, 'costo' =>  0.00, 'stock' => 999],
                ['nombre' => 'Parqueo Piscina',                      'precio' =>   5.00, 'costo' =>  0.00, 'stock' => 999],
            ],
        ];

        $mapa = [];
        foreach ($catalogo as $catNombre => $items) {
            $cat = Categoria::create(['tienda_id' => $this->tid, 'nombre' => $catNombre]);
            foreach ($items as $item) {
                $p = Producto::create([
                    'tienda_id'     => $this->tid,
                    'categoria_id'  => $cat->id,
                    'nombre'        => $item['nombre'],
                    'precio'        => $item['precio'],
                    'precio_costo'  => $item['costo'],
                    'stock'         => $item['stock'],
                    'stock_minimo'  => 5,
                    'unidad_medida' => 'unidad',
                    'estado'        => 'Disponible',
                ]);
                $mapa[$item['nombre']] = $p;
            }
        }
        return $mapa;
    }

    // ─────────────────────────────────────────────────────────────
    //  MOVIMIENTOS INICIALES (entrada de stock)
    // ─────────────────────────────────────────────────────────────
    private function crearMovimientosIniciales(array $prods, User $admin): void
    {
        $fecha = Carbon::now()->subDays(30)->toDateTimeString();
        foreach ($prods as $nombre => $p) {
            if ($p->precio_costo <= 0) continue; // parqueos no tienen costo
            $mov = MovimientoInventario::create([
                'tienda_id'       => $this->tid,
                'producto_id'     => $p->id,
                'user_id'         => $admin->id,
                'tipo'            => 'entrada',
                'cantidad'        => $p->stock,
                'precio_unitario' => $p->precio_costo,
                'referencia'      => 'Stock inicial',
            ]);
            DB::table('movimiento_inventarios')->where('id', $mov->id)
                ->update(['created_at' => $fecha, 'updated_at' => $fecha]);
        }
    }

    // ─────────────────────────────────────────────────────────────
    //  CLIENTES
    // ─────────────────────────────────────────────────────────────
    private function crearClientes(): array
    {
        $lista = [
            ['nombre' => 'Juan Pablo Quispe',     'ci_nit' => '5423891',  'telefono' => '72345678', 'email' => null],
            ['nombre' => 'María Fernanda Coca',   'ci_nit' => '8761234',  'telefono' => null,       'email' => 'mfcoca@gmail.com'],
            ['nombre' => 'Roberto Salinas',       'ci_nit' => null,       'telefono' => '69871234', 'email' => null],
            ['nombre' => 'Valentina Herrera',     'ci_nit' => '9823456',  'telefono' => null,       'email' => 'v.herrera@gmail.com'],
            ['nombre' => 'Diego Mamani',          'ci_nit' => null,       'telefono' => '71234567', 'email' => null],
            ['nombre' => 'Lucía Vásquez',         'ci_nit' => '4567890',  'telefono' => '68123456', 'email' => null],
            ['nombre' => 'Andrés Pinto',          'ci_nit' => null,       'telefono' => '60987654', 'email' => null],
        ];

        $mapa = [];
        foreach ($lista as $c) {
            $cliente = Cliente::create(array_merge($c, ['tienda_id' => $this->tid, 'estado' => true]));
            $mapa[$c['nombre']] = $cliente;
        }
        return $mapa;
    }

    // ─────────────────────────────────────────────────────────────
    //  VENTAS
    // ─────────────────────────────────────────────────────────────
    private function crearVentas(array $prods, array $clientes, array $users): void
    {
        $admin = $users['admin'];
        $v1    = $users['v1'];
        $v2    = $users['v2'];

        // [dias_atras, vendedor, cliente|null, metodo, [ [producto, cantidad], ... ]]
        $ventas = [
            [25, $v1, $clientes['Juan Pablo Quispe'], 'efectivo', [
                ['Amstel 269ml', 4],
                ['Vodka', 2],
                ['Coca Cola 500 ml', 2],
            ]],
            [23, $v2, null, 'efectivo', [
                ['Botella Fernet + 1 Coca Cola 2 Lts', 1],
            ]],
            [21, $v1, $clientes['María Fernanda Coca'], 'qr', [
                ['Mojito', 3],
                ['Red Bull 250ml', 2],
                ['Agua sin gas 500 ml', 1],
            ]],
            [18, $v2, $clientes['Roberto Salinas'], 'efectivo', [
                ['Amstel 269ml', 6],
                ['Tequila Jarana', 4],
                ['Coca Cola 500 ml', 2],
            ]],
            [16, $admin, $clientes['Valentina Herrera'], 'transferencia', [
                ['Botella Tequila + Limón & Sal', 1],
                ['Botella Ron + 1 Coca Cola 2 Lts', 1],
            ]],
            [14, $v1, $clientes['Diego Mamani'], 'efectivo', [
                ['Gin Greenalls - Clasico', 2],
                ['Gin Greenalls - Frutos Rojos', 2],
                ['Agua tónica 1 Lt', 1],
                ['Powerade', 2],
            ]],
            [12, $v2, $clientes['Lucía Vásquez'], 'qr', [
                ['Fernet Branca', 3],
                ['Miks Vodka Ice 275 ml', 2],
                ['CAMELL 20 unidades', 1],
            ]],
            [10, $v1, null, 'efectivo', [
                ['Parqueo Coliseo', 2],
                ['Parqueo Piscina', 3],
            ]],
            [9, $v2, $clientes['Andrés Pinto'], 'efectivo', [
                ['Amstel 269ml', 5],
                ['Bacardi Gold', 3],
                ['Sprite 500ml', 3],
            ]],
            [7, $v1, $clientes['Juan Pablo Quispe'], 'qr', [
                ['PACK HIDRATACION - 12Aguas/12Powerade', 1],
            ]],
            [6, $admin, null, 'efectivo', [
                ['Tequila Jarana', 4],
                ['Jägermeister', 3],
                ['Coca Cola 500 ml', 4],
            ]],
            [5, $v2, $clientes['María Fernanda Coca'], 'transferencia', [
                ['Botella Jägermeister', 1],
                ['Red Bull 250ml', 2],
            ]],
            [4, $v1, $clientes['Valentina Herrera'], 'qr', [
                ['Singani Casa Real', 2],
                ['Negroni', 2],
                ['Moscow Mule', 1],
                ['Fanta 500 ml', 2],
            ]],
            [2, $v2, $clientes['Diego Mamani'], 'efectivo', [
                ['Mojito', 3],
                ['Martini Spritz', 2],
                ['LM 20 unidades', 1],
                ['Ginger Ale 1.5 Lt', 2],
            ]],
            [1, $admin, $clientes['Lucía Vásquez'], 'qr', [
                ['Amstel 269ml', 6],
                ['Vodka', 2],
                ['Coca Cola Zero 500 ml', 2],
                ['Parqueo Coliseo', 1],
            ]],
        ];

        foreach ($ventas as $idx => [$diasAtras, $vendedor, $cliente, $metodo, $items]) {
            $fecha  = Carbon::now()->subDays($diasAtras)->setTime(rand(18, 23), rand(0, 59));
            $codigo = 'VTA-' . $this->tid . $fecha->format('ymdHis') . str_pad($idx + 1, 2, '0', STR_PAD_LEFT);

            $total = 0;
            foreach ($items as [$nombre, $cant]) {
                $total += $prods[$nombre]->precio * $cant;
            }

            $venta = Venta::create([
                'tienda_id'        => $this->tid,
                'vendedor_id'      => $vendedor->id,
                'codigo_pedido'    => $codigo,
                'cliente_id'       => $cliente?->id,
                'cliente_nombre'   => $cliente?->nombre ?? 'Cliente genérico',
                'cliente_telefono' => $cliente?->telefono,
                'cliente_email'    => $cliente?->email,
                'cliente_nit'      => $cliente?->ci_nit,
                'total'            => $total,
                'metodo_pago'      => $metodo,
                'estado_venta'     => 'Completada',
            ]);

            DB::table('ventas')->where('id', $venta->id)
                ->update(['created_at' => $fecha, 'updated_at' => $fecha]);

            foreach ($items as [$nombre, $cant]) {
                $p = $prods[$nombre];

                DetalleVenta::create([
                    'venta_id'        => $venta->id,
                    'producto_id'     => $p->id,
                    'cantidad'        => $cant,
                    'precio_unitario' => $p->precio,
                ]);

                // Reducir stock
                $nuevoStock = max(0, $p->stock - $cant);
                $p->update([
                    'stock'  => $nuevoStock,
                    'estado' => $nuevoStock <= 0 ? 'Agotado' : 'Disponible',
                ]);

                // Movimiento salida
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
        }
    }
}
