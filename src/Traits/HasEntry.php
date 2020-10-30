<?php


namespace DigitalEntropy\Accounting\Traits;


use DigitalEntropy\Accounting\Contracts\Account;
use DigitalEntropy\Accounting\Contracts\Journal;
use DigitalEntropy\Accounting\Contracts\Journal\Entry;
use DigitalEntropy\Accounting\Contracts\Reports\Statement;

trait HasEntry
{

    /**
     * Registrar identifier.
     *
     * @return string
     */
    function getIdentifier(): string
    {
        return 'id';
    }

    /**
     * Create a journal of entries.
     *
     * @param string $memo
     * @param string|null $refId
     * @return Journal|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    function createJournal(string $memo, ?string $refId = null)
    {
        return \DigitalEntropy\Accounting\Entities\Journal::query()->create([
            'memo' => $memo,
            'ref' => $refId
        ]);
    }

    /**
     * Create an entry.
     *
     * @param Journal $journal
     * @param Account $account
     * @param int $amount
     * @param string|null $memo
     * @return Entry|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    function createEntry(Journal $journal, Account $account, int $amount, ?string $memo = null)
    {
        return \DigitalEntropy\Accounting\Entities\Journal\Entry::query()->create([
            'amount' => $amount,
            'memo' => $memo,
            'account_id' => $account->getIdentifier(),
            'journal_id' => $journal->getIdentifier()
        ]);
    }

    /**
     * Get statements.
     *
     * @param bool $ownOnly
     * @return mixed
     */
    function getStatements($ownOnly = true): Statement
    {

    }
}