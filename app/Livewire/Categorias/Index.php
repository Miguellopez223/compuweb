<?php

namespace App\Livewire\Categorias;

use App\Models\Categoria;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Categorias')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $deleteId = null;

    public string $nombre = '';

    protected $rules = [
        'nombre' => 'required|string|max:255',
    ];

    protected $messages = [
        'nombre.required' => 'El nombre es obligatorio.',
    ];

    public function updatingSearch(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        $this->reset(['nombre', 'editingId']);
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $cat = Categoria::findOrFail($id);
        $this->editingId = $cat->id;
        $this->nombre    = $cat->nombre;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();
        $data = [
            'nombre'    => $this->nombre,
            'tienda_id' => auth()->user()->tienda_id,
        ];

        if ($this->editingId) {
            Categoria::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Categoria actualizada.');
        } else {
            Categoria::create($data);
            session()->flash('success', 'Categoria creada.');
        }
        $this->showModal = false;
        $this->reset(['nombre', 'editingId']);
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        Categoria::findOrFail($this->deleteId)->delete();
        $this->showDeleteModal = false;
        $this->deleteId = null;
        session()->flash('success', 'Categoria eliminada.');
    }

    public function render()
    {
        $categorias = Categoria::withCount('productos')
            ->when($this->search, fn($q) => $q->where('nombre', 'like', '%'.$this->search.'%'))
            ->latest()
            ->paginate(10);

        return view('livewire.categorias.index', compact('categorias'));
    }
}
