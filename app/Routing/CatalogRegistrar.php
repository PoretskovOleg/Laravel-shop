<?php

declare(strict_types=1);

namespace App\Routing;

use App\Contracts\RouteRegistrar;
use App\Http\Controllers\Catalog\CatalogController;
use App\Http\Middleware\CatalogViewMiddleware;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Support\Facades\Route;

final class CatalogRegistrar implements RouteRegistrar
{
    public function map(Registrar $registrar): void
    {
        Route::middleware('web')->group(function () {
            Route::get('catalog/{category:slug?}', CatalogController::class)
                ->middleware(CatalogViewMiddleware::class)
                ->name('catalog');
        });
    }
}
