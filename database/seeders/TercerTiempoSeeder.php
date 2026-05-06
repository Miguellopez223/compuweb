<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tienda;
use App\Models\User;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TercerTiempoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear la nueva Tienda
        $tienda = Tienda::create([
            'nombre' => 'Tercer Tiempo',
            'slug' => Str::slug('Tercer Tiempo'),
            'estado' => 1,
        ]);

        // 2. Crear el Usuario Admin
        User::create([
            'tienda_id' => $tienda->id,
            'name' => 'Alejandro Rodriguez',
            'email' => 'alejandro@tercertiempo.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'visible_catalogo' => 1,
        ]);

        // 3. Definir Categorías y Productos de la imagen
        $data = [
            'Cervezas' => [
                ['nombre' => 'Amstel 269ml', 'precio' => 10.00],
            ],
            'Tragos por Vasos' => [
                ['nombre' => 'Tequila Jarana', 'precio' => 15.00],
                ['nombre' => 'Jägermeister', 'precio' => 15.00],
                ['nombre' => 'Bacardi Gold', 'precio' => 25.00],
                ['nombre' => 'Gin Greenalls - Clasico', 'precio' => 25.00],
                ['nombre' => 'Gin Greenalls - Frutos Rojos', 'precio' => 25.00],
                ['nombre' => 'Fernet Branca', 'precio' => 25.00],
                ['nombre' => 'Vodka', 'precio' => 25.00],
                ['nombre' => 'Singani Casa Real', 'precio' => 25.00],
                ['nombre' => 'Mojito', 'precio' => 25.00],
                ['nombre' => 'Martini Spritz', 'precio' => 25.00],
                ['nombre' => 'Moscow Mule', 'precio' => 25.00],
                ['nombre' => 'Negroni', 'precio' => 25.00],
            ],
            'Otros Tragos' => [
                ['nombre' => 'Miks Vodka Ice 275 ml', 'precio' => 25.00],
            ],
            'Tragos en COMBO' => [
                ['nombre' => 'Botella Ron + 1 Coca Cola 2 Lts', 'precio' => 250.00],
                ['nombre' => 'Botella Tequila + Limón & Sal', 'precio' => 250.00],
                ['nombre' => 'Botella Jägermeister', 'precio' => 280.00],
                ['nombre' => 'Botella Gin + 1 Agua Tónica 1Lt', 'precio' => 280.00],
                ['nombre' => 'Botella Fernet + 1 Coca Cola 2 Lts', 'precio' => 250.00],
                ['nombre' => 'Botella Singani + 1 Ginger 1,5 Lt', 'precio' => 210.00],
            ],
            'Hidratantes' => [
                ['nombre' => 'Powerade', 'precio' => 12.00],
                ['nombre' => 'Agua sin gas 500 ml', 'precio' => 10.00],
            ],
            'Energizantes' => [
                ['nombre' => 'Red Bull 250ml', 'precio' => 15.00],
            ],
            'Gaseosas 500 ml' => [
                ['nombre' => 'Coca Cola 500 ml', 'precio' => 10.00],
                ['nombre' => 'Coca Cola Zero 500 ml', 'precio' => 10.00],
                ['nombre' => 'Sprite 500ml', 'precio' => 10.00],
                ['nombre' => 'Fanta 500 ml', 'precio' => 10.00],
            ],
            'Bebidas para Tragos' => [
                ['nombre' => 'Ginger Ale 1.5 Lt', 'precio' => 20.00],
                ['nombre' => 'Agua tónica 1 Lt', 'precio' => 20.00],
            ],
            'Cigarros' => [
                ['nombre' => 'CAMELL 20 unidades', 'precio' => 25.00],
                ['nombre' => 'LM 20 unidades', 'precio' => 25.00],
            ],
            'Packs' => [
                ['nombre' => 'PACK HIDRATACION - 12Aguas/12Powerade', 'precio' => 240.00],
            ],
            'Parqueos' => [
                ['nombre' => 'Parqueo Coliseo', 'precio' => 5.00],
                ['nombre' => 'Parqueo Piscina', 'precio' => 5.00],
            ],
        ];

        // 4. Insertar en la Base de Datos
        foreach ($data as $catNombre => $productos) {
            $categoria = Categoria::create([
                'tienda_id' => $tienda->id,
                'nombre' => $catNombre,
            ]);

            foreach ($productos as $prod) {
                Producto::create([
                    'tienda_id' => $tienda->id,
                    'categoria_id' => $categoria->id,
                    'nombre' => $prod['nombre'],
                    'precio' => $prod['precio'],
                    'precio_costo' => 0.00,
                    'stock' => 100,
                    'stock_minimo' => 5,
                    'unidad_medida' => 'unidad',
                    'estado' => 'Disponible',
                ]);
            }
        }
    }
}
