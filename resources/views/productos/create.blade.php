<x-app-layout>
    <h1 class="page-title">Registrar Nuevo Producto</h1>
    <p class="page-subtitle">Ingrese los detalles para catalogar un nuevo ítem en el inventario.</p>

    <div class="card">
        <form action="{{ route('productos.store') }}" method="POST">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label>Categoría *</label>
                    <select name="categoria_id" class="form-control">
                        <option value="">Seleccione una categoría...</option>

                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>
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
                        placeholder="Ej: Ryzen 5 5600G"
                        value="{{ old('nombre') }}"
                    >

                    @error('nombre')
                        <p style="color:#dc2626; font-weight:700; margin-top:5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label>Descripción</label>
                <textarea 
                    name="descripcion" 
                    class="form-control" 
                    rows="4"
                    placeholder="Características técnicas del producto..."
                >{{ old('descripcion') }}</textarea>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Precio *</label>
                    <input 
                        type="number" 
                        step="0.01" 
                        name="precio" 
                        class="form-control" 
                        placeholder="0.00"
                        value="{{ old('precio') }}"
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
                        placeholder="0"
                        value="{{ old('stock') }}"
                    >

                    @error('stock')
                        <p style="color:#dc2626; font-weight:700; margin-top:5px;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label>Estado *</label>
                <select name="estado" class="form-control">
                    <option value="1">Disponible</option>
                    <option value="0">Inactivo</option>
                </select>
            </div>

            <div class="actions">
                <a href="{{ route('productos.index') }}" class="btn btn-secondary">← Cancelar</a>
                <button type="submit" class="btn btn-primary">Registrar Producto</button>
            </div>
        </form>
    </div>
</x-app-layout>