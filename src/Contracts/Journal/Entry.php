<?php


namespace DigitalEntropy\Accounting\Contracts\Journal;


interface Entry
{
    const TYPE_DEBIT = 'DEBIT';
    const TYPE_CREDIT = 'CREDIT';

    /**
     * Amount of an entry.
     *
     * @return mixed
     */
    function getAmount(): int;

    /**
     * Write memo of an entry.
     *
     * @return mixed
     */
    function getMemo(): string;

    /**
     * Available Entry::TYPE_DEBIT or Entry::TYPE_CREDIT only
     *
     * @return mixed
     */
    function getType(): string;

    /**
     * Optional reference id of an entry.
     *
     * @return mixed
     */
    function getReferenceId(): ?string;

}