<?php

namespace DigitalEntropy\Accounting;

use Illuminate\Support\ServiceProvider;

class AccountingServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/accounting.php', 'accounting');

        $this->app->singleton(AccountingManager::class, function ($app) {
            return new AccountingManager($app['config']['accounting']);
        });
    }

    /**
     * Boot application.
     */
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
