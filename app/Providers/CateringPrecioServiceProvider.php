<?php

namespace App\Providers;

use App\Models\CateringPrecio;
use Illuminate\Support\ServiceProvider;

class CateringPrecioServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('catering.precios', function () {
            return CateringPrecio::getPreciosArray();
        });
    }

    public function boot(): void
    {
    }
}
