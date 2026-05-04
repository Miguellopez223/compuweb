<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalle de Categoría
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">

                <p><strong>ID:</strong> {{ $categoria->id }}</p>
                <p><strong>Nombre:</strong> {{ $categoria->nombre }}</p>
                <p><strong>Descripción:</strong> {{ $categoria->descripcion }}</p>
                <p>
                    <strong>Estado:</strong>
                    @if ($categoria->estado)
                        Activo
                    @else
                        Inactivo
                    @endif
                </p>

                <div class="mt-4">
                    <a href="{{ route('categorias.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">
                        Volver
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>