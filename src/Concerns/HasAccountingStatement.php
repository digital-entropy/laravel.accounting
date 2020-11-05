<?php


namespace DigitalEntropy\Accounting\Concerns;


use DigitalEntropy\Accounting\AccountingStatement;
use DigitalEntropy\Accounting\Contracts\Reports\Statement;

trait HasAccountingStatement
{
    /**
     * Get statements.
     *
     * @param bool $ownOnly
     * @return mixed
     */
    function getStatements($ownOnly = true): Statement
    {
        return new AccountingStatement(
            config('accounting'),
            $ownOnly ? $this->getKey() : null
        );
    }
}
