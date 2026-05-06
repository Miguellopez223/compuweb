<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PublicController;
use App\Http\Controllers\Api\ReporteController;
use App\Http\Controllers\Api\VentaController;
use Illuminate\Support\Facades\Route;

// Catalogo publico (sin autenticacion)
Route::prefix('tiendas/{slug}')->group(function () {
    Route::get('/', [PublicController::class, 'tienda']);
    Route::get('/categorias', [PublicController::class, 'categorias']);
    Route::get('/productos', [PublicController::class, 'productos']);
    Route::get('/productos/{id}', [PublicController::class, 'producto']);
    Route::get('/vendedores', [PublicController::class, 'vendedores']);
});

// Autenticacion
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

// Vendedor (token requerido)
Route::middleware('auth:api')->group(function () {
    Route::get('/ventas', [VentaController::class, 'index']);
    Route::post('/ventas', [VentaController::class, 'store']);
    Route::get('/ventas/{id}', [VentaController::class, 'show']);
});

// Admin (token + role=admin)
Route::middleware(['auth:api', \App\Http\Middleware\AdminOnly::class])->group(function () {
    Route::prefix('reportes')->group(function () {
        Route::get('/ventas', [ReporteController::class, 'ventas']);
        Route::get('/inventario', [ReporteController::class, 'inventario']);
    });
});
