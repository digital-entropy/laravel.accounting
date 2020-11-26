<?php


namespace DigitalEntropy\Accounting\Tests;

use DigitalEntropy\Accounting\AccountingServiceProvider;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Orchestra\Testbench\Concerns\CreatesApplication;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getEnvironmentSetUp($app)
    {
        $app->register(AccountingServiceProvider::class);
    }
}
