<?php


namespace DigitalEntropy\Accounting\Contracts\Journal;


use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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

    /**
     * Define belongsTo relation with `Account` model.
     *
     * @return BelongsTo
     */
    public function account(): BelongsTo;

    /**
     * Define belongsTo relation with `Journal` model.
     *
     * @return BelongsTo
     */
    public function journal(): BelongsTo;


    /**
     * Define morph relation with `EntryAuthor` model.
     *
     * @return MorphTo
     */
    public function author(): MorphTo;
}
