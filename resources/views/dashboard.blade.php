<x-app-layout>
    <h1 class="page-title">Dashboard General</h1>
    <p class="page-subtitle">Resumen del estado actual del inventario.</p>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number">0</div>
            <div class="stat-label">Total Productos</div>
        </div>

        <div class="stat-card">
            <div class="stat-number">0</div>
            <div class="stat-label">Total Movimientos</div>
        </div>

        <div class="stat-card alert">
            <div class="stat-number">0</div>
            <div class="stat-label">Sin Stock</div>
        </div>

        <div class="stat-card">
            <div class="stat-number">0</div>
            <div class="stat-label">Stock Bajo</div>
        </div>
    </div>

    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h2 style="font-size:22px; font-weight:900; margin:0;">🔔 Alertas de Stock</h2>
                <p style="color:#64748b; margin-top:8px;">Revisión requerida para mantener el flujo de operaciones.</p>
            </div>

            <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                Ver Inventario
            </a>
        </div>

        <div style="text-align:center; padding:60px 20px;">
            <div style="font-size:45px;">📦</div>

            <h3 style="font-size:22px; font-weight:900; margin-top:15px;">
                Inventario listo para gestionar
            </h3>

            <p style="color:#64748b; max-width:500px; margin:10px auto;">
                Desde este panel podrás controlar productos, categorías, movimientos de inventario y ventas.
            </p>
        </div>
    </div>
</x-app-layout>