<?php

namespace App\Livewire\Usuarios;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Usuarios')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $deleteId = null;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'vendedor';
    public string $whatsapp_number = '';

    public function updatingSearch(): void { $this->resetPage(); }

    protected function rules(): array
    {
        return [
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email'.($this->editingId ? ','.$this->editingId : ''),
            'password'         => $this->editingId ? 'nullable|min:8' : 'required|min:8',
            'role'             => 'required|in:admin,vendedor',
            'whatsapp_number'  => 'nullable|string|max:20',
        ];
    }

    protected $messages = [
        'name.required'     => 'El nombre es obligatorio.',
        'email.required'    => 'El correo es obligatorio.',
        'email.email'       => 'Ingresa un correo válido.',
        'email.unique'      => 'Este correo ya está en uso.',
        'password.required' => 'La contraseña es obligatoria.',
        'password.min'      => 'La contraseña debe tener al menos 8 caracteres.',
    ];

    public function openCreate(): void
    {
        $this->reset(['name', 'email', 'password', 'role', 'whatsapp_number', 'editingId']);
        $this->role = 'vendedor';
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $user = User::findOrFail($id);
        $this->editingId       = $user->id;
        $this->name            = $user->name;
        $this->email           = $user->email;
        $this->role            = $user->role;
        $this->whatsapp_number = $user->whatsapp_number ?? '';
        $this->password        = '';
        $this->showModal       = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'tienda_id'       => auth()->user()->tienda_id,
            'name'            => $this->name,
            'email'           => $this->email,
            'role'            => $this->role,
            'whatsapp_number' => $this->whatsapp_number ?: null,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->editingId) {
            User::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Usuario actualizado.');
        } else {
            User::create($data);
            session()->flash('success', 'Usuario creado.');
        }

        $this->showModal = false;
        $this->reset(['name', 'email', 'password', 'role', 'whatsapp_number', 'editingId']);
    }

    public function confirmDelete(int $id): void
    {
        if ($id === auth()->id()) {
            session()->flash('error', 'No puedes eliminar tu propia cuenta.');
            return;
        }
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        User::findOrFail($this->deleteId)->delete();
        $this->showDeleteModal = false;
        $this->deleteId = null;
        session()->flash('success', 'Usuario eliminado.');
    }

    public function render()
    {
        $usuarios = User::where('tienda_id', auth()->user()->tienda_id)
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('email', 'like', '%'.$this->search.'%');
            }))
            ->latest()
            ->paginate(10);

        return view('livewire.usuarios.index', compact('usuarios'));
    }
}
