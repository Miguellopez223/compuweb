<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            width: 72mm;
            padding: 4mm;
            color: #000;
            background: #fff;
            font-weight: bold;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 6px;
        }

        .logo-container img {
            width: 120px;
            max-height: 80px;
        }

        .store-name {
            font-family: 'DejaVu Serif', serif;
            font-size: 15px;
            font-weight: 900;
            text-align: center;
            margin-bottom: 3px;
            color: #000;
        }

        .store-info {
            text-align: center;
            font-size: 9px;
            margin-bottom: 2px;
            font-weight: bold;
            color: #000;
        }

        .separator {
            border-top: 2px dashed #000;
            margin: 5px 0;
        }

        .section-title {
            font-family: 'DejaVu Serif', serif;
            font-weight: 900;
            font-size: 11px;
            margin: 5px 0 3px 0;
            border-bottom: 2px solid #000;
            padding-bottom: 2px;
            color: #000;
        }

        .info-line {
            margin-bottom: 2px;
            font-size: 10px;
            font-weight: bold;
            color: #000;
        }

        .label {
            font-family: 'DejaVu Serif', serif;
            font-weight: 900;
            font-size: 10px;
            color: #000;
        }

        .value {
            font-family: 'Arial', 'DejaVu Sans', sans-serif;
            font-weight: 900;
            font-size: 10px;
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 4px 0;
        }

        table th {
            font-family: 'DejaVu Serif', serif;
            font-size: 9px;
            font-weight: 900;
            text-align: left;
            border-bottom: 2px solid #000;
            padding: 2px 1px;
            color: #000;
        }

        table td {
            font-family: 'Arial', 'DejaVu Sans', sans-serif;
            font-size: 9px;
            font-weight: 900;
            padding: 2px 1px;
            vertical-align: top;
            color: #000;
        }

        table th:last-child,
        table td:last-child {
            text-align: right;
        }

        table th:nth-child(2),
        table td:nth-child(2) {
            text-align: center;
        }

        .total-row {
            border-top: 2px dashed #000;
            padding-top: 5px;
            margin-top: 5px;
        }

        .total-label {
            font-family: 'DejaVu Serif', serif;
            font-weight: 900;
            font-size: 12px;
            text-align: left;
            color: #000;
        }

        .total-amount {
            font-family: 'DejaVu Serif', serif;
            font-size: 18px;
            font-weight: 900;
            text-align: right;
            color: #000;
        }

        .footer {
            text-align: center;
            font-family: 'Arial', 'DejaVu Sans', sans-serif;
            font-size: 9px;
            font-weight: bold;
            margin-top: 7px;
            color: #000;
        }

        .logo-error {
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            color: #000;
            margin-bottom: 5px;
        }
    </style>
</head>

@php
    $logoPath = public_path('imagen.png');

    $logoBase64 = file_exists($logoPath)
        ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
        : null;
@endphp

<body>

    {{-- Logo de la empresa --}}
    <div class="logo-container">
        @if($logoBase64)
            <img src="{{ $logoBase64 }}" alt="Logo de la empresa">
        @else
            <div class="logo-error">IMAGEN NO ENCONTRADA</div>
        @endif
    </div>

    {{-- Nombre y datos de la tienda --}}
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
    <div class="info-line">
        <span class="label">VENTA:</span>
        <span class="value">{{ $venta->codigo_pedido }}</span>
    </div>

    <div class="info-line">
        <span class="label">FECHA:</span>
        <span class="value">{{ $venta->created_at->format('d/m/Y H:i') }}</span>
    </div>

    <div class="info-line">
        <span class="label">ESTADO:</span>
        <span class="value">{{ $venta->estado_venta }}</span>
    </div>

    <div class="info-line">
        <span class="label">PAGO:</span>
        <span class="value">{{ ucfirst($venta->metodo_pago) }}</span>
    </div>

    <div class="info-line">
        <span class="label">VENDEDOR:</span>
        <span class="value">{{ $venta->vendedor->name ?? '--' }}</span>
    </div>

    <div class="separator"></div>

    {{-- Datos del cliente --}}
    <div class="section-title">DATOS DEL CLIENTE</div>

    <div class="info-line">
        <span class="label">Nombre:</span>
        <span class="value">{{ mb_strtoupper($venta->cliente_nombre) }}</span>
    </div>

    @if($venta->cliente_nit)
        <div class="info-line">
            <span class="label">NIT/CI:</span>
            <span class="value">{{ $venta->cliente_nit }}</span>
        </div>
    @endif

    @if($venta->cliente_email)
        <div class="info-line">
            <span class="label">Email:</span>
            <span class="value">{{ $venta->cliente_email }}</span>
        </div>
    @endif

    @if($venta->cliente_telefono)
        <div class="info-line">
            <span class="label">Tel:</span>
            <span class="value">{{ $venta->cliente_telefono }}</span>
        </div>
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

    {{-- Total --}}
    <div class="total-row">
        <table>
            <tr>
                <td class="total-label">TOTAL:</td>
                <td class="total-amount">Bs. {{ number_format($venta->total, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="separator"></div>

    {{-- Pie --}}
    <div class="footer">
        Gracias por su compra<br>
        {{ $venta->tienda->nombre }}<br>
        {{ $venta->created_at->format('d/m/Y H:i:s') }}
    </div>


</body>
</html>
