<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Categorías
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-4">
                <a href="{{ route('categorias.create') }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded">
                    + Nueva Categoría
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <table class="w-full border">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-4 py-2">ID</th>
                                <th class="border px-4 py-2">Nombre</th>
                                <th class="border px-4 py-2">Descripción</th>
                                <th class="border px-4 py-2">Estado</th>
                                <th class="border px-4 py-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categorias as $categoria)
                                <tr>
                                    <td class="border px-4 py-2">{{ $categoria->id }}</td>
                                    <td class="border px-4 py-2">{{ $categoria->nombre }}</td>
                                    <td class="border px-4 py-2">{{ $categoria->descripcion }}</td>
                                    <td class="border px-4 py-2">
                                        @if ($categoria->estado)
                                            <span class="text-green-600">Activo</span>
                                        @else
                                            <span class="text-red-600">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="border px-4 py-2">
                                        <a href="{{ route('categorias.show', $categoria) }}" class="text-blue-600">Ver</a>
                                        |
                                        <a href="{{ route('categorias.edit', $categoria) }}" class="text-yellow-600">Editar</a>
                                        |
                                        <form action="{{ route('categorias.destroy', $categoria) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600"
                                                onclick="return confirm('¿Seguro que deseas eliminar esta categoría?')">
                                                Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="border px-4 py-2 text-center">
                                        No hay categorías registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $categorias->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout><x-app-layout>
    <h1 class="page-title">Categorías</h1>
    <p class="page-subtitle">Gestiona los grupos o categorías de productos del inventario.</p>

    @if (session('success'))
        <div class="card" style="margin-bottom:20px; background:#dcfce7; color:#15803d; font-weight:700;">
            {{ session('success') }}
        </div>
    @endif

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <div>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">← Volver al Dashboard</a>
        </div>

        <div>
            <a href="{{ route('categorias.create') }}" class="btn btn-primary">+ Nueva Categoría</a>
        </div>
    </div>

    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>CÓDIGO</th>
                    <th>NOMBRE</th>
                    <th>DESCRIPCIÓN</th>
                    <th>ESTADO</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($categorias as $categoria)
                    <tr>
                        <td>CAT{{ str_pad($categoria->id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $categoria->nombre }}</td>
                        <td>{{ $categoria->descripcion ?? 'Sin descripción' }}</td>
                        <td>
                            @if ($categoria->estado)
                                <span class="badge badge-green">Activo</span>
                            @else
                                <span class="badge badge-red">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('categorias.show', $categoria) }}" style="color:#2f7484; font-weight:800;">
                                Ver
                            </a>
                            |
                            <a href="{{ route('categorias.edit', $categoria) }}" style="color:#ca8a04; font-weight:800;">
                                Editar
                            </a>
                            |
                            <form action="{{ route('categorias.destroy', $categoria) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    onclick="return confirm('¿Seguro que deseas eliminar esta categoría?')"
                                    style="border:none; background:none; color:#dc2626; font-weight:800; cursor:pointer;">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center; padding:35px;">
                            No hay categorías registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:20px;">
            {{ $categorias->links() }}
        </div>
    </div>
</x-app-layout>