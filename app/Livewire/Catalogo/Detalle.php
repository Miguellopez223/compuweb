<?php

namespace App\Livewire\Catalogo;

use App\Models\Producto;
use App\Models\Tienda;
use App\Models\User;
use Livewire\Component;

class Detalle extends Component
{
    public string $slug;
    public int $id;
    public ?object $tienda = null;
    public ?object $producto = null;
    public bool $notFound = false;
    public array $vendedores = [];

    public function mount(string $slug, int $id): void
    {
        $this->slug = $slug;
        $this->id   = $id;

        $this->tienda = Tienda::where('slug', $slug)->where('estado', true)->first();

        if (!$this->tienda) {
            $this->notFound = true;
            return;
        }

        $this->producto = Producto::withoutGlobalScopes()
            ->with(['categoria:id,nombre', 'atributos'])
            ->where('tienda_id', $this->tienda->id)
            ->where('estado', 'Disponible')
            ->find($id);

        if (!$this->producto) {
            $this->notFound = true;
            return;
        }

        $this->vendedores = User::where('tienda_id', $this->tienda->id)
            ->where('visible_catalogo', true)
            ->whereNotNull('whatsapp_number')
            ->get(['id', 'name', 'role', 'whatsapp_number'])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.catalogo.detalle')
            ->layout('layouts.catalogo', [
                'tiendaNombre' => $this->tienda?->nombre ?? 'Catalogo',
                'tiendaPhone'  => $this->tienda?->telefono_principal ?? '',
                'slug'         => $this->slug,
            ]);
    }
}
