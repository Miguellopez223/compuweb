<?php

use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Livewire\Categorias\Index as CategoriasIndex;
use App\Livewire\Productos\Index as ProductosIndex;
use App\Livewire\Movimientos\Index as MovimientosIndex;
use App\Livewire\Ventas\Index as VentasIndex;
use App\Livewire\Ventas\Registrar as VentasRegistrar;
use App\Livewire\Usuarios\Index as UsuariosIndex;
use Illuminate\Support\Facades\Route;

// Catálogo público
Route::get('/catalogo/{slug}', \App\Livewire\Catalogo\Index::class)->name('catalogo');
Route::get('/catalogo/{slug}/producto/{id}', \App\Livewire\Catalogo\Detalle::class)->name('catalogo.producto');

Route::get('/login', Login::class)->name('login')->middleware('guest');

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/productos', ProductosIndex::class)->name('productos');
    Route::get('/categorias', CategoriasIndex::class)->name('categorias');
    Route::get('/movimientos', MovimientosIndex::class)->name('movimientos');
    Route::get('/ventas', VentasIndex::class)->name('ventas');
    Route::get('/ventas/registrar', VentasRegistrar::class)->name('ventas.registrar');

    Route::middleware(App\Http\Middleware\AdminOnly::class)->group(function () {
        Route::get('/usuarios', UsuariosIndex::class)->name('usuarios');
    });
});
