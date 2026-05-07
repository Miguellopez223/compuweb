<?php

namespace App\Livewire\Configuracion;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.admin')]
#[Title('Configuración')]
class Index extends Component
{
    public string $slug = '';
    public bool $editingSlug = false;

    public function mount(): void
    {
        $this->slug = auth()->user()->tienda->slug ?? '';
    }

    protected function rules(): array
    {
        $tiendaId = auth()->user()->tienda_id;
        return [
            'slug' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'regex:/^[a-z0-9-]+$/',
                "unique:tiendas,slug,{$tiendaId}",
            ],
        ];
    }

    protected $messages = [
        'slug.required' => 'El slug es obligatorio.',
        'slug.min'      => 'El slug debe tener al menos 3 caracteres.',
        'slug.max'      => 'El slug no puede superar 50 caracteres.',
        'slug.regex'    => 'Solo letras minúsculas, números y guiones (-). Sin espacios ni caracteres especiales.',
        'slug.unique'   => 'Este slug ya está en uso por otra tienda.',
    ];

    public function saveSlug(): void
    {
        $this->validate();

        auth()->user()->tienda->update(['slug' => $this->slug]);

        $this->editingSlug = false;
        session()->flash('success', 'Slug actualizado correctamente.');
    }

    public function cancelEdit(): void
    {
        $this->slug = auth()->user()->tienda->slug ?? '';
        $this->editingSlug = false;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.configuracion.index');
    }
}
