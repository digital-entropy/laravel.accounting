<?php

namespace DigitalEntropy\Accounting;

use Illuminate\Support\ServiceProvider;

class AccountingServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/accounting.php', 'accounting');

        $this->app->singleton(Accounting::class, function ($app) {
            return new Accounting($app['config']['accounting']);
        });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {

            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'accounting-migrations');

            $this->publishes([
                __DIR__ . '/../config/accounting.php' => config_path('accounting.php')
            ], 'accounting-config');

        }
    }
}