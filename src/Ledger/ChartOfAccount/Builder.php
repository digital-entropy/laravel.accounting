<?php


namespace DigitalEntropy\Accounting\Ledger\ChartOfAccount;


use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DigitalEntropy\Accounting\Contracts\Journal\Entry;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Collection;

class Builder
{
    /**
     * @var EloquentBuilder
     */
    private EloquentBuilder $query;

    /**
     * @var string
     */
    private string $entry;

    /**
     * @var string
     */
    public string $account;

    /**
     * @var bool
     */
    public bool $accumulated;

    /**
     * @var CarbonPeriod
     */
    private CarbonPeriod $period;

    public function __construct()
    {
        $defaultStartDate = Carbon::now()->startOfMonth()->toDateString();
        $defaultEndDate = Carbon::now()->endOfMonth()->toDateString();
        $this->period = CarbonPeriod::create($defaultStartDate, $defaultEndDate);

        $this->entry = config('accounting.models.entry');
        $this->account = config('accounting.models.account');

        $this->query = $this->account::query();
    }

    /**
     * Set period
     *
     * @param CarbonPeriod|null $period
     * @return Builder
     */
    public function period(?CarbonPeriod $period)
    {
        if (! is_null($period)) {
            $this->period = $period;
        }

        return $this;
    }

    /**
     * Set accumulating period
     *
     * @param $accumulating
     * @return Builder
     */
    public function accumulated($accumulating)
    {
       $this->accumulated = $accumulating;

       return $this;
    }

    /**
     * Add balance into selected account
     *
     * @return $this
     */
    public function withBalance()
    {
        $this->query
            ->whereHas('entries', function ($query) {
                return $this->queryWithinPeriod($query);
            })->addSelect([
            'debit' => $this->queryWithinPeriod($this->entry::query())
                ->selectRaw('sum(amount)')
                ->whereColumn('account_id', 'accounts.id')
                ->where('type', Entry::TYPE_DEBIT),
            'credit' => $this->queryWithinPeriod($this->entry::query())
                ->selectRaw('sum(amount)')
                ->whereColumn('account_id', 'accounts.id')
                ->where('type', Entry::TYPE_CREDIT)
        ]);

        return $this;
    }

    /**
     * Pick specific date.
     *
     * @param \Illuminate\Database\Query\Builder $builder
     * @return \Illuminate\Database\Query\Builder
     */
    private function queryWithinPeriod(\Illuminate\Database\Query\Builder $builder) {
        $builder->whereDate('date', '<=', $this->period->end);

        if (! $this->accumulated) {
            $builder->whereDate('date', '>=', $this->period->start);
        }

        return $builder;
    }

    /**
     * Get only cash account
     *
     * @param bool $cashOnly
     * @return $this
     */
    public function cash(bool $cashOnly = true)
    {
        $this->query->where('is_cash', $cashOnly);

        return $this;
    }

    /**
     * Get specific group code
     *
     * @param string|null $code
     * @return $this
     */
    public function groupCode(?string $code)
    {
        if (! is_null($code)) {
            $this->query->where('group_code', $code);
        }

        return $this;
    }

    /**
     * Get specific account type_code.
     *
     * @param mixed ...$types
     * @return $this
     */
    public function accountTypeCode(...$types)
    {
        if (is_array($types[0])) {
            $this->query->whereIn('type_code', $types[0]);
        } else {
            $this->query->where('type_code', $types);
        }

        return $this;
    }

    /**
     * Get accounts but grouped by account type code.
     *
     * @return Collection
     */
    public function groupByAccountTypeCode(): Collection
    {
        return $this->get()->groupBy('type_code');
    }

    /**
     * Return collection of account
     *
     * @return Collection
     */
    public function get(): Collection
    {
        return $this->query->get();
    }

    /**
     * Return query of account
     *
     * @return EloquentBuilder
     */
    public function getQuery()
    {
        return $this->query;
    }
}
