<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Movimientos</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #1e293b; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #4f46e5; padding-bottom: 15px; }
        .header h1 { font-size: 18px; color: #4f46e5; margin-bottom: 4px; }
        .header p { font-size: 10px; color: #64748b; }
        .filters { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px; padding: 8px 12px; margin-bottom: 15px; font-size: 10px; color: #475569; }
        .filters span { margin-right: 15px; }
        .filters strong { color: #1e293b; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th { background: #f1f5f9; border: 1px solid #e2e8f0; padding: 6px 8px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748b; font-weight: 600; }
        td { border: 1px solid #e2e8f0; padding: 5px 8px; font-size: 10px; }
        tr:nth-child(even) { background: #f8fafc; }
        .tipo-entrada { color: #059669; font-weight: 600; }
        .tipo-salida { color: #64748b; font-weight: 600; }
        .text-right { text-align: right; }
        .footer { text-align: center; font-size: 9px; color: #94a3b8; margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        .total-row { background: #f1f5f9; font-weight: 600; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $tienda->nombre ?? 'CompuWeb' }}</h1>
        <p>Reporte de Movimientos de Inventario</p>
        <p>Generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="filters">
        <span><strong>Periodo:</strong> {{ $filtros['fecha_desde'] ? \Carbon\Carbon::parse($filtros['fecha_desde'])->format('d/m/Y') : 'Inicio' }} — {{ $filtros['fecha_hasta'] ? \Carbon\Carbon::parse($filtros['fecha_hasta'])->format('d/m/Y') : 'Hoy' }}</span>
        @if($filtros['tipo']) <span><strong>Tipo:</strong> {{ ucfirst($filtros['tipo']) }}</span> @endif
        @if($filtros['motivo']) <span><strong>Motivo:</strong> {{ ucfirst(str_replace('_', ' ', $filtros['motivo'])) }}</span> @endif
        @if($filtros['usuario'])
            @php $nombreUsuario = $movimientos->first()?->user?->name ?? 'N/A'; @endphp
            <span><strong>Usuario:</strong> {{ $nombreUsuario }}</span>
        @endif
        <span><strong>Total registros:</strong> {{ $movimientos->count() }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Motivo</th>
                <th>Producto</th>
                <th class="text-right">Cantidad</th>
                <th class="text-right">Precio Unit.</th>
                <th>Proveedor / Ref.</th>
                <th>Registrado por</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movimientos as $m)
            <tr>
                <td>{{ $m->created_at->format('d/m/Y H:i') }}</td>
                <td class="{{ $m->tipo === 'entrada' ? 'tipo-entrada' : 'tipo-salida' }}">
                    {{ $m->tipo === 'entrada' ? '+ Entrada' : '- Salida' }}
                </td>
                <td>{{ $m->motivo ? ucfirst(str_replace('_', ' ', $m->motivo)) : '—' }}</td>
                <td>{{ $m->producto->nombre ?? '—' }}</td>
                <td class="text-right">{{ $m->tipo === 'entrada' ? '+' : '-' }}{{ $m->cantidad }}</td>
                <td class="text-right">{{ $m->precio_unitario ? 'Bs. '.number_format($m->precio_unitario, 2) : '—' }}</td>
                <td>{{ $m->proveedor ?: '' }}{{ $m->referencia ? ($m->proveedor ? ' · ' : '') . $m->referencia : '' }}{{ !$m->proveedor && !$m->referencia ? '—' : '' }}</td>
                <td>{{ $m->user->name ?? '—' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 20px; color: #94a3b8;">Sin movimientos para los filtros aplicados</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>{{ $tienda->nombre ?? 'CompuWeb' }} — Sistema de Gestión de Inventario</p>
        <p>Este documento fue generado automáticamente y no requiere firma.</p>
    </div>
</body>
</html>
