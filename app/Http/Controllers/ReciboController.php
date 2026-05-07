<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;

class ReciboController extends Controller
{
    public function descargar(Venta $venta)
    {
        abort_unless($venta->tienda_id === auth()->user()->tienda_id, 403);

        $venta->load(['detalles.producto', 'tienda', 'vendedor']);

        $pdf = Pdf::loadView('pdf.recibo-venta', compact('venta'))
            ->setPaper([0, 0, 226.77, 1100], 'portrait');

        return $pdf->stream("recibo-{$venta->codigo_pedido}.pdf");
    }
}
