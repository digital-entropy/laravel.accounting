<?php


namespace DigitalEntropy\Accounting\Contracts;


use Illuminate\Database\Eloquent\Relations\MorphMany;

interface EntryAuthor
{
    /**
     * Define `morphMany` relationship with Entry model.
     *
     * @return MorphMany
     */
    function entries(): MorphMany;

    /**
     * Create single entry on specific journal
     *
     * @param Journal $journal
     * @param $entry
     * @return \Illuminate\Database\Eloquent\Model
     */
    function createEntry(Journal $journal, $entry);

    /**
     * Create many entries at once on specific journal
     *
     * @param Journal $journal
     * @param $data
     *
     * @return void
     */
    function createManyEntry(Journal $journal, array $data);
}
