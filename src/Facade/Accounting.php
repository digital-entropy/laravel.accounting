<?php


namespace DigitalEntropy\Accounting\Facade;


use Illuminate\Support\Facades\Facade;

class Accounting extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \DigitalEntropy\Accounting\Accounting::class;
    }
}