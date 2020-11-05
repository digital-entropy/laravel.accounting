<?php


namespace DigitalEntropy\Accounting\Concerns;


use DigitalEntropy\Accounting\Entities\Journal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasJournals
{

    /**
     * Define `morphMany` relationship with Journal model.
     *
     * @return MorphMany
     */
    public function journals()
    {
        return $this->morphMany(Journal::class, 'recordable');
    }

    /**
     * Create journal.
     *
     * @param string|null $memo
     * @param string|null $refId
     * @return Model
     */
    public function createJournal(?string $memo, ?string $refId)
    {
        return $this->journals()->create([
            'memo' => $memo,
            'ref' => $refId
        ]);
    }

}
