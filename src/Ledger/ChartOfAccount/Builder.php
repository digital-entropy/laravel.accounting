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
     * @var bool
     */
    public bool $appendBalance;

    /**
     * @var string|null
     */
    public ?string $groupCode;

    /**
     * @var CarbonPeriod
     */
    private CarbonPeriod $period;

    public function __construct()
    {
        $this->accumulated = false;
        $this->appendBalance = false;
        $this->groupCode = null;

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
     * @param bool $accumulated
     * @return Builder
     */
    public function period(?CarbonPeriod $period, $accumulated = false): Builder
    {
        if (! is_null($period)) {
            $this->period = $period;
        }

        $this->accumulated = $accumulated;

        return $this;
    }

    /**
     * Add balance into selected account
     *
     * @return $this
     */
    public function appendBalance(): Builder
    {
        $this->appendBalance = true;

        return $this;
    }

    /**
     * Pick specific date.
     *
     * @param EloquentBuilder $builder
     * @return EloquentBuilder
     */
    private function queryWithinPeriod(\Illuminate\Database\Eloquent\Builder $builder): EloquentBuilder
    {
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
    public function cash(bool $cashOnly = true): Builder
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
    public function groupCode(?string $code): Builder
    {
        $this->groupCode = $code;

        return $this;
    }

    /**
     * Get specific account type_code.
     *
     * @param mixed ...$types
     * @return $this
     */
    public function accountTypeCode(...$types): Builder
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
     * Build query
     *
     * @return $this
     */
    private function buildWithBalance(): Builder
    {
        $this->query
            ->whereHas('entries', function ($query) {
                return $this->queryWithinPeriod($query);
            })->addSelect([
                'debit' => $this->queryWithinPeriod($this->entry::query())
                    ->selectRaw('sum(amount)')
                    ->whereHas('journal', function ($builder) {
                        if (! is_null($this->groupCode)) {
                            $builder->where('group_code', $this->groupCode);
                        }
                    })->whereColumn('account_id', 'accounts.id')
                    ->where('type', Entry::TYPE_DEBIT),
                'credit' => $this->queryWithinPeriod($this->entry::query())
                    ->selectRaw('sum(amount)')
                    ->whereHas('journal', function ($builder) {
                        if (! is_null($this->groupCode)) {
                            $builder->where('group_code', $this->groupCode);
                        }
                    })->whereColumn('account_id', 'accounts.id')
                    ->where('type', Entry::TYPE_CREDIT)
            ]);

        return $this;
    }

    /**
     * Return collection of account
     *
     * @return Collection
     */
    public function get(): Collection
    {
        if (! $this->appendBalance) {
            return $this->query->get()->makeHidden('balance');
        } else {
            $this->buildWithBalance();
        }

        return $this->query->get()->filter(fn ($account) => $account->balance > 0);
    }

    /**
     * Return query of account
     *
     * @return EloquentBuilder
     */
    public function getQuery(): EloquentBuilder
    {
        if ($this->appendBalance) {
            $this->buildWithBalance();
        }

        return $this->query;
    }
}
