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
</x-app-layout>