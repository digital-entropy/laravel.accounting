<?php


namespace DigitalEntropy\Accounting\Ledger;


use Carbon\CarbonPeriod;
use DigitalEntropy\Accounting\Exceptions\StatementNotFoundException;
use DigitalEntropy\Accounting\Ledger\ChartOfAccount\Builder;
use Illuminate\Support\Arr;

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
    public function getStatement($key, string $groupCode = null, ?CarbonPeriod $period = null): array
    {
        if (! array_key_exists($key, config('accounting.statements'))) {
            throw new StatementNotFoundException($key);
        }

        $statement = config('accounting.statements.' . $key);
        $types = Arr::only(config('accounting.account_types'), $statement['accounts']);
        $accumulated = Arr::get($statement, 'accumulated', false);

        $this->builder
            ->period($period, $accumulated)
            ->appendBalance()
            ->groupCode($groupCode)
            ->accountTypeCode(Arr::flatten($types));

        if ($statement['cash_only']) {
            $this->builder->cash($statement["cash_only"]);
        }

        $accountByTypes = $this->builder->groupByAccountTypeCode();

        $debit = 0;
        $credit = 0;

        foreach ($accountByTypes as $key => $value) {
            foreach ($value as $account) {
                $debit += $account->getDebit();
                $credit += $account->getCredit();
            }
        }

        // unset unnecessary attribute
        unset($statement['accounts']);
        unset($statement['cash_only']);

        return array_merge($statement, [
            'name' => $statement['name'],
            'total' => $debit - $credit,
            'result' => $accountByTypes
        ]);
    }

    /**
     * Get all financial statements
     *
     * @param CarbonPeriod|null $period
     * @return array
     * @throws StatementNotFoundException
     */
    public function getFinancialStatements(?CarbonPeriod $period = null): array
    {
        $statements = [];

        foreach (config('accounting.statements') as $key => $statement) {
            $statements[$key] = $this->getStatement($key, $period);
        }

        return $statements;
    }

}
