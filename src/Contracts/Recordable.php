<?php


namespace Dentro\Accounting\Contracts;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Recordable
{
    /**
     * Define `morphMany` relationship with Journal model.
     *
     * @return MorphMany
     */
    function journals();

    /**
     * Create journal.
     *
     * @param string|null $memo
     * @param string|null $refId
     * @return Model
     */
    function createJournal(?string $memo, ?string $refId);
}
