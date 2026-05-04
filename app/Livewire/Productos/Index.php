<?php

namespace App\Livewire\Productos;

use App\Models\Categoria;
use App\Models\Producto;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Productos')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filtroEstado = '';
    public string $filtroCategoria = '';
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $deleteId = null;

    public string $nombre = '';
    public string $sku = '';
    public string $descripcion = '';
    public string $precio = '';
    public int $stock = 0;
    public int $stock_minimo = 5;
    public string $estado = 'Disponible';
    public ?int $categoria_id = null;

    protected $rules = [
        'nombre'       => 'required|string|max:255',
        'sku'          => 'nullable|string|max:100',
        'descripcion'  => 'nullable|string',
        'precio'       => 'required|numeric|min:0',
        'stock'        => 'required|integer|min:0',
        'stock_minimo' => 'required|integer|min:0',
        'estado'       => 'required|in:Disponible,Agotado',
        'categoria_id' => 'required|exists:categorias,id',
    ];

    protected $messages = [
        'nombre.required'    => 'El nombre es obligatorio.',
        'precio.required'    => 'El precio es obligatorio.',
        'precio.numeric'     => 'El precio debe ser un número.',
        'stock.required'     => 'El stock es obligatorio.',
        'categoria_id.required' => 'Selecciona una categoría.',
    ];

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFiltroEstado(): void { $this->resetPage(); }
    public function updatingFiltroCategoria(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        $this->reset(['nombre', 'sku', 'descripcion', 'precio', 'stock', 'stock_minimo', 'estado', 'categoria_id', 'editingId']);
        $this->stock_minimo = 5;
        $this->estado = 'Disponible';
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $p = Producto::findOrFail($id);
        $this->editingId    = $p->id;
        $this->nombre       = $p->nombre;
        $this->sku          = $p->sku ?? '';
        $this->descripcion  = $p->descripcion ?? '';
        $this->precio       = $p->precio;
        $this->stock        = $p->stock;
        $this->stock_minimo = $p->stock_minimo;
        $this->estado       = $p->estado;
        $this->categoria_id = $p->categoria_id;
        $this->showModal    = true;
    }

    public function save(): void
    {
        $this->validate();
        $data = [
            'tienda_id'    => auth()->user()->tienda_id,
            'categoria_id' => $this->categoria_id,
            'nombre'       => $this->nombre,
            'sku'          => $this->sku ?: null,
            'descripcion'  => $this->descripcion ?: null,
            'precio'       => $this->precio,
            'stock'        => $this->stock,
            'stock_minimo' => $this->stock_minimo,
            'estado'       => $this->stock == 0 ? 'Agotado' : $this->estado,
        ];

        if ($this->editingId) {
            Producto::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Producto actualizado.');
        } else {
            Producto::create($data);
            session()->flash('success', 'Producto creado.');
        }
        $this->showModal = false;
        $this->reset(['nombre', 'sku', 'descripcion', 'precio', 'stock', 'stock_minimo', 'estado', 'categoria_id', 'editingId']);
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        Producto::findOrFail($this->deleteId)->delete();
        $this->showDeleteModal = false;
        $this->deleteId = null;
        session()->flash('success', 'Producto eliminado.');
    }

    public function render()
    {
        $productos = Producto::with('categoria')
            ->when($this->search, fn($q) => $q->where(function($q) {
                $q->where('nombre', 'like', '%'.$this->search.'%')
                  ->orWhere('sku', 'like', '%'.$this->search.'%');
            }))
            ->when($this->filtroEstado, fn($q) => $q->where('estado', $this->filtroEstado))
            ->when($this->filtroCategoria, fn($q) => $q->where('categoria_id', $this->filtroCategoria))
            ->latest()
            ->paginate(12);

        $categorias = Categoria::all();

        return view('livewire.productos.index', compact('productos', 'categorias'));
    }
}
