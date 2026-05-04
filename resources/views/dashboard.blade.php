<x-app-layout>
    <h1 class="page-title">Dashboard General</h1>
    <p class="page-subtitle">Resumen del estado actual del inventario.</p>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number">0</div>
            <div class="stat-label">TOTAL PRODUCTOS</div>
        </div>

        <div class="stat-card">
            <div class="stat-number">0</div>
            <div class="stat-label">TOTAL MOVIMIENTOS</div>
        </div>

        <div class="stat-card alert">
            <div class="stat-number">0</div>
            <div class="stat-label">SIN STOCK</div>
        </div>

        <div class="stat-card">
            <div class="stat-number">0</div>
            <div class="stat-label">STOCK BAJO</div>
        </div>
    </div>

    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h2 style="font-size:22px; font-weight:800;">🔔 Alertas de Stock</h2>
                <p style="color:#6b7280;">Revisión requerida para mantener el flujo de operaciones.</p>
            </div>

            <div>
                <a href="{{ route('productos.index') }}" class="btn btn-secondary">Ver Inventario</a>
            </div>
        </div>

        <div style="text-align:center; padding:60px 20px;">
            <div style="font-size:45px;">📦</div>
            <h3 style="font-size:20px; font-weight:800; margin-top:15px;">
                Inventario listo para gestionar
            </h3>
            <p style="color:#6b7280; max-width:500px; margin:10px auto;">
                Desde este panel podrás controlar productos, categorías, movimientos de inventario y ventas.
            </p>
        </div>
    </div>
</x-app-layout>