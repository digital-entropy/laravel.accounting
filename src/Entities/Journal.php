<?php


namespace DigitalEntropy\Accounting\Entities;


use Illuminate\Database\Eloquent\Model;

/**
 * Class Journal
 * @package DigitalEntropy\AccountingManager\Entities
 * @property int id
 */
class Journal extends Model
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
        return $this->hasMany(JournalEntry::class, 'journal_id', 'id');
    }

}