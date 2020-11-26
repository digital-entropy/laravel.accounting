<?php


namespace DigitalEntropy\Accounting\Exceptions;


use Throwable;

class StatementNotFoundException extends InvalidAccountingException
{
    /**
     * StatementNotFoundException constructor.
     * @param string $statement
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $statement, $code = 0, Throwable $previous = null)
    {
        parent::__construct("Key [${$statement}] is not found in accounting config", $code, $previous);
    }
}
