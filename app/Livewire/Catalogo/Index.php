<?php

namespace App\Livewire\Catalogo;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\Tienda;
use App\Models\User;
use Livewire\Attributes\Url;
use Livewire\Component;

class Index extends Component
{
    public string $slug;
    public ?object $tienda = null;
    public bool $notFound = false;

    #[Url(as: 'q')]
    public string $busqueda = '';

    #[Url(as: 'cat')]
    public string $filtroCategoria = '';

    #[Url]
    public string $ordenar = 'nombre';

    public int $pagina = 1;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
        $this->tienda = Tienda::where('slug', $slug)->where('estado', true)->first();

        if (!$this->tienda) {
            $this->notFound = true;
        }
    }

    public function updatingBusqueda(): void        { $this->pagina = 1; }
    public function updatingFiltroCategoria(): void  { $this->pagina = 1; }
    public function updatingOrdenar(): void          { $this->pagina = 1; }

    public function paginar(int $pagina): void { $this->pagina = $pagina; }

    public function render()
    {
        $productos = collect();
        $categorias = collect();
        $meta = ['current_page' => 1, 'last_page' => 1, 'total' => 0];

        if (!$this->notFound) {
            $categorias = Categoria::withoutGlobalScopes()
                ->where('tienda_id', $this->tienda->id)
                ->orderBy('nombre')
                ->get();

            $query = Producto::withoutGlobalScopes()
                ->with(['categoria:id,nombre', 'atributos'])
                ->where('tienda_id', $this->tienda->id)
                ->where('estado', 'Disponible')
                ->where('stock', '>', 0);

            if ($this->busqueda) {
                $q = $this->busqueda;
                $query->where(fn($qb) =>
                    $qb->where('nombre', 'like', "%{$q}%")
                       ->orWhere('sku', 'like', "%{$q}%")
                );
            }

            if ($this->filtroCategoria) {
                $query->where('categoria_id', $this->filtroCategoria);
            }

            match ($this->ordenar) {
                'precio_asc'  => $query->orderBy('precio', 'asc'),
                'precio_desc' => $query->orderBy('precio', 'desc'),
                default       => $query->orderBy('nombre', 'asc'),
            };

            $paginated = $query->paginate(12, ['*'], 'page', $this->pagina);

            $productos = $paginated->getCollection();
            $meta = [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'total'        => $paginated->total(),
            ];
        }

        $vendedores = collect();
        if (!$this->notFound) {
            $vendedores = User::withoutGlobalScopes()
                ->where('tienda_id', $this->tienda->id)
                ->whereNotNull('whatsapp_number')
                ->where('whatsapp_number', '!=', '')
                ->select('id', 'name', 'whatsapp_number')
                ->orderBy('name')
                ->get();
        }

        return view('livewire.catalogo.index', compact('productos', 'categorias', 'meta'))
            ->layout('layouts.catalogo', [
                'tiendaNombre' => $this->tienda?->nombre ?? 'Catalogo',
                'tiendaPhone'  => $this->tienda?->telefono_principal ?? '',
                'slug'         => $this->slug,
                'vendedores'   => $vendedores,
            ]);
    }
}
