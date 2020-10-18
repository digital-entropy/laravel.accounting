<?php


namespace DigitalEntropy\Accounting;


use DigitalEntropy\Accounting\Entities\Account;
use DigitalEntropy\Accounting\Entities\Journal;
use DigitalEntropy\Accounting\Entities\JournalEntry;

class Accounting
{

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
    public static function createSimpleJournal(
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
        ]);

        /** @var JournalEntry $debitEntry */
        $debitEntry = $journal->entries()->create([
            'amount' => $amount,
            'type' => Journal::TYPE_DEBIT,
            'account_id' => $debitAccount->id
        ]);

        /** @var JournalEntry $creditEntry */
        $creditEntry = $journal->entries()->create([
            'amount' => $amount,
            'type' => Journal::TYPE_CREDIT,
            'account_id' => $creditAccount->id
        ]);

        return $journal;
    }

}