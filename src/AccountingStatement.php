<?php


namespace DigitalEntropy\Accounting;


use Carbon\CarbonPeriod;
use DigitalEntropy\Accounting\Contracts\Account;
use DigitalEntropy\Accounting\Contracts\Reports\Statement;
use DigitalEntropy\Accounting\Entities\Journal\Entry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AccountingStatement implements Statement
{

    protected $config;
    protected $accountTypes;
    protected $statements;
    protected $ownerId;

    public function __construct($config, ?string $entryOwnerId = null)
    {
        $this->config = $config;
        $this->accountTypes = $config['account_types'];
        $this->statements = $config['statements'];
        $this->ownerId = $entryOwnerId;
    }

    function getAccountBalance(Account $account, $debit = true, $cash_only = false, ?CarbonPeriod $period = null): int
    {
        return Entry::query()
            ->where(function (Builder $query) use ($period) {
                if (!is_null($period)) {
                    return $query
                        ->whereDate('created_at', '>=', $period->start)
                        ->whereDate('created_at', '<=', $period->end);
                }
                return $query;
            })
            ->where(function (Builder $query) use ($cash_only) {
                if ($cash_only) {
                    return $query->where('is_cash', true);
                }
                return $query;
            })
            ->where('account_id', $account->getCode())
            ->where('type', $debit ? Entry::TYPE_DEBIT : Entry::TYPE_CREDIT)
            ->sum('amount');
    }

    function getAccountTypeBalance(string $accountTypeCode, $debit = true, $cash_only = false, ?CarbonPeriod $period = null): int
    {
        return Entry::query()
            ->where(function (Builder $query) use ($period) {
                if (!is_null($period)) {
                    return $query
                        ->whereDate('created_at', '>=', $period->start)
                        ->whereDate('created_at', '<=', $period->end);
                }
                return $query;
            })
            ->where(function (Builder $query) use ($cash_only) {
                if ($cash_only) {
                    return $query->where('is_cash', true);
                }
                return $query;
            })
            ->whereHas(Account::class, function (Builder $query) use ($accountTypeCode) {
                return $query->where('account_type_code', $accountTypeCode);
            })
            ->where('type', $debit ? Entry::TYPE_DEBIT : Entry::TYPE_CREDIT)
            ->sum('amount');
    }

    function getAllAccounts($sorted = true): Collection
    {
        return Entities\Account::query()->orderBy('code')->get();
    }

    function getAllAccountTypes($sorted = true): Collection
    {
        return $this->accountTypes;
    }

    function getSingleReports($name, ?CarbonPeriod $period = null)
    {
        $rules = $this->statements[$name];
        $is_cash_only = $rules['cash_only'];
        $left_account_types = $rules['left'];
        $right_account_types = $rules['right'];

        $result = [
            'name' => $name,
            'left' => [],
            'right' => [],
            'total_left_balance' => 0,
            'total_right_balance' => 0,
            'final_balance' => 0
        ];

        foreach ($left_account_types as $account_type_code) {
            $account_type = $account_type_code . ' ' . $this->accountTypes[$account_type_code];

            $debit = $this->getAccountTypeBalance($account_type_code, true, $is_cash_only, $period);
            $credit = $this->getAccountTypeBalance($account_type_code, false, $is_cash_only, $period);

            $result['left'][$account_type] = [
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $debit - $credit
            ];

            $result['total_left_balance'] += $debit - $credit;
        }

        foreach ($right_account_types as $account_type_code) {
            $account_type = $account_type_code . ' ' . $this->accountTypes[$account_type_code];

            $debit = $this->getAccountTypeBalance($account_type_code, true, $period);
            $credit = $this->getAccountTypeBalance($account_type_code, false, $period);

            $result['right'][$account_type] = [
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $debit - $credit
            ];

            $result['total_right_balance'] += $debit - $credit;
        }

        $result['final_balance'] = $result['total_left_balance'] - $result['total_right_balance'];

        return $result;
    }

    function getAllReports(?CarbonPeriod $period = null)
    {
        $result = [];
        foreach ($this->statements as $statement) {
            $result[] = $this->getSingleReports($statement, $period);
        }
        return $result;
    }
}