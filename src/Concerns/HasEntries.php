<?php


namespace Dentro\Accounting\Concerns;


use Dentro\Accounting\Contracts\Journal;
use Illuminate\Database\Eloquent\Model;
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
        return $this->morphMany(config('accounting.models.entry'), 'author');
    }

    /**
     * Create single entry on specific journal
     *
     * @param Journal $journal
     * @param $entry
     * @return Model
     */
    function createEntry(Journal $journal, $entry): Model
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
    function createManyEntry(Journal $journal, $data)
    {
        foreach ($data as $item) {
            $this->createEntry($journal, $item);
        }
    }

}
