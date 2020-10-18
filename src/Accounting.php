<?php


namespace DigitalEntropy\Accounting;


use DigitalEntropy\Accounting\Entities\Account;
use DigitalEntropy\Accounting\Entities\Journal;
use DigitalEntropy\Accounting\Entities\JournalEntry;
use DigitalEntropy\Accounting\Exceptions\NotBalanceJournalEntryException;
use DigitalEntropy\Accounting\Journal\Entry;
use Illuminate\Support\Collection;

class Accounting
{

    /**
     * @param int $amount
     * @param string $memo
     * @param string|null $ref
     * @param JournalEntry ...$entries
     *
     * @throws NotBalanceJournalEntryException
     */
    public static function createJournalFromEntries(
        int $amount,
        string $memo,
        ?string $ref = null,
        JournalEntry ...$entries
    ): Journal
    {
        $entriesCollection = Collection::make($entries);

        $sumEntries = $entriesCollection->sum(function (JournalEntry $entry) {
            if ($entry->type == Journal::TYPE_DEBIT) {
                return $entry->amount;
            } else {
                return $entry->amount * -1;
            }
        });

        if ($sumEntries !== 0) {
            throw new NotBalanceJournalEntryException($sumEntries);
        }

        /** @var Journal $journal */
        $journal = Journal::query()->create([
            'amount' => $amount,
            'memo' => $memo,
            'ref' => $ref
        ]);

        $journal->entries()->createMany($journal->toArray());

        return $journal->fresh();
    }

    /**
     * Make Journal:
     * Journal should has 2 entries minimum.
     *
     * @param int $amount
     * @param string $memo
     * @param Account|null $debitAccount
     * @param Account|null $creditAccount
     * @param string|null $ref
     *
     * @return Journal
     */
    public static function createJournal(
        int $amount,
        string $memo,
        Account $debitAccount,
        Account $creditAccount,
        ?string $ref = null
    ): Journal
    {
        /** @var Journal $journal */
        $journal = Journal::query()->create([
            'amount' => $amount,
            'memo' => $memo,
            'ref' => $ref
        ])->fresh();

        $journal->entries()->create([
            'amount' => $amount,
            'type' => Journal::TYPE_DEBIT,
            'account_id' => $debitAccount->id
        ]);

        $journal->entries()->create([
            'amount' => $amount,
            'type' => Journal::TYPE_CREDIT,
            'account_id' => $creditAccount->id
        ]);

        return $journal;
    }

}