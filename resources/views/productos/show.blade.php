<x-app-layout>
    <h1 class="page-title">Detalle del Producto</h1>
    <p class="page-subtitle">Información completa del producto seleccionado.</p>

    <div class="card">
        <table class="table">
            <tr>
                <th>CÓDIGO</th>
                <td>PRD{{ str_pad($producto->id, 3, '0', STR_PAD_LEFT) }}</td>
            </tr>

            <tr>
                <th>NOMBRE</th>
                <td>{{ $producto->nombre }}</td>
            </tr>

            <tr>
                <th>CATEGORÍA</th>
                <td>{{ $producto->categoria->nombre ?? 'Sin categoría' }}</td>
            </tr>

            <tr>
                <th>DESCRIPCIÓN</th>
                <td>{{ $producto->descripcion ?? 'Sin descripción' }}</td>
            </tr>

            <tr>
                <th>PRECIO</th>
                <td>Bs {{ number_format($producto->precio, 2) }}</td>
            </tr>

            <tr>
                <th>STOCK</th>
                <td>{{ $producto->stock }}</td>
            </tr>

            <tr>
                <th>ESTADO</th>
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
            </tr>

            <tr>
                <th>FECHA DE REGISTRO</th>
                <td>{{ $producto->created_at->format('d/m/Y H:i') }}</td>
            </tr>
        </table>

        <div class="actions">
            <a href="{{ route('productos.index') }}" class="btn btn-secondary">← Volver</a>
            <a href="{{ route('productos.edit', $producto) }}" class="btn btn-primary">Editar Producto</a>
        </div>
    </div>
</x-app-layout>