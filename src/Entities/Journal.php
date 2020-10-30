<?php


namespace DigitalEntropy\Accounting\Entities;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class Journal
 *
 * @package DigitalEntropy\AccountingManager\Entities
 * @property int id
 */
class Journal extends Model implements \DigitalEntropy\Accounting\Contracts\Journal
{
    const TYPE_DEBIT = 'DEBIT';
    const TYPE_CREDIT = 'CREDIT';

    protected $fillable = [
        'amount',
        'memo',
        'ref'
    ];

    public function entries()
    {
        return $this->hasMany(Journal\Entry::class);
    }

    function getMemo(): string
    {
        return $this->attributes['memo'] ?? '';
    }

    function getEntries(): Collection
    {
        return $this->entries()->latest()->get();
    }
}