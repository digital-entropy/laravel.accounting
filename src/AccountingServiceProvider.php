<?php

namespace DigitalEntropy\Accounting;

use DigitalEntropy\Accounting\Ledger\ChartOfAccount\Builder;
use DigitalEntropy\Accounting\Ledger\Poster;
use DigitalEntropy\Accounting\Ledger\Recorder;
use DigitalEntropy\Accounting\Ledger\Report;
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

        $this->app->bind(Recorder::class, function () {
            return new Recorder();
        });

        $this->app->bind(Builder::class, function () {
            return new Builder();
        });

        $this->app->bind(Report::class, function ($app) {
            return new Report($app->make(Builder::class));
        });

        $this->app->bind(Poster::class, function ($app) {
            return new Poster($app->make(Builder::class));
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
