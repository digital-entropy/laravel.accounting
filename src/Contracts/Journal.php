<?php


namespace DigitalEntropy\Accounting\Contracts;


use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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
     * @return HasMany
     */
    function entries(): HasMany;

    /**
     * Get instance of recordable of a journal.
     *
     * @return MorphTo
     */
    function recordable(): MorphTo;

}
