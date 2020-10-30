<?php


namespace DigitalEntropy\Accounting\Entities\Journal;


use DigitalEntropy\Accounting\Entities\Account;
use DigitalEntropy\Accounting\Entities\Journal;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Entry
 *
 * @package DigitalEntropy\AccountingManager\Entities
 * @property int amount
 * @property string type
 */
class Entry extends Model implements \DigitalEntropy\Accounting\Contracts\Journal\Entry
{
    protected $table = 'journal_entries';

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

    public function journal()
    {
        return $this->belongsTo(Journal::class, 'journal_id', 'id');
    }

    function getAmount(): int
    {
        return $this->attributes['amount'];
    }

    function getMemo(): string
    {
        return $this->attributes['memo'];
    }

    function getType(): string
    {
        return $this->attributes['type'];
    }

    function getReferenceId(): ?string
    {
        return $this->attributes['ref'] ?? null;
    }
}