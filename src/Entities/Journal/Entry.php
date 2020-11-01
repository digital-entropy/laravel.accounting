<?php


namespace DigitalEntropy\Accounting\Entities\Journal;


use DigitalEntropy\Accounting\Entities\Account;
use DigitalEntropy\Accounting\Entities\Journal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class Entry
 *
 * @package DigitalEntropy\AccountingManager\Entities
 * @property int amount
 * @property string type
 */
class Entry extends Model implements \DigitalEntropy\Accounting\Contracts\Journal\Entry
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'journal_entries';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'journal_id',
        'account_id',
        'type',
        'memo',
        'amount',
        'ref'
    ];

    /**
     * Define belongsTo relation with `Account` model.
     *
     * @return BelongsTo
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    /**
     * Define belongsTo relation with `Journal` model.
     *
     * @return BelongsTo
     */
    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'journal_id', 'id');
    }

    /**
     * Amount of an entry.
     *
     * @return mixed
     */
    function getAmount(): int
    {
        return $this->attributes['amount'];
    }

    /**
     * Write memo of an entry.
     *
     * @return mixed
     */
    function getMemo(): string
    {
        return $this->attributes['memo'];
    }

    /**
     * Define morph relation with `EntryAuthor` model.
     *
     * @return MorphTo
     */
    public function author(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Available Entry::TYPE_DEBIT or Entry::TYPE_CREDIT only
     *
     * @return mixed
     */
    function getType(): string
    {
        return $this->attributes['type'];
    }

    /**
     * Optional reference id of an entry.
     *
     * @return mixed
     */
    function getReferenceId(): ?string
    {
        return $this->attributes['ref'] ?? null;
    }
}
