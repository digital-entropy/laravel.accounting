<?php


namespace DigitalEntropy\Accounting\Entities;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class Journal
 *
 * @package DigitalEntropy\AccountingManager\Entities
 * @property int id
 */
class Journal extends Model implements \DigitalEntropy\Accounting\Contracts\Journal
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'amount',
        'memo',
        'ref'
    ];

    /**
     * Journal unique identifier.
     *
     * @return string
     */
    function getIdentifier(): string
    {
        return $this->getKeyname();
    }

    /**
     * Get the journal memo.
     *
     * @return string
     */
    function getMemo(): string
    {
        return $this->attributes['memo'] ?? '';
    }

    /**
     * Define `hasMany` relationship with Entry model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function entries(): HasMany
    {
        return $this->hasMany(Journal\Entry::class);
    }

    /**
     * Get instance of recordable of a journal.
     *
     * @return MorphTo
     */
    function recordable(): MorphTo
    {
        return $this->morphTo('recordable');
    }
}
