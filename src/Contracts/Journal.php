<?php


namespace DigitalEntropy\Accounting\Contracts;


use DigitalEntropy\Accounting\Contracts\Journal\Entry;
use Illuminate\Support\Collection;

interface Journal
{

    /**
     * Journal unique identifier.
     *
     * @return string
     */
    function getIdentifier(): string;

    /**
     * Get the journal memo.
     *
     * @return string
     */
    function getMemo(): string;

    /**
     * Get the journal attached entries.
     *
     * @return array<Entry>
     */
    function getEntries(): Collection;

}