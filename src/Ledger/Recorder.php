<?php


namespace DigitalEntropy\Accounting\Ledger;


use Carbon\Carbon;
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
     * @param EntryAuthor|array $author
     * @param string $type
     * @param null $memo
     * @param null $ref
     * @param Carbon|null $date
     * @return $this
     */
    private function addEntry(Account $account, int $amount, $author, string $type, $memo = null, $ref = null, ?Carbon $date = null)
    {
        $this->entries[] = collect([
            'account' => $account,
            'type' => $type,
            'memo' => $memo,
            'amount' => $amount,
            'ref' => $ref,
            'author' => $author,
            'date' => ($date ?? now())->toDateTimeString()
        ]);

        return $this;
    }

    /**
     * Record debit account.
     *
     * @param Account $account
     * @param $amount
     * @param EntryAuthor|array $author
     * @param null $memo
     * @param null $ref
     * @param Carbon|null $date
     * @return Recorder
     */
    public function debit(Account $account, $amount, $author, $memo = null, $ref = null, ?Carbon $date = null)
    {
        $this->addEntry($account, $amount, $author, Entry::TYPE_DEBIT, $memo, $ref, $date);

        return $this;
    }

    /**
     * Record credit account.
     *
     * @param Account $account
     * @param $amount
     * @param EntryAuthor|array $author
     * @param null $memo
     * @param null $ref
     * @param Carbon|null $date
     * @return Recorder
     */
    public function credit(Account $account, $amount, $author, $memo = null, $ref = null, ?Carbon $date = null)
    {
        $this->addEntry($account, $amount, $author, Entry::TYPE_CREDIT, $memo, $ref, $date);

        return $this;
    }

    /**
     * Save created entries.
     *
     * @param Recordable|array|null $recordable
     * @param string|null $memo
     * @param string|null $ref
     * @param bool $strict
     * @return Journal|null
     * @throws NotBalanceJournalEntryException
     */
    public function record($recordable, ?string $memo = null, ?string $ref = null, $strict = true)
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
            'amount' => intval(abs($debitBalance)),
            'memo' => $memo,
            'ref' => $ref
        ]);

        if (! is_null($recordable)) {
            if ($recordable instanceof Model) {
                $journal->recordable()->associate($recordable);
            } else {
                $journal->setAttribute('recordable_id', $recordable['recordable_id']);
                $journal->setAttribute('recordable_type', $recordable['recordable_type']);
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

            if ($entryAttributes->get('author') instanceof Model) {
                $entry->author()->associate($entryAttributes->get('author'));
            } else {
                $entry->setAttribute('author_id', $entryAttributes->get('author')['author_id']);
                $entry->setAttribute('author_type', $entryAttributes->get('author')['author_type']);
            }

            $entry->save();
        }

        $journal->load(['entries']);

        return $journal;
    }

}
