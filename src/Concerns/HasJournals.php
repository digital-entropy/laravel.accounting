<?php


namespace Dentro\Accounting\Concerns;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasJournals
{

    /**
     * Define `morphMany` relationship with Journal model.
     *
     * @return MorphMany
     */
    public function journals(): MorphMany
    {
        return $this->morphMany(config('accounting.models.journal'), 'recordable');
    }

    /**
     * Create journal.
     *
     * @param string|null $memo
     * @param string|null $refId
     * @return Model
     */
    public function createJournal(?string $memo, ?string $refId): Model
    {
        return $this->journals()->create([
            'memo' => $memo,
            'ref' => $refId
        ]);
    }

}
