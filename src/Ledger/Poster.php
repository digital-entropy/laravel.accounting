<?php


namespace DigitalEntropy\Accounting\Ledger;


use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DigitalEntropy\Accounting\Contracts\Account;
use DigitalEntropy\Accounting\Ledger\ChartOfAccount\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class Poster
{
    /**
     * @var Builder
     */
    private Builder $builder;

    /**
     * @var CarbonPeriod
     */
    private CarbonPeriod $period;

    /**
     * @var EloquentBuilder
     */
    private $query;

    /**
     * Poster constructor.
     * @param Builder $builder
     */
    public function __construct(Builder $builder)
    {
        $this->builder = $builder;

        $now = Carbon::now();

        $this->period = CarbonPeriod::create($now->startOfMonth()->toDateString(), $now->endOfMonth()->toDateString());
        $ledgerClass = config('accounting.models.general_ledger');
        $this->query = $ledgerClass::query();

    }

    /**
     * Set time period.
     *
     * @param CarbonPeriod $period
     * @return Poster
     */
    public function period(CarbonPeriod $period): Poster
    {
        $this->period = $period;

        return $this;
    }

    /**
     * Post the ledger from account.
     *
     * @return $this
     */
    public function post()
    {
        $accounts = $this->builder
            ->withBalance()
            ->period($this->period)
            ->get();

        /** @var Model|Account $account */
        foreach ($accounts as $account) {
            $this->apply($account)->toArray();
        }

        return $this;
    }

    /**
     * Save into database.
     *
     * @param $account
     * @return EloquentBuilder|Model
     */
    private function apply($account)
    {
        return $this->query->updateOrCreate([
            'account_id' => $account->id,
            'period' => $this->period->start->startOfMonth()
        ], [
            'debit' => $account->getDebit(),
            'credit' => $account->getCredit()
        ]);
    }

    /**
     * Get summarized general ledger per account type_code.
     *
     * @return EloquentBuilder
     */
    public function summaryByAccountType(): EloquentBuilder
    {
        $this->prepare();

        return $this->query
            ->selectRaw('accounts.type_code as account_type_code')
            ->groupBy('account_type_code');
    }

    /**
     * Get summarized general ledger per account.
     *
     * @return EloquentBuilder
     */
    public function summary(): EloquentBuilder
    {
        $this->prepare();

        return $this->query
            ->selectRaw('accounts.code as account_code')
            ->selectRaw('accounts.description as account_description')
            ->selectRaw('accounts.group_code as account_group_code')
            ->groupBy('account_code', 'account_name', 'account_group_code');
    }

    /**
     * Prepare default query
     *
     * @return $this
     */
    public function prepare(): Poster
    {
        $this->query
            ->when($this->period->isStartIncluded(), function ($query) {
                $query->whereDate('period', '>=', $this->period->start);
            })->when($this->period->isEndIncluded(), function ($query) {
                $query->whereDate('period', '<=', $this->period->end);
            })
            ->join('accounts', 'general_ledgers.account_id', '=', 'accounts.id')
            ->selectRaw('sum(debit) as debit')
            ->selectRaw('sum(credit) as credit');

        return $this;
    }

}
