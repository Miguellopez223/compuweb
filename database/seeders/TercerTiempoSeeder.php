<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\DetalleVenta;
use App\Models\MovimientoInventario;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Tienda;
use App\Models\User;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TercerTiempoSeeder extends Seeder
{
    private int $tid;

    public function run(): void
    {
        $tienda = Tienda::withoutGlobalScopes()
            ->firstOrCreate(
                ['slug' => 'tercertiempo'],
                [
                    'nombre'             => 'Tercer Tiempo',
                    'slug'               => 'tercertiempo',
                    'telefono_principal' => '59171234567',
                    'estado'             => true,
                ]
            );

        $this->tid = $tienda->id;

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
                ['nombre' => 'Amstel 269ml',                         'precio' =>  10.00, 'costo' =>  6.00, 'stock' =>  48, 'imagen' => 'uploads/productos/69fcb944381be_7802100005526.webp'],
            ],
            'Tragos por Vasos' => [
                ['nombre' => 'Tequila Jarana',                       'precio' =>  15.00, 'costo' =>  8.00, 'stock' => 100, 'imagen' => 'uploads/productos/69fcb9550aa23_JARANA-REPOSADO_500x.jpg'],
                ['nombre' => 'Jägermeister',                         'precio' =>  15.00, 'costo' =>  9.00, 'stock' =>  80, 'imagen' => 'uploads/productos/69fcb972190f1_espirituoso-jagermeister-700ml-espirituoso-jagermeister-700ml.jpg'],
                ['nombre' => 'Bacardi Gold',                         'precio' =>  25.00, 'costo' => 14.00, 'stock' =>  80, 'imagen' => 'uploads/productos/69fcb97e96269_Bacardi-Gold-Rum-1L.webp'],
                ['nombre' => 'Gin Greenalls - Clasico',               'precio' =>  25.00, 'costo' => 14.00, 'stock' =>  60, 'imagen' => 'uploads/productos/69fcb9b009762_5010296002980.webp'],
                ['nombre' => 'Gin Greenalls - Frutos Rojos',          'precio' =>  25.00, 'costo' => 14.00, 'stock' =>  60, 'imagen' => 'uploads/productos/69fcb9bd9fc36_greenalls-wild-berry-pink-gin.jpg.webp'],
                ['nombre' => 'Fernet Branca',                        'precio' =>  25.00, 'costo' => 15.00, 'stock' =>  70, 'imagen' => 'uploads/productos/69fcb9ce7c661_1220001.webp'],
                ['nombre' => 'Vodka',                                'precio' =>  25.00, 'costo' => 13.00, 'stock' =>  80, 'imagen' => 'uploads/productos/69fcb9da704a8_7312040017683_1024x1024.webp'],
                ['nombre' => 'Singani Casa Real',                    'precio' =>  25.00, 'costo' => 13.00, 'stock' =>  70, 'imagen' => 'uploads/productos/69fcb9e66643d_images (1).jpg'],
                ['nombre' => 'Mojito',                               'precio' =>  25.00, 'costo' => 12.00, 'stock' =>  60, 'imagen' => 'uploads/productos/69fcb9f268ba4_istockphoto-542212406-612x612.jpg'],
                ['nombre' => 'Martini Spritz',                       'precio' =>  25.00, 'costo' => 13.00, 'stock' =>  60, 'imagen' => 'uploads/productos/69fcba03bdfc5_Fiero-Spritz--1200x1500.png'],
                ['nombre' => 'Moscow Mule',                          'precio' =>  25.00, 'costo' => 13.00, 'stock' =>  60, 'imagen' => 'uploads/productos/69fcba107a9f4_images (2).jpg'],
                ['nombre' => 'Negroni',                              'precio' =>  25.00, 'costo' => 14.00, 'stock' =>  50, 'imagen' => 'uploads/productos/69fcba19da28f_mezcal-negroni-1500x1500-primary-6f6c472050a949c8a55aa07e1b5a2d1b.jpg'],
            ],
            'Otros Tragos' => [
                ['nombre' => 'Miks Vodka Ice 275 ml',                'precio' =>  25.00, 'costo' => 15.00, 'stock' =>  24, 'imagen' => 'uploads/productos/69fcba2b17075_images (3).jpg'],
            ],
            'Tragos en COMBO' => [
                ['nombre' => 'Botella Ron + 1 Coca Cola 2 Lts',      'precio' => 250.00, 'costo' => 160.00, 'stock' =>  8, 'imagen' => 'uploads/productos/69fcba3bb2006_0301-031003_580x.jpg'],
                ['nombre' => 'Botella Tequila + Limón & Sal',        'precio' => 250.00, 'costo' => 155.00, 'stock' =>  6, 'imagen' => 'uploads/productos/69fcba4fcd302_botella-y-vaso-de-tequila-bebida-mexicana-servida-con-sal-limón-217911916.webp'],
                ['nombre' => 'Botella Jägermeister',                 'precio' => 280.00, 'costo' => 185.00, 'stock' =>  5, 'imagen' => 'uploads/productos/69fcba5e41b71_image-proxy.webp'],
                ['nombre' => 'Botella Gin + 1 Agua Tónica 1Lt',      'precio' => 280.00, 'costo' => 180.00, 'stock' =>  5, 'imagen' => 'uploads/productos/69fcba9a3241e_vineria_san_juan_gin_burnets_sierra_de_los_padres_tonica-copia-9e79d93752d3b24bbd17464542056657-1024-1024.webp'],
                ['nombre' => 'Botella Fernet + 1 Coca Cola 2 Lts',   'precio' => 250.00, 'costo' => 158.00, 'stock' =>  7, 'imagen' => 'uploads/productos/69fcbaa671ce2_68486f196d80973ae1ac852c.jpg'],
                ['nombre' => 'Botella Singani + 1 Ginger 1,5 Lt',    'precio' => 210.00, 'costo' => 130.00, 'stock' =>  8, 'imagen' => 'uploads/productos/69fcbab04501c_65c4ebf16a823bc476968acf.jpg'],
            ],
            'Hidratantes' => [
                ['nombre' => 'Powerade',                             'precio' =>  12.00, 'costo' =>  7.00, 'stock' =>  24, 'imagen' => 'uploads/productos/69fcbabf18c5c_images (4).jpg'],
                ['nombre' => 'Agua sin gas 500 ml',                  'precio' =>  10.00, 'costo' =>  4.00, 'stock' =>  36, 'imagen' => 'uploads/productos/69fcbacaefc7d_image-proxy (1).webp'],
            ],
            'Energizantes' => [
                ['nombre' => 'Red Bull 250ml',                       'precio' =>  15.00, 'costo' =>  9.00, 'stock' =>  24, 'imagen' => 'uploads/productos/69fcbad874deb_564111_700x700.webp'],
            ],
            'Gaseosas 500 ml' => [
                ['nombre' => 'Coca Cola 500 ml',                     'precio' =>  10.00, 'costo' =>  5.00, 'stock' =>  48, 'imagen' => 'uploads/productos/69fcbaea40198_909698_51f94965-bbe4-484f-8141-b33901a6e979_1200x1200.webp'],
                ['nombre' => 'Coca Cola Zero 500 ml',                'precio' =>  10.00, 'costo' =>  5.00, 'stock' =>  24, 'imagen' => 'uploads/productos/69fcbaf46f6dd_images (5).jpg'],
                ['nombre' => 'Sprite 500ml',                         'precio' =>  10.00, 'costo' =>  5.00, 'stock' =>  36, 'imagen' => 'uploads/productos/69fcbafec62aa_516276-800-auto.webp'],
                ['nombre' => 'Fanta 500 ml',                         'precio' =>  10.00, 'costo' =>  5.00, 'stock' =>  24, 'imagen' => 'uploads/productos/69fcbb0ed9cb3_111686_Primary.webp'],
            ],
            'Bebidas para Tragos' => [
                ['nombre' => 'Ginger Ale 1.5 Lt',                   'precio' =>  20.00, 'costo' => 12.00, 'stock' =>  12, 'imagen' => 'uploads/productos/69fcbb1b24a22_7771609004388-des2_c7edf40e-1043-4efb-b630-234fc8f94df0_700x700.webp'],
                ['nombre' => 'Agua tónica 1 Lt',                    'precio' =>  20.00, 'costo' => 11.00, 'stock' =>  12, 'imagen' => 'uploads/productos/69fcbb25a5a1c_7772115420655_1200x1200.webp'],
            ],
            'Cigarros' => [
                ['nombre' => 'CAMELL 20 unidades',                   'precio' =>  25.00, 'costo' => 18.00, 'stock' =>  20, 'imagen' => 'uploads/productos/69fcbb317856c_40329055_1200x1200.webp'],
                ['nombre' => 'LM 20 unidades',                       'precio' =>  25.00, 'costo' => 17.00, 'stock' =>  20, 'imagen' => 'uploads/productos/69fcbb3e2ef30_77766755_700x700.webp'],
            ],
            'Packs' => [
                ['nombre' => 'PACK HIDRATACION - 12Aguas/12Powerade','precio' => 240.00, 'costo' => 154.00,'stock' =>   5, 'imagen' => 'uploads/productos/69fcbb4c690d1_98308_01.webp'],
            ],
            'Parqueos' => [
                ['nombre' => 'Parqueo Coliseo',                      'precio' =>   5.00, 'costo' =>  0.00, 'stock' => 999, 'imagen' => 'uploads/productos/69fcbb59b8f6f_depositphotos_130360542-stock-illustration-vector-illustration-of-the-parking.jpg'],
                ['nombre' => 'Parqueo Piscina',                      'precio' =>   5.00, 'costo' =>  0.00, 'stock' => 999, 'imagen' => 'uploads/productos/69fcbb62ab2f4_depositphotos_130360542-stock-illustration-vector-illustration-of-the-parking.jpg'],
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
                    'imagen'        => $item['imagen'] ?? null,
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
