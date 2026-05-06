<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            width: 72mm;
            padding: 4mm;
            color: #000;
        }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
        .separator {
            border-top: 1px dashed #000;
            margin: 4px 0;
        }
        .store-name {
            font-size: 14px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 2px;
        }
        .store-info {
            text-align: center;
            font-size: 9px;
            margin-bottom: 2px;
        }
        .section-title {
            font-weight: bold;
            font-size: 10px;
            margin: 4px 0 2px 0;
            border-bottom: 1px solid #000;
            padding-bottom: 1px;
        }
        .label { font-weight: bold; }
        .info-line {
            margin-bottom: 1px;
            font-size: 9px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 3px 0;
        }
        table th {
            font-size: 8px;
            text-align: left;
            border-bottom: 1px solid #000;
            padding: 2px 1px;
        }
        table th:last-child,
        table td:last-child {
            text-align: right;
        }
        table th:nth-child(2),
        table td:nth-child(2) {
            text-align: center;
        }
        table td {
            font-size: 9px;
            padding: 2px 1px;
            vertical-align: top;
        }
        .total-row {
            border-top: 1px dashed #000;
            padding-top: 4px;
            margin-top: 4px;
        }
        .total-amount {
            font-size: 16px;
            font-weight: bold;
            text-align: right;
        }
        .footer {
            text-align: center;
            font-size: 8px;
            margin-top: 6px;
            color: #555;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 4px;
        }
        .logo-container img {
            max-height: 40px;
            max-width: 50mm;
        }
    </style>
</head>
<body>

    {{-- Logo y datos de tienda --}}
    @if($venta->tienda->logo)
    <div class="logo-container">
        <img src="{{ public_path('storage/' . $venta->tienda->logo) }}" alt="Logo">
    </div>
    @endif

    <div class="store-name">{{ mb_strtoupper($venta->tienda->nombre) }}</div>

    @if($venta->tienda->direccion)
        <div class="store-info">{{ $venta->tienda->direccion }}</div>
    @endif
    @if($venta->tienda->telefono_principal)
        <div class="store-info">Tel: {{ $venta->tienda->telefono_principal }}</div>
    @endif
    @if($venta->tienda->nit)
        <div class="store-info">NIT: {{ $venta->tienda->nit }}</div>
    @endif

    <div class="separator"></div>

    {{-- Datos del pedido --}}
    <div class="info-line"><span class="label">VENTA:</span> {{ $venta->codigo_pedido }}</div>
    <div class="info-line"><span class="label">FECHA:</span> {{ $venta->created_at->format('d/m/Y H:i') }}</div>
    <div class="info-line"><span class="label">ESTADO:</span> {{ $venta->estado_venta }}</div>
    <div class="info-line"><span class="label">PAGO:</span> {{ ucfirst($venta->metodo_pago) }}</div>
    <div class="info-line"><span class="label">VENDEDOR:</span> {{ $venta->vendedor->name ?? '-' }}</div>

    <div class="separator"></div>

    {{-- Datos del cliente --}}
    <div class="section-title">DATOS DEL CLIENTE</div>
    <div class="info-line"><span class="label">Nombre:</span> {{ mb_strtoupper($venta->cliente_nombre) }}</div>
    @if($venta->cliente_nit)
        <div class="info-line"><span class="label">NIT/CI:</span> {{ $venta->cliente_nit }}</div>
    @endif
    @if($venta->cliente_email)
        <div class="info-line"><span class="label">Email:</span> {{ $venta->cliente_email }}</div>
    @endif
    @if($venta->cliente_telefono)
        <div class="info-line"><span class="label">Tel:</span> {{ $venta->cliente_telefono }}</div>
    @endif
    @if($venta->cliente_direccion)
        <div class="info-line"><span class="label">Dir:</span> {{ $venta->cliente_direccion }}</div>
    @endif

    <div class="separator"></div>

    {{-- Detalle de productos --}}
    <div class="section-title">DETALLE DE VENTA</div>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cant.</th>
                <th>P.Unit.</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalles as $detalle)
            <tr>
                <td>{{ $detalle->producto->nombre ?? 'Producto eliminado' }}</td>
                <td>{{ $detalle->cantidad }}</td>
                <td>{{ number_format($detalle->precio_unitario, 2) }}</td>
                <td>{{ number_format($detalle->cantidad * $detalle->precio_unitario, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-row">
        <table>
            <tr>
                <td class="bold" style="text-align: left; font-size: 11px;">TOTAL:</td>
                <td class="total-amount">Bs. {{ number_format($venta->total, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="separator"></div>

    <div class="footer">
        Gracias por su compra<br>
        {{ $venta->tienda->nombre }}<br>
        {{ $venta->created_at->format('d/m/Y H:i:s') }}
    </div>

</body>
</html>
