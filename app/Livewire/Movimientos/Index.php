<?php

namespace App\Livewire\Movimientos;

use App\Models\MovimientoInventario;
use App\Models\Producto;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Movimientos de Inventario')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filtroTipo = '';
    public bool $showModal = false;

    public ?int $producto_id = null;
    public int $cantidad = 1;
    public string $precio_unitario = '';
    public string $proveedor = '';
    public string $referencia = '';

    protected $rules = [
        'producto_id'    => 'required|exists:productos,id',
        'cantidad'       => 'required|integer|min:1',
        'precio_unitario' => 'nullable|numeric|min:0',
        'proveedor'      => 'nullable|string|max:255',
        'referencia'     => 'nullable|string|max:255',
    ];

    protected $messages = [
        'producto_id.required' => 'Selecciona un producto.',
        'cantidad.required'    => 'La cantidad es obligatoria.',
        'cantidad.min'         => 'La cantidad debe ser al menos 1.',
    ];

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFiltroTipo(): void { $this->resetPage(); }

    public function openEntrada(): void
    {
        $this->reset(['producto_id', 'cantidad', 'precio_unitario', 'proveedor', 'referencia']);
        $this->cantidad = 1;
        $this->showModal = true;
    }

    public function registrarEntrada(): void
    {
        $this->validate();

        $producto = Producto::findOrFail($this->producto_id);
        $nuevoStock = $producto->stock + $this->cantidad;

        MovimientoInventario::create([
            'tienda_id'      => auth()->user()->tienda_id,
            'producto_id'    => $this->producto_id,
            'user_id'        => auth()->id(),
            'tipo'           => 'entrada',
            'cantidad'       => $this->cantidad,
            'precio_unitario' => $this->precio_unitario ?: null,
            'proveedor'      => $this->proveedor ?: null,
            'referencia'     => $this->referencia ?: null,
        ]);

        $producto->update([
            'stock'  => $nuevoStock,
            'estado' => $nuevoStock > 0 ? 'Disponible' : 'Agotado',
        ]);

        $this->showModal = false;
        $this->reset(['producto_id', 'cantidad', 'precio_unitario', 'proveedor', 'referencia']);
        session()->flash('success', "Entrada registrada. Stock actualizado a {$nuevoStock} unidades.");
    }

    public function render()
    {
        $movimientos = MovimientoInventario::with(['producto', 'user'])
            ->when($this->search, fn($q) => $q->whereHas('producto', fn($q) =>
                $q->where('nombre', 'like', '%'.$this->search.'%')
            ))
            ->when($this->filtroTipo, fn($q) => $q->where('tipo', $this->filtroTipo))
            ->latest()
            ->paginate(15);

        $productos = Producto::orderBy('nombre')->get();

        return view('livewire.movimientos.index', compact('movimientos', 'productos'));
    }
}
