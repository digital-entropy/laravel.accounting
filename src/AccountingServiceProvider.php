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
    }

    public function boot()
    {

    }
}