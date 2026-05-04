<?php

namespace App\Livewire\Catalogo;

use Livewire\Component;
use Illuminate\Support\Facades\Http;

class Detalle extends Component
{
    public string $slug;
    public int    $id;
    public array  $producto = [];
    public array  $tienda   = [];
    public bool   $notFound = false;

    public function mount(string $slug, int $id): void
    {
        $this->slug = $slug;
        $this->id   = $id;
        $base       = rtrim(env('API_URL', 'http://compuweb-api.test'), '/');

        try {
            $tRes = Http::timeout(5)->get("{$base}/api/tiendas/{$slug}");
            if ($tRes->failed()) { $this->notFound = true; return; }
            $this->tienda = $tRes->json();

            $pRes = Http::timeout(5)->get("{$base}/api/tiendas/{$slug}/productos/{$id}");
            if ($pRes->status() === 404 || $pRes->failed()) { $this->notFound = true; return; }
            $this->producto = $pRes->json();
        } catch (\Exception) {
            $this->notFound = true;
        }
    }

    public function render()
    {
        return view('livewire.catalogo.detalle')
            ->layout('layouts.catalogo', [
                'tiendaNombre' => $this->tienda['nombre'] ?? 'Catálogo',
                'tiendaPhone'  => $this->tienda['telefono_principal'] ?? '',
                'slug'         => $this->slug,
            ]);
    }
}
