<?php


namespace DigitalEntropy\Accounting\Traits;

/**
 * Trait HasAccount
 *
 * Use this trait for tenants model.
 *
 * @package DigitalEntropy\Accounting\Traits
 */
trait HasAccount
{

    function getCodeColumn()
    {
        return 'code';
    }

}