<?php

namespace App\Livewire\Ventas;

use App\Models\Producto;
use App\Models\Venta;
use App\Models\MovimientoInventario;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Historial de Ventas')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filtroEstado = '';
    public string $filtroFecha = '';
    public bool $showCancelModal = false;
    public ?int $cancelId = null;

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFiltroEstado(): void { $this->resetPage(); }

    public function confirmCancel(int $id): void
    {
        $this->cancelId = $id;
        $this->showCancelModal = true;
    }

    public function cancelar(): void
    {
        $venta = Venta::with('detalles')->findOrFail($this->cancelId);

        if ($venta->estado_venta === 'Anulada') {
            $this->showCancelModal = false;
            return;
        }

        foreach ($venta->detalles as $detalle) {
            $producto = Producto::find($detalle->producto_id);
            if ($producto) {
                $nuevoStock = $producto->stock + $detalle->cantidad;
                $producto->update([
                    'stock'  => $nuevoStock,
                    'estado' => 'Disponible',
                ]);
                MovimientoInventario::create([
                    'tienda_id'       => auth()->user()->tienda_id,
                    'producto_id'     => $producto->id,
                    'user_id'         => auth()->id(),
                    'tipo'            => 'entrada',
                    'cantidad'        => $detalle->cantidad,
                    'precio_unitario' => $detalle->precio_unitario,
                    'referencia'      => 'Anulación de venta #'.$venta->id,
                ]);
            }
        }

        $venta->update(['estado_venta' => 'Anulada']);
        $this->showCancelModal = false;
        $this->cancelId = null;
        session()->flash('success', 'Venta anulada. El stock fue reintegrado.');
    }

    public function render()
    {
        $ventas = Venta::with('vendedor')
            ->when($this->search, fn($q) => $q->where('cliente_nombre', 'like', '%'.$this->search.'%'))
            ->when($this->filtroEstado, fn($q) => $q->where('estado_venta', $this->filtroEstado))
            ->when($this->filtroFecha, fn($q) => $q->whereDate('created_at', $this->filtroFecha))
            ->latest()
            ->paginate(15);

        return view('livewire.ventas.index', compact('ventas'));
    }
}
