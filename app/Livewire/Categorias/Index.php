<?php

namespace App\Livewire\Categorias;

use App\Models\Categoria;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Categorías')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $deleteId = null;

    public string $nombre = '';
    public ?int $especialista_id = null;

    protected $rules = [
        'nombre'         => 'required|string|max:255',
        'especialista_id' => 'nullable|exists:users,id',
    ];

    protected $messages = [
        'nombre.required' => 'El nombre es obligatorio.',
    ];

    public function updatingSearch(): void { $this->resetPage(); }

    public function openCreate(): void
    {
        $this->reset(['nombre', 'especialista_id', 'editingId']);
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $cat = Categoria::findOrFail($id);
        $this->editingId      = $cat->id;
        $this->nombre         = $cat->nombre;
        $this->especialista_id = $cat->especialista_id;
        $this->showModal      = true;
    }

    public function save(): void
    {
        $this->validate();
        $data = [
            'nombre'          => $this->nombre,
            'especialista_id' => $this->especialista_id ?: null,
            'tienda_id'       => auth()->user()->tienda_id,
        ];

        if ($this->editingId) {
            Categoria::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Categoría actualizada.');
        } else {
            Categoria::create($data);
            session()->flash('success', 'Categoría creada.');
        }
        $this->showModal = false;
        $this->reset(['nombre', 'especialista_id', 'editingId']);
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
        session()->flash('success', 'Categoría eliminada.');
    }

    public function render()
    {
        $categorias = Categoria::with('especialista')
            ->when($this->search, fn($q) => $q->where('nombre', 'like', '%'.$this->search.'%'))
            ->latest()
            ->paginate(10);

        $especialistas = User::where('tienda_id', auth()->user()->tienda_id)->get();

        return view('livewire.categorias.index', compact('categorias', 'especialistas'));
    }
}
