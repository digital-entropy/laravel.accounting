<?php


namespace DigitalEntropy\Accounting\Ledger;


use DigitalEntropy\Accounting\Contracts\Account;
use DigitalEntropy\Accounting\Contracts\EntryAuthor;
use DigitalEntropy\Accounting\Contracts\Journal;
use DigitalEntropy\Accounting\Contracts\Journal\Entry;
use DigitalEntropy\Accounting\Contracts\Recordable;
use DigitalEntropy\Accounting\Exceptions\NotBalanceJournalEntryException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Recorder
{

    /**
     * @var array
     */
    protected array $entries;

    /**
     * Recorder constructor.
     */
    public function __construct()
    {
        $this->entries = [];
    }

    /**
     * Add entry.
     *
     * @param Account $account
     * @param int $amount
     * @param null $memo
     * @param EntryAuthor $author
     * @param string $type
     * @param null $ref
     * @return $this
     */
    private function addEntry(Account $account, int $amount, EntryAuthor $author, string $type, $memo = null, $ref = null)
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
     * Record debit account.
     *
     * @param Account $account
     * @param $amount
     * @param EntryAuthor $author
     * @param null $memo
     * @param null $ref
     * @return Recorder
     */
    public function debit(Account $account, $amount, EntryAuthor $author, $memo = null, $ref = null)
    {
        $this->addEntry($account, $amount, $author, Entry::TYPE_DEBIT, $memo, $ref);

        return $this;
    }

    /**
     * Record credit account.
     *
     * @param Account $account
     * @param $amount
     * @param EntryAuthor $author
     * @param null $memo
     * @param null $ref
     * @return Recorder
     */
    public function credit(Account $account, $amount, EntryAuthor $author, $memo = null, $ref = null)
    {
        $this->addEntry($account, $amount, $author, Entry::TYPE_CREDIT, $memo, $ref);

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
    public function record(?Recordable $recordable, ?string $memo, ?string $ref = null, $strict = true)
    {
        $debitBalance = 0;
        $creditBalance = 0;
        $balance = 0;

        foreach ($this->entries as $item) {
            if ($item['type'] == Entry::TYPE_DEBIT) {
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

        $journalClass = config('accounting.models.journal');

        /** @var Journal|Model $journal */
        $journal = new $journalClass();
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

            $modelClass = config('accounting.models.entry');

            /** @var Entry|Model $entry */
            $entry = new $modelClass;
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
