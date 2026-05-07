<?php

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));

        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer')
                );

                $openApi->info->title       = 'CompuWeb API';
                $openApi->info->version     = '1.0.0';
                $openApi->info->description = <<<'MD'
API REST de **CompuWeb** — Sistema de inventario y ventas para MyPEs.

## Autenticación

Los endpoints protegidos requieren un token Bearer (Laravel Passport).

1. Obtén tu token en `POST /api/auth/login`
2. Úsalo en cada request: `Authorization: Bearer {token}`

## Niveles de acceso

| Nivel | Descripción |
|-------|-------------|
| Público | Sin token — catálogo de productos de una tienda |
| Vendedor | Token válido — registrar y consultar ventas |
| Admin | Token + role `admin` — reportes de ventas e inventario |
MD;
            });
    }
}
