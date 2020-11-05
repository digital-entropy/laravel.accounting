<?php


namespace DigitalEntropy\Accounting;


use DigitalEntropy\Accounting\Contracts\EntryAuthor;
use DigitalEntropy\Accounting\Contracts\Recordable;
use DigitalEntropy\Accounting\Entities\Account;
use DigitalEntropy\Accounting\Entities\Journal;
use DigitalEntropy\Accounting\Exceptions\NotBalanceJournalEntryException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class JournalFactory
{

    /**
     * @var array
     */
    protected $entries;

    /**
     * Make instance.
     *
     * @return static
     */
    public static function make()
    {
        return new static;
    }

    /**
     * JournalFactory constructor.
     */
    public function __construct()
    {
        $this->entries = [];
    }

    /**
     * Add entry.
     *
     * @param Account $account
     * @param $amount
     * @param null $memo
     * @param EntryAuthor $author
     * @param string $type
     * @param null $ref
     * @return $this
     */
    public function addEntry(Account $account, $amount, EntryAuthor $author, $memo = null, $type = Journal::TYPE_DEBIT, $ref = null)
    {
        $this->entries[] = collect([
            'account' => $account,
            'type' => $type,
            'memo' => $memo,
            'amount' => $amount,
            'ref' => $ref,
            'author' => $author
        ]);

        return $this;
    }

    /**
     * Save created entries.
     *
     * @param Recordable|null $recordable
     * @param string|null $memo
     * @param string|null $ref
     * @param bool $strict
     * @return Journal|null
     * @throws NotBalanceJournalEntryException
     */
    public function save(?Recordable $recordable, ?string $memo, ?string $ref = null, $strict = true)
    {
        $debitBalance = 0;
        $creditBalance = 0;
        $balance = 0;

        foreach ($this->entries as $item) {
            if ($item['type'] == Journal::TYPE_DEBIT) {
                $balance += $item['amount'];
                $debitBalance += $item['amount'];
            } else {
                $balance -= $item['amount'];
                $creditBalance += $item['amount'];
            }
        }

        if ($balance != 0 && $strict) {
            throw new NotBalanceJournalEntryException($balance, $debitBalance, $creditBalance);
        }

        $journal = new Journal();
        $journal->fill([
            'memo' => $memo,
            'ref' => $ref
        ]);

        if (! is_null($recordable)) {
            if ($recordable instanceof Model) {
                $journal->recordable()->associate($recordable);
            } else {
                throw new \InvalidArgumentException('Param $recordable must be instance of eloquent model');
            }
        }

        $journal->save();

        /** @var Collection $entryAttributes */
        foreach ($this->entries as $entryAttributes) {
            $entry = new Journal\Entry();
            $entry->fill($entryAttributes->except('account', 'author')->toArray());

            $entry->journal()->associate($journal);
            $entry->account()->associate($entryAttributes->get('account'));
            $entry->author()->associate($entryAttributes->get('author'));
            $entry->save();
        }

        $journal->load(['entries']);

        return $journal;
    }

}
