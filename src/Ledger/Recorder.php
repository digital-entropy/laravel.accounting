<?php


namespace Dentro\Accounting\Ledger;


use Carbon\Carbon;
use Dentro\Accounting\Contracts\Account;
use Dentro\Accounting\Contracts\EntryAuthor;
use Dentro\Accounting\Contracts\Journal;
use Dentro\Accounting\Contracts\Journal\Entry;
use Dentro\Accounting\Contracts\Recordable;
use Dentro\Accounting\Entities\Journal\Entry as EntryModel;
use Dentro\Accounting\Exceptions\NotBalanceJournalEntryException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
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
    private function addEntry(Account $account, int $amount, $author, string $type, $memo = null, $ref = null, ?Carbon $date = null): Recorder
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
    public function debit(Account $account, $amount, $author, $memo = null, $ref = null, ?Carbon $date = null): Recorder
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
    public function credit(Account $account, $amount, $author, $memo = null, $ref = null, ?Carbon $date = null): Recorder
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
     * @param string|null $groupCode
     * @param bool $strict
     * @return Journal
     * @throws NotBalanceJournalEntryException
     */
    public function record($recordable, ?string $memo = null, ?string $ref = null, ?string $groupCode = null, $strict = true): Journal
    {
        list($debitBalance) = $this->sumEntries($strict);

        $journal = $this->saveJournal(null, [
            'amount' => intval(abs($debitBalance)),
            'memo' => $memo,
            'ref' => $ref,
            'group_code' => $groupCode
        ], $recordable);

        $this->saveEntries($journal);

        /** @noinspection PhpUndefinedMethodInspection */
        $journal->load(['entries']);

        return $journal;
    }

    /**
     * @param Journal $journal
     * @param Recordable|array|null $recordable
     * @param string|null $memo
     * @param string|null $ref
     * @param string|null $groupCode
     * @param bool $strict
     * @return Journal
     * @throws NotBalanceJournalEntryException
     */
    public function updateRecord(Journal $journal, $recordable, ?string $memo = null, ?string $ref = null, ?string $groupCode = null, $strict = true): Journal
    {
        list($debitBalance) = $this->sumEntries($strict);

        $entries = collect($this->entries);

        $ids = $entries->filter(fn ($item) => Arr::get($item, 'id') !== null)->pluck('id');

        // Delete entry if they didn't mentioned.
        $toBeDeletedEntries = $journal->entries()->whereNotIn('id', $ids);

        /** @var EntryModel $entry */
        foreach ($toBeDeletedEntries->get() as $entry) {
            $entry->forceDelete();
        }

        $journal = $this->saveJournal($journal, [
            'amount' => intval(abs($debitBalance)),
            'memo' => $memo,
            'ref' => $ref,
            'group_code' => $groupCode
        ], $recordable);

        /** @noinspection PhpUndefinedMethodInspection */
        $journal->load(['entries']);
        
        $this->saveEntries($journal);

        return $journal;
    }

    /**
     * Sum journal entries
     *
     * @param $strict
     * @return array
     * @throws NotBalanceJournalEntryException
     */
    private function sumEntries($strict): array
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

        return [
            $debitBalance,
            $creditBalance,
            $balance
        ];
    }

    private function saveEntries(Journal $journal)
    {
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
    }

    public function saveJournal(?Journal $journal, array $attributes, $recordable = null): Journal
    {
        if (is_null($journal)) {
            $journalClass = config('accounting.models.journal');

            /** @var Journal|Model $journal */
            $journal = new $journalClass();
        }

        $journal->fill($attributes);

        if (! is_null($recordable)) {
            if ($recordable instanceof Model) {
                $journal->recordable()->associate($recordable);
            } else {
                $journal->setAttribute('recordable_id', $recordable['recordable_id']);
                $journal->setAttribute('recordable_type', $recordable['recordable_type']);
            }
        }

        $journal->save();

        return $journal;
    }

}
