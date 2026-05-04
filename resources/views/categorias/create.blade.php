<x-app-layout>
    <h1 class="page-title">Registrar Nueva Categoría</h1>
    <p class="page-subtitle">Ingrese los detalles para crear una nueva categoría del inventario.</p>

    <div class="card">
        <form action="{{ route('categorias.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Nombre de la categoría *</label>
                <input 
                    type="text" 
                    name="nombre" 
                    class="form-control" 
                    placeholder="Ej: Procesadores, Tarjetas gráficas, Laptops"
                    value="{{ old('nombre') }}"
                >

                @error('nombre')
                    <p style="color:#dc2626; font-weight:700; margin-top:5px;">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label>Descripción</label>
                <textarea 
                    name="descripcion" 
                    class="form-control" 
                    rows="4"
                    placeholder="Detalles adicionales sobre la categoría..."
                >{{ old('descripcion') }}</textarea>
            </div>

            <div class="form-group">
                <label>Estado *</label>
                <select name="estado" class="form-control">
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </select>
            </div>

            <div class="actions">
                <a href="{{ route('categorias.index') }}" class="btn btn-secondary">← Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar Categoría</button>
            </div>
        </form>
    </div>
</x-app-layout>