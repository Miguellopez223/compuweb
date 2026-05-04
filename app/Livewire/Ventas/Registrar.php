<?php

namespace App\Livewire\Ventas;

use App\Models\Categoria;
use App\Models\DetalleVenta;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.admin')]
#[Title('Registrar Venta')]
class Registrar extends Component
{
    public string $busqueda = '';
    public string $filtroCategoria = '';
    public array $carrito = [];

    public string $cliente_nombre = '';
    public string $cliente_telefono = '';
    public string $metodo_pago = 'efectivo';

    public bool $showSuccess = false;
    public ?int $ventaId = null;

    protected $rules = [
        'cliente_nombre' => 'required|string|max:255',
        'cliente_telefono' => 'nullable|string|max:20',
        'metodo_pago'    => 'required|in:efectivo,qr,transferencia',
    ];

    protected $messages = [
        'cliente_nombre.required' => 'El nombre del cliente es obligatorio.',
    ];

    public function agregarProducto(int $productoId): void
    {
        $producto = Producto::findOrFail($productoId);

        if ($producto->estado === 'Agotado' || $producto->stock <= 0) {
            session()->flash('error', "'{$producto->nombre}' está agotado.");
            return;
        }

        $enCarrito = array_sum(array_column(
            array_filter($this->carrito, fn($i) => $i['producto_id'] === $productoId),
            'cantidad'
        ));

        if ($enCarrito >= $producto->stock) {
            session()->flash('error', "No hay más stock disponible de '{$producto->nombre}'.");
            return;
        }

        foreach ($this->carrito as &$item) {
            if ($item['producto_id'] === $productoId) {
                $item['cantidad']++;
                $item['subtotal'] = $item['cantidad'] * $item['precio'];
                return;
            }
        }

        $this->carrito[] = [
            'producto_id' => $producto->id,
            'nombre'      => $producto->nombre,
            'precio'      => (float) $producto->precio,
            'cantidad'    => 1,
            'subtotal'    => (float) $producto->precio,
            'stock_max'   => $producto->stock,
        ];
    }

    public function cambiarCantidad(int $index, int $cantidad): void
    {
        if (!isset($this->carrito[$index])) return;

        if ($cantidad <= 0) {
            $this->quitarItem($index);
            return;
        }

        $maxStock = $this->carrito[$index]['stock_max'];
        $cantidad = min($cantidad, $maxStock);
        $this->carrito[$index]['cantidad'] = $cantidad;
        $this->carrito[$index]['subtotal'] = $cantidad * $this->carrito[$index]['precio'];
    }

    public function quitarItem(int $index): void
    {
        unset($this->carrito[$index]);
        $this->carrito = array_values($this->carrito);
    }

    public function getTotal(): float
    {
        return array_sum(array_column($this->carrito, 'subtotal'));
    }

    public function confirmarVenta(): void
    {
        if (empty($this->carrito)) {
            session()->flash('error', 'Agrega al menos un producto al carrito.');
            return;
        }

        $this->validate();

        DB::transaction(function () {
            $venta = Venta::create([
                'tienda_id'        => auth()->user()->tienda_id,
                'vendedor_id'      => auth()->id(),
                'cliente_nombre'   => $this->cliente_nombre,
                'cliente_telefono' => $this->cliente_telefono ?: null,
                'total'            => $this->getTotal(),
                'metodo_pago'      => $this->metodo_pago,
                'estado_venta'     => 'Completada',
            ]);

            foreach ($this->carrito as $item) {
                DetalleVenta::create([
                    'venta_id'       => $venta->id,
                    'producto_id'    => $item['producto_id'],
                    'cantidad'       => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                ]);

                $producto = Producto::find($item['producto_id']);
                if ($producto) {
                    $nuevoStock = max(0, $producto->stock - $item['cantidad']);
                    $producto->update([
                        'stock'  => $nuevoStock,
                        'estado' => $nuevoStock <= 0 ? 'Agotado' : 'Disponible',
                    ]);

                    MovimientoInventario::create([
                        'tienda_id'   => auth()->user()->tienda_id,
                        'producto_id' => $producto->id,
                        'user_id'     => auth()->id(),
                        'tipo'        => 'salida',
                        'cantidad'    => $item['cantidad'],
                        'referencia'  => 'Venta #'.$venta->id,
                    ]);
                }
            }

            $this->ventaId = $venta->id;
        });

        $this->carrito = [];
        $this->reset(['cliente_nombre', 'cliente_telefono', 'metodo_pago', 'busqueda']);
        $this->metodo_pago = 'efectivo';
        $this->showSuccess = true;
    }

    public function render()
    {
        $productos = Producto::with('categoria')
            ->where('estado', 'Disponible')
            ->where('stock', '>', 0)
            ->when($this->busqueda, fn($q) => $q->where(function ($q) {
                $q->where('nombre', 'like', '%'.$this->busqueda.'%')
                  ->orWhere('sku', 'like', '%'.$this->busqueda.'%');
            }))
            ->when($this->filtroCategoria, fn($q) => $q->where('categoria_id', $this->filtroCategoria))
            ->orderBy('nombre')
            ->get();

        $categorias = Categoria::all();

        return view('livewire.ventas.registrar', [
            'productos'  => $productos,
            'categorias' => $categorias,
            'total'      => $this->getTotal(),
        ]);
    }
}
