<?php


namespace DigitalEntropy\Accounting\Entities;


use Illuminate\Database\Eloquent\Model;

/**
 * Class JournalEntry
 * @package DigitalEntropy\Accounting\Entities
 * @property int amount
 * @property string type
 */
class JournalEntry extends Model
{
    protected $table = 'journals_entries';

    protected $fillable = [
        'journal_id',
        'account_id',
        'type',
        'memo',
        'amount',
        'ref'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function journal() {
        return $this->belongsTo(Journal::class, 'journal_id', 'id');
    }

}