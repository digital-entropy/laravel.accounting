<?php


namespace DigitalEntropy\Accounting;


use DigitalEntropy\Accounting\Entities\Account;
use DigitalEntropy\Accounting\Entities\Journal;
use DigitalEntropy\Accounting\Exceptions\NotBalanceJournalEntryException;

class JournalFactory
{

    protected $entries;

    public static function make()
    {
        return new static;
    }

    public function __construct()
    {
        $this->entries = [];
    }

    /**
     * @param Account $account
     * @param $amount
     * @param $memo
     * @param string $type
     * @param null $ref
     * @return $this
     */
    public function addEntry(Account $account, $amount, $memo, $type = Journal::TYPE_DEBIT, $ref = null)
    {
        $this->entries[] = [
            'account_id' => $account->id,
            'type' => $type,
            'memo' => $memo,
            'amount' => $amount,
            'ref' => $ref
        ];
        return $this;
    }

    /**
     * @param string|null $memo
     * @param null $ref
     * @param bool $strict
     * @return Journal|null
     * @throws NotBalanceJournalEntryException
     */
    public function save(string $memo, $ref = null, bool $strict = true)
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

        if ($balance != 0) {
            throw new NotBalanceJournalEntryException($balance, $debitBalance, $creditBalance);
        }

        /** @var Journal $journal */
        $journal = Journal::query()->create([
            'memo' => $memo,
            'ref' => $ref
        ])->fresh();

        foreach ($this->entries as $item) {
            $journal->entries()->create($item);
        }

        return $journal->fresh(['entries']);
    }

}