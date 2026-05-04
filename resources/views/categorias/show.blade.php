<x-app-layout>
    <h1 class="page-title">Detalle de Categoría</h1>
    <p class="page-subtitle">Información completa de la categoría seleccionada.</p>

    <div class="card">
        <table class="table">
            <tr>
                <th>CÓDIGO</th>
                <td>CAT{{ str_pad($categoria->id, 3, '0', STR_PAD_LEFT) }}</td>
            </tr>

            <tr>
                <th>NOMBRE</th>
                <td>{{ $categoria->nombre }}</td>
            </tr>

            <tr>
                <th>DESCRIPCIÓN</th>
                <td>{{ $categoria->descripcion ?? 'Sin descripción' }}</td>
            </tr>

            <tr>
                <th>ESTADO</th>
                <td>
                    @if ($categoria->estado)
                        <span class="badge badge-green">Activo</span>
                    @else
                        <span class="badge badge-red">Inactivo</span>
                    @endif
                </td>
            </tr>

            <tr>
                <th>FECHA DE REGISTRO</th>
                <td>{{ $categoria->created_at->format('d/m/Y H:i') }}</td>
            </tr>
        </table>

        <div class="actions">
            <a href="{{ route('categorias.index') }}" class="btn btn-secondary">← Volver</a>
            <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-primary">Editar Categoría</a>
        </div>
    </div>
</x-app-layout>