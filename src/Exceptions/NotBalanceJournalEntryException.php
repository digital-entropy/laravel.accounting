<?php


namespace DigitalEntropy\Accounting\Exceptions;


use Throwable;

class NotBalanceJournalEntryException extends InvalidAccountingException
{

    public function __construct(?int $balance = 0, ?int $left = 0, ?int $right = 0, $code = 0, Throwable $previous = null)
    {
        if ($balance == null && !is_null($left) && !is_null($right)) $balance = abs($left - $right);

        if (!is_null($left) && !is_null($right)) {
            parent::__construct("Not balance journal entry of $left and $right is $balance difference", $code, $previous);
        } else {
            parent::__construct("Not balance journal entry: $balance difference", $code, $previous);
        }
    }

}