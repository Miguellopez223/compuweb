<?php
require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$admin = \App\Models\User::where('email', 'alejandro@tercertiempo.com')->first();
\Illuminate\Support\Facades\Auth::login($admin);

$c = new \App\Livewire\Reportes\Index();
$c->vtaFechaDesde = now()->subDays(120)->format('Y-m-d');
$c->vtaFechaHasta = now()->format('Y-m-d');
$c->movFechaDesde = now()->subDays(120)->format('Y-m-d');
$c->movFechaHasta = now()->format('Y-m-d');

$ref = new ReflectionClass($c);
$metodos = ['getVentasData', 'getProductosData', 'getDashboardData', 'getVendedoresData', 'getClientesData', 'getProveedoresData', 'getRentabilidadData', 'getFiscalData'];

foreach ($metodos as $m) {
    $method = $ref->getMethod($m);
    $method->setAccessible(true);
    try {
        $r = $method->invoke($c);
        echo str_pad($m, 22) . ': OK (' . count($r) . ' claves)' . PHP_EOL;
    } catch (\Throwable $e) {
        echo str_pad($m, 22) . ': ERROR -> ' . $e->getMessage() . PHP_EOL;
    }
}

// Muestras de datos clave
$rent = (function () use ($ref, $c) { $m = $ref->getMethod('getRentabilidadData'); $m->setAccessible(true); return $m->invoke($c); })();
echo PHP_EOL . 'ABC: A=' . $rent['resumenAbc']['A'] . ' B=' . $rent['resumenAbc']['B'] . ' C=' . $rent['resumenAbc']['C'] . PHP_EOL;
$cli = (function () use ($ref, $c) { $m = $ref->getMethod('getClientesData'); $m->setAccessible(true); return $m->invoke($c); })();
echo 'Clientes: top=' . $cli['topClientes']->count() . ' recurrentes=' . $cli['recurrentes'] . ' nuevos=' . $cli['nuevos'] . ' inactivos=' . $cli['clientesInactivos']->count() . PHP_EOL;
$prov = (function () use ($ref, $c) { $m = $ref->getMethod('getProveedoresData'); $m->setAccessible(true); return $m->invoke($c); })();
echo 'Proveedores: ' . $prov['compras']->count() . ' (total compras Bs. ' . number_format($prov['totalCompras'], 2) . ')' . PHP_EOL;
$fis = (function () use ($ref, $c) { $m = $ref->getMethod('getFiscalData'); $m->setAccessible(true); return $m->invoke($c); })();
echo 'Fiscal: facturadas=' . $fis['countFacturado'] . ' total=Bs. ' . number_format($fis['totalFacturado'], 2) . ' sinNIT=' . $fis['sinNit'] . PHP_EOL;
$vta = (function () use ($ref, $c) { $m = $ref->getMethod('getVentasData'); $m->setAccessible(true); return $m->invoke($c); })();
echo 'Ventas: anuladas=' . $vta['numAnuladas'] . ' (Bs. ' . number_format($vta['montoAnulado'], 2) . ') uds/venta=' . number_format($vta['unidadesPromedio'], 1) . ' cambioMensual=' . number_format($vta['cambioMensual'], 1) . '%' . PHP_EOL;
$prod = (function () use ($ref, $c) { $m = $ref->getMethod('getProductosData'); $m->setAccessible(true); return $m->invoke($c); })();
echo 'Rotacion: ' . $prod['rotacionProductos']->count() . ' productos con rotacion' . PHP_EOL;
