<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;

class ReciboController extends Controller
{
    private const RECEIPT_WIDTH = 226.77; // 80mm
    private const MEASURE_HEIGHT = 4000;
    private const HEIGHT_PADDING = 30;

    public function descargar(Venta $venta)
    {
        abort_unless($venta->tienda_id === auth()->user()->tienda_id, 403);

        $venta->load(['detalles.producto', 'tienda', 'vendedor']);

        $view = 'pdf.recibo-venta';
        $data = compact('venta');

        $contentHeight = $this->measureContentHeight($view, $data);
        $finalHeight = $contentHeight + self::HEIGHT_PADDING;

        $pdf = Pdf::loadView($view, $data)
            ->setPaper([0, 0, self::RECEIPT_WIDTH, $finalHeight], 'portrait');

        return $pdf->stream("recibo-{$venta->codigo_pedido}.pdf");
    }

    private function measureContentHeight(string $view, array $data): float
    {
        $measure = Pdf::loadView($view, $data)
            ->setPaper([0, 0, self::RECEIPT_WIDTH, self::MEASURE_HEIGHT], 'portrait');
        $measure->render();

        $pdfOutput = $measure->getDomPDF()->getCanvas()->get_cpdf()->output();

        preg_match_all('/stream\r?\n(.*?)\r?\nendstream/s', $pdfOutput, $streams);

        $allY = [];
        foreach ($streams[1] as $stream) {
            $text = @gzuncompress($stream);
            if ($text === false) $text = @gzinflate($stream);
            if ($text === false) continue;

            preg_match_all('/([\d.]+)\s+([\d.]+)\s+Td/', $text, $td);
            if (!empty($td[2])) $allY = array_merge($allY, array_map('floatval', $td[2]));

            preg_match_all('/([\d.]+)\s+([\d.]+)\s+[ml]\b/', $text, $ml);
            if (!empty($ml[2])) $allY = array_merge($allY, array_map('floatval', $ml[2]));

            preg_match_all('/([\d.]+)\s+([\d.]+)\s+([\d.]+)\s+([\d.]+)\s+re/', $text, $re);
            if (!empty($re[2])) {
                foreach ($re[2] as $i => $y) {
                    $w = floatval($re[3][$i]);
                    $h = floatval($re[4][$i]);
                    if ($w >= self::RECEIPT_WIDTH - 1 && $h >= self::MEASURE_HEIGHT - 1) continue;
                    $allY[] = floatval($y);
                }
            }
        }

        if (empty($allY)) {
            return 600;
        }

        return self::MEASURE_HEIGHT - min($allY);
    }
}
