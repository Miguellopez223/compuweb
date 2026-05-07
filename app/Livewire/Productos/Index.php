<?php

namespace App\Livewire\Productos;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\UnidadMedida;
use Illuminate\Support\Facades\File;
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
    public string $unidad_medida = 'unidad';
    public $imagen = null;
    public ?string $imagenActual = null;
    public string $imagenUrl = '';
    public array $atributos = [];

    // Unidad de medida CRUD
    public bool $showUnidadModal = false;
    public ?int $editingUnidadId = null;
    public string $unidadNombre = '';
    public string $unidadAbreviatura = '';

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
            'categoria_id'  => 'required|exists:categorias,id',
            'unidad_medida' => 'required|string|max:50',
            'imagen'        => 'nullable|image|max:2048',
            'imagenUrl'     => 'nullable|url',
            'atributos.*.nombre' => 'nullable|string|max:100',
            'atributos.*.valor'  => 'nullable|string|max:255',
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
        $this->reset(['nombre', 'sku', 'descripcion', 'precio', 'imagen', 'imagenActual', 'imagenUrl', 'editingId', 'atributos']);
        $this->stock = 0;
        $this->stock_minimo = 5;
        $this->estado = 'Disponible';
        $this->unidad_medida = 'unidad';
        $this->categoria_id = null;
        $this->showModal = true;
    }

    public function addAtributo(): void
    {
        $this->atributos[] = ['nombre' => '', 'valor' => ''];
    }

    public function removeAtributo(int $index): void
    {
        unset($this->atributos[$index]);
        $this->atributos = array_values($this->atributos);
    }

    public function openEdit(int $id): void
    {
        $p = Producto::with('atributos')->findOrFail($id);
        $this->editingId     = $p->id;
        $this->nombre        = $p->nombre;
        $this->sku           = $p->sku ?? '';
        $this->descripcion   = $p->descripcion ?? '';
        $this->precio        = $p->precio;
        $this->stock         = $p->stock;
        $this->stock_minimo  = $p->stock_minimo;
        $this->estado        = $p->estado;
        $this->unidad_medida = $p->unidad_medida;
        $this->categoria_id  = $p->categoria_id;
        $this->imagen        = null;
        $this->atributos     = $p->atributos->map(fn($a) => ['nombre' => $a->nombre, 'valor' => $a->valor])->toArray();

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
            if ($this->imagenActual && !str_starts_with($this->imagenActual, 'http')) {
                File::delete(public_path($this->imagenActual));
            }
            $dir = public_path('uploads/productos');
            File::ensureDirectoryExists($dir);
            $filename = uniqid() . '_' . $this->imagen->getClientOriginalName();
            File::copy($this->imagen->getRealPath(), $dir . DIRECTORY_SEPARATOR . $filename);
            $imagenPath = 'uploads/productos/' . $filename;
        } elseif ($this->imagenUrl) {
            $imagenPath = $this->imagenUrl;
        } else {
            $imagenPath = $this->imagenActual;
        }

        $data = [
            'tienda_id'     => auth()->user()->tienda_id,
            'categoria_id'  => $this->categoria_id,
            'nombre'        => $this->nombre,
            'sku'           => $this->sku ?: null,
            'descripcion'   => $this->descripcion ?: null,
            'imagen'        => $imagenPath,
            'precio'        => $this->precio,
            'stock'         => $this->stock,
            'stock_minimo'  => $this->stock_minimo,
            'unidad_medida' => $this->unidad_medida,
            'estado'        => $this->stock == 0 ? 'Agotado' : $this->estado,
        ];

        if ($this->editingId) {
            $producto = Producto::findOrFail($this->editingId);
            $producto->update($data);
        } else {
            $producto = Producto::create($data);
        }

        $producto->atributos()->delete();
        $validAttrs = array_filter($this->atributos, fn($a) => !empty($a['nombre']) && !empty($a['valor']));
        foreach ($validAttrs as $attr) {
            $producto->atributos()->create([
                'nombre' => $attr['nombre'],
                'valor'  => $attr['valor'],
            ]);
        }

        session()->flash('success', $this->editingId ? 'Producto actualizado.' : 'Producto creado.');

        $this->showModal = false;
        $this->reset(['nombre', 'sku', 'descripcion', 'precio', 'imagen', 'imagenActual', 'imagenUrl', 'editingId', 'atributos']);
        $this->stock = 0;
        $this->stock_minimo = 5;
        $this->estado = 'Disponible';
        $this->unidad_medida = 'unidad';
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
        if ($producto->imagen && !str_starts_with($producto->imagen, 'http')) {
            File::delete(public_path($producto->imagen));
        }
        $producto->delete();
        $this->showDeleteModal = false;
        $this->deleteId = null;
        session()->flash('success', 'Producto eliminado.');
    }

    // ── Unidad de medida CRUD ─────────────────────

    public function openUnidadCreate(): void
    {
        $this->reset(['editingUnidadId', 'unidadNombre', 'unidadAbreviatura']);
        $this->showUnidadModal = true;
    }

    public function openUnidadEdit(int $id): void
    {
        $u = UnidadMedida::findOrFail($id);
        $this->editingUnidadId = $u->id;
        $this->unidadNombre = $u->nombre;
        $this->unidadAbreviatura = $u->abreviatura;
        $this->showUnidadModal = true;
    }

    public function saveUnidad(): void
    {
        $this->validate([
            'unidadNombre' => 'required|string|max:50',
            'unidadAbreviatura' => 'required|string|max:10',
        ], [
            'unidadNombre.required' => 'El nombre es obligatorio.',
            'unidadAbreviatura.required' => 'La abreviatura es obligatoria.',
        ]);

        $data = [
            'tienda_id' => auth()->user()->tienda_id,
            'nombre' => $this->unidadNombre,
            'abreviatura' => $this->unidadAbreviatura,
        ];

        if ($this->editingUnidadId) {
            UnidadMedida::findOrFail($this->editingUnidadId)->update($data);
        } else {
            UnidadMedida::create($data);
        }

        $this->showUnidadModal = false;
        $this->reset(['editingUnidadId', 'unidadNombre', 'unidadAbreviatura']);
    }

    public function deleteUnidad(int $id): void
    {
        $unidad = UnidadMedida::findOrFail($id);
        $usada = Producto::where('unidad_medida', $unidad->nombre)->exists();
        if ($usada) {
            session()->flash('unidad_error', 'No se puede eliminar, hay productos usando esta unidad.');
            return;
        }
        $unidad->delete();
    }

    // ── Render ──────────────────────────────────────

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
        $unidades = UnidadMedida::orderBy('nombre')->get();

        return view('livewire.productos.index', compact('productos', 'categorias', 'unidades'));
    }

    public function imagenDisplayUrl(?string $imagen): ?string
    {
        if (!$imagen) return null;
        if (str_starts_with($imagen, 'http')) return $imagen;
        return asset($imagen);
    }
}
