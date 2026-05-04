<?php

namespace App\Livewire\Productos;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Productos')]
class Index extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public string $filtroEstado = '';
    public string $filtroCategoria = '';
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $deleteId = null;

    // Form fields
    public string $nombre = '';
    public string $sku = '';
    public string $descripcion = '';
    public string $precio = '';
    public int $stock = 0;
    public int $stock_minimo = 5;
    public string $estado = 'Disponible';
    public ?int $categoria_id = null;
    public $imagen = null;
    public ?string $imagenActual = null;
    public string $imagenUrl = '';  // URL externa de imagen

    protected function rules(): array
    {
        return [
            'nombre'       => 'required|string|max:255',
            'sku'          => 'nullable|string|max:100',
            'descripcion'  => 'nullable|string',
            'precio'       => 'required|numeric|min:0',
            'stock'        => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'estado'       => 'required|in:Disponible,Agotado',
            'categoria_id' => 'required|exists:categorias,id',
            'imagen'       => 'nullable|image|max:2048',
            'imagenUrl'    => 'nullable|url',
        ];
    }

    protected $messages = [
        'nombre.required'       => 'El nombre es obligatorio.',
        'precio.required'       => 'El precio es obligatorio.',
        'precio.numeric'        => 'El precio debe ser un número.',
        'stock.required'        => 'El stock es obligatorio.',
        'categoria_id.required' => 'Selecciona una categoría.',
        'imagen.image'          => 'El archivo debe ser una imagen.',
        'imagen.max'            => 'La imagen no puede superar 2 MB.',
        'imagenUrl.url'         => 'Debe ser una URL válida (https://...).',
    ];

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFiltroEstado(): void { $this->resetPage(); }
    public function updatingFiltroCategoria(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        $this->reset(['nombre', 'sku', 'descripcion', 'precio', 'imagen', 'imagenActual', 'imagenUrl', 'editingId']);
        $this->stock = 0;
        $this->stock_minimo = 5;
        $this->estado = 'Disponible';
        $this->categoria_id = null;
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
        $this->imagen       = null;

        // Si la imagen guardada es una URL externa, cargarla en imagenUrl
        if ($p->imagen && str_starts_with($p->imagen, 'http')) {
            $this->imagenActual = null;
            $this->imagenUrl    = $p->imagen;
        } else {
            $this->imagenActual = $p->imagen;
            $this->imagenUrl    = '';
        }

        $this->showModal = true;
    }

    public function removeImagen(): void
    {
        $this->imagen       = null;
        $this->imagenActual = null;
        $this->imagenUrl    = '';
    }

    public function save(): void
    {
        $this->validate();

        // Prioridad: archivo subido > URL > imagen actual
        if ($this->imagen) {
            // Eliminar archivo anterior si era un path local
            if ($this->imagenActual && !str_starts_with($this->imagenActual, 'http')) {
                Storage::disk('public')->delete($this->imagenActual);
            }
            $imagenPath = $this->imagen->store('productos', 'public');
        } elseif ($this->imagenUrl) {
            $imagenPath = $this->imagenUrl;
        } else {
            $imagenPath = $this->imagenActual;
        }

        $data = [
            'tienda_id'    => auth()->user()->tienda_id,
            'categoria_id' => $this->categoria_id,
            'nombre'       => $this->nombre,
            'sku'          => $this->sku ?: null,
            'descripcion'  => $this->descripcion ?: null,
            'imagen'       => $imagenPath,
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
        $this->reset(['nombre', 'sku', 'descripcion', 'precio', 'imagen', 'imagenActual', 'imagenUrl', 'editingId']);
        $this->stock = 0;
        $this->stock_minimo = 5;
        $this->estado = 'Disponible';
        $this->categoria_id = null;
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        $producto = Producto::findOrFail($this->deleteId);
        // Solo borrar del disco si es un archivo local (no URL)
        if ($producto->imagen && !str_starts_with($producto->imagen, 'http')) {
            Storage::disk('public')->delete($producto->imagen);
        }
        $producto->delete();
        $this->showDeleteModal = false;
        $this->deleteId = null;
        session()->flash('success', 'Producto eliminado.');
    }

    public function render()
    {
        $productos = Producto::with('categoria')
            ->when($this->search, fn($q) => $q->where(function ($q) {
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

    // Helper para generar la URL de display de la imagen
    public function imagenDisplayUrl(?string $imagen): ?string
    {
        if (!$imagen) return null;
        if (str_starts_with($imagen, 'http')) return $imagen;
        return Storage::url($imagen);
    }
}
