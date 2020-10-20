<?php

namespace DigitalEntropy\Accounting;

use Illuminate\Support\ServiceProvider;

class AccountingServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/accounting.php',
            'accounting'
        );

        $this->app->singleton(Accounting::class, function ($app) {
            return new Accounting($app['config']['accounting']);
        });
    }

    public function boot()
    {

    }
}