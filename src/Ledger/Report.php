<?php


namespace DigitalEntropy\Accounting\Ledger;


use Carbon\CarbonPeriod;
use DigitalEntropy\Accounting\Contracts\Account;
use DigitalEntropy\Accounting\Exceptions\StatementNotFoundException;
use DigitalEntropy\Accounting\Ledger\ChartOfAccount\Builder;

class Report
{

    /**
     * @var Builder
     */
    private Builder $builder;

    /**
     * Report constructor.
     * @param Builder $builder
     */
    public function __construct(Builder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Get statement
     *
     * @param $key
     * @param string|null $groupCode
     * @param CarbonPeriod|null $period
     * @return array
     * @throws StatementNotFoundException
     */
    public function getStatement($key, string $groupCode = null, ?CarbonPeriod $period = null)
    {
        if (! array_key_exists($key, config('accounting.statements'))) {
            throw new StatementNotFoundException($key);
        }

        $statement = config('accounting.statements.' . $key);

        $accounts = $this->builder
            ->period($period)
            ->withBalance()
            ->accountTypeCode($statement['accounts'])
            ->groupCode($groupCode)
            ->cash($statement["cash_only"] ?? true)->get();

        $debit = 0;
        $credit = 0;


        /** @var Account $account */
        foreach ($accounts as $account) {
            $debit += $account->getDebit();
            $credit += $account->getCredit();
        }

        return array_merge($statement, [
            'debit' => $debit,
            'credit' => $credit,
            'total' => $debit - $credit,
            'result' => $accounts
        ]);
    }

    /**
     * Get all financial statements
     *
     * @param CarbonPeriod|null $period
     * @return array
     * @throws StatementNotFoundException
     */
    public function getFinancialStatements(?CarbonPeriod $period = null)
    {
        $statements = [];

        foreach (config('accounting.statements') as $key => $statement) {
            $statements[$key] = $this->getStatement($key, $period);
        }

        return $statements;
    }

}
