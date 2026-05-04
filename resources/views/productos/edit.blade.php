<x-app-layout>
    <h1 class="page-title">Editar Producto</h1>
    <p class="page-subtitle">Actualice la información del producto seleccionado.</p>

    <div class="card">
        <form action="{{ route('productos.update', $producto) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="form-group">
                    <label>Categoría *</label>
                    <select name="categoria_id" class="form-control">
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}"
                                {{ old('categoria_id', $producto->categoria_id) == $categoria->id ? 'selected' : '' }}>
                                {{ $categoria->nombre }}
                            </option>
                        @endforeach
                    </select>

                    @error('categoria_id')
                        <p style="color:#dc2626; font-weight:700; margin-top:5px;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Nombre del producto *</label>
                    <input 
                        type="text" 
                        name="nombre" 
                        class="form-control"
                        value="{{ old('nombre', $producto->nombre) }}"
                    >

                    @error('nombre')
                        <p style="color:#dc2626; font-weight:700; margin-top:5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label>Descripción</label>
                <textarea name="descripcion" class="form-control" rows="4">{{ old('descripcion', $producto->descripcion) }}</textarea>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Precio *</label>
                    <input 
                        type="number" 
                        step="0.01" 
                        name="precio" 
                        class="form-control"
                        value="{{ old('precio', $producto->precio) }}"
                    >

                    @error('precio')
                        <p style="color:#dc2626; font-weight:700; margin-top:5px;">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Stock *</label>
                    <input 
                        type="number" 
                        name="stock" 
                        class="form-control"
                        value="{{ old('stock', $producto->stock) }}"
                    >

                    @error('stock')
                        <p style="color:#dc2626; font-weight:700; margin-top:5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label>Estado *</label>
                <select name="estado" class="form-control">
                    <option value="1" {{ old('estado', $producto->estado) == '1' ? 'selected' : '' }}>Disponible</option>
                    <option value="0" {{ old('estado', $producto->estado) == '0' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>

            <div class="actions">
                <a href="{{ route('productos.index') }}" class="btn btn-secondary">← Cancelar</a>
                <button type="submit" class="btn btn-primary">Actualizar Producto</button>
            </div>
        </form>
    </div>
</x-app-layout>