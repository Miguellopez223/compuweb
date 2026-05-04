<?php

namespace App\Livewire\Catalogo;

use Livewire\Attributes\Url;
use Livewire\Component;
use Illuminate\Support\Facades\Http;

class Index extends Component
{
    public string $slug;
    public array  $tienda     = [];
    public array  $categorias = [];
    public bool   $notFound   = false;
    public bool   $apiError   = false;

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
        $base       = rtrim(env('API_URL', 'http://compuweb-api.test'), '/');

        try {
            $tRes = Http::timeout(5)->get("{$base}/api/tiendas/{$slug}");
            if ($tRes->status() >= 400) {
                $this->notFound = true;
                return;
            }
            $this->tienda = $tRes->json();
        } catch (\Exception $e) {
            \Log::error("Catalogo: error cargando tienda [{$slug}]: " . $e->getMessage());
            $this->apiError = true;
            return;
        }

        try {
            $cRes = Http::timeout(5)->get("{$base}/api/tiendas/{$slug}/categorias");
            if ($cRes->successful()) {
                $this->categorias = $cRes->json();
            } else {
                \Log::warning("Catalogo: /categorias devolvió {$cRes->status()} para [{$slug}]");
                $this->categorias = [];
            }
        } catch (\Exception $e) {
            \Log::error("Catalogo: error cargando categorias [{$slug}]: " . $e->getMessage());
            $this->categorias = [];
        }
    }

    public function updatingBusqueda(): void        { $this->pagina = 1; }
    public function updatingFiltroCategoria(): void { $this->pagina = 1; }
    public function updatingOrdenar(): void         { $this->pagina = 1; }

    public function paginar(int $pagina): void { $this->pagina = $pagina; }

    public function render()
    {
        $productos = [];
        $meta      = ['current_page' => 1, 'last_page' => 1, 'total' => 0];

        if (!$this->notFound && !$this->apiError) {
            $base   = rtrim(env('API_URL', 'http://compuweb-api.test'), '/');
            $params = ['per_page' => 12, 'page' => $this->pagina, 'sort' => $this->ordenar];

            if ($this->busqueda)       $params['q']            = $this->busqueda;
            if ($this->filtroCategoria) $params['categoria_id'] = $this->filtroCategoria;

            try {
                $res = Http::timeout(5)->get("{$base}/api/tiendas/{$this->slug}/productos", $params);
                if ($res->successful()) {
                    $data      = $res->json();
                    $productos = $data['data'] ?? [];
                    $meta      = [
                        'current_page' => $data['current_page'] ?? 1,
                        'last_page'    => $data['last_page']    ?? 1,
                        'total'        => $data['total']        ?? 0,
                    ];
                }
            } catch (\Exception) {
                $this->apiError = true;
            }
        }

        return view('livewire.catalogo.index', compact('productos', 'meta'))
            ->layout('layouts.catalogo', [
                'tiendaNombre' => $this->tienda['nombre'] ?? 'Catálogo',
                'tiendaPhone'  => $this->tienda['telefono_principal'] ?? '',
                'slug'         => $this->slug,
            ]);
    }
}
