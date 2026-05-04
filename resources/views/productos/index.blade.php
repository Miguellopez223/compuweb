<x-app-layout>
    <h1 class="page-title">Control de Inventario</h1>
    <p class="page-subtitle">Gestiona existencias y monitorea alertas de stock en tiempo real.</p>

    @if (session('success'))
        <div class="card" style="margin-bottom:20px; background:#dcfce7; color:#15803d; font-weight:700;">
            {{ session('success') }}
        </div>
    @endif

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">← Volver al Dashboard</a>

        <a href="{{ route('productos.create') }}" class="btn btn-primary">+ Nuevo Producto</a>
    </div>

    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>CÓDIGO</th>
                    <th>NOMBRE</th>
                    <th>CATEGORÍA</th>
                    <th>PRECIO</th>
                    <th>STOCK</th>
                    <th>ESTADO</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($productos as $producto)
                    <tr>
                        <td>PRD{{ str_pad($producto->id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $producto->nombre }}</td>
                        <td>{{ $producto->categoria->nombre ?? 'Sin categoría' }}</td>
                        <td>Bs {{ number_format($producto->precio, 2) }}</td>
                        <td>
                            @if ($producto->stock == 0)
                                <span style="color:#dc2626; font-weight:900;">{{ $producto->stock }}</span>
                            @elseif ($producto->stock <= 5)
                                <span style="color:#ca8a04; font-weight:900;">{{ $producto->stock }}</span>
                            @else
                                <span style="color:#15803d; font-weight:900;">{{ $producto->stock }}</span>
                            @endif
                        </td>
                        <td>
                            @if ($producto->stock == 0)
                                <span class="badge badge-red">Sin Stock</span>
                            @elseif ($producto->stock <= 5)
                                <span class="badge badge-yellow">Stock Bajo</span>
                            @elseif ($producto->estado)
                                <span class="badge badge-green">Disponible</span>
                            @else
                                <span class="badge badge-red">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('productos.show', $producto) }}" style="color:#2f7484; font-weight:800;">
                                Ver
                            </a>
                            |
                            <a href="{{ route('productos.edit', $producto) }}" style="color:#ca8a04; font-weight:800;">
                                Editar
                            </a>
                            |
                            <form action="{{ route('productos.destroy', $producto) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    onclick="return confirm('¿Seguro que deseas eliminar este producto?')"
                                    style="border:none; background:none; color:#dc2626; font-weight:800; cursor:pointer;">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center; padding:35px;">
                            No hay productos registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:20px;">
            {{ $productos->links() }}
        </div>
    </div>
</x-app-layout>