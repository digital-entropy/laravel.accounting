<?php


namespace DigitalEntropy\Accounting;


class AccountingManager extends AccountingStatement
{

    public function __construct($config, ?string $entryOwnerId = null)
    {
        parent::__construct($config, $entryOwnerId);
    }

}