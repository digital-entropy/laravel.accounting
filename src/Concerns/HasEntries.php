<?php


namespace DigitalEntropy\Accounting\Concerns;


use DigitalEntropy\Accounting\Contracts\Journal;
use DigitalEntropy\Accounting\Entities\Journal\Entry;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasEntries
{

    /**
     * Define `morphMany` relationship with Entry model.
     *
     * @return MorphMany
     */
    public function entries(): MorphMany
    {
        return $this->morphMany(Entry::class, 'author');
    }

    /**
     * Create single entry on specific journal
     *
     * @param Journal $journal
     * @param $entry
     * @return \Illuminate\Database\Eloquent\Model
     */
    function createEntry(Journal $journal, $entry)
    {
        return $journal->entries()->create($entry);
    }

    /**
     * Create many entries at once on specific journal
     *
     * @param Journal $journal
     * @param $data
     *
     * @return void
     */
    function createManyEntries(Journal $journal, $data)
    {
        foreach ($data as $item) {
            $this->createEntry($journal, $item);
        }
    }

}