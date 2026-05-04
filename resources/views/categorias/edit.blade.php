<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Categoría
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">

                <form action="{{ route('categorias.update', $categoria) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="block font-medium">Nombre</label>
                        <input type="text" name="nombre" class="w-full border rounded p-2" value="{{ old('nombre', $categoria->nombre) }}">
                        @error('nombre')
                            <p class="text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium">Descripción</label>
                        <textarea name="descripcion" class="w-full border rounded p-2">{{ old('descripcion', $categoria->descripcion) }}</textarea>
                        @error('descripcion')
                            <p class="text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block font-medium">Estado</label>
                        <select name="estado" class="w-full border rounded p-2">
                            <option value="1" {{ $categoria->estado ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ !$categoria->estado ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                            Actualizar
                        </button>

                        <a href="{{ route('categorias.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">
                            Cancelar
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout><x-app-layout>
    <h1 class="page-title">Editar Categoría</h1>
    <p class="page-subtitle">Actualice la información de la categoría seleccionada.</p>

    <div class="card">
        <form action="{{ route('categorias.update', $categoria) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>NOMBRE DE LA CATEGORÍA *</label>
                <input type="text" name="nombre" class="form-control"
                       value="{{ old('nombre', $categoria->nombre) }}">

                @error('nombre')
                    <p style="color:#dc2626; font-weight:700; margin-top:5px;">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label>DESCRIPCIÓN</label>
                <textarea name="descripcion" class="form-control" rows="4">{{ old('descripcion', $categoria->descripcion) }}</textarea>

                @error('descripcion')
                    <p style="color:#dc2626; font-weight:700; margin-top:5px;">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label>ESTADO *</label>
                <select name="estado" class="form-control">
                    <option value="1" {{ old('estado', $categoria->estado) == '1' ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ old('estado', $categoria->estado) == '0' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>

            <div class="actions">
                <a href="{{ route('categorias.index') }}" class="btn btn-secondary">← Cancelar</a>
                <button type="submit" class="btn btn-primary">Actualizar Categoría</button>
            </div>
        </form>
    </div>
</x-app-layout>