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
    private string $account;

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
     * Add balance into selected account
     *
     * @return $this
     */
    public function withBalance()
    {
        $this->query->addSelect([
            'debit' => $this->entry::query()
                ->whereColumn('account_id', 'accounts.id')
                ->where('type', Entry::TYPE_DEBIT)
                ->whereDate('created_at', '>=', $this->period->start)
                ->whereDate('created_at', '<=', $this->period->end)
                ->sum('amount'),
            'credit' => $this->entry::query()
                ->whereColumn('account_id', 'accounts.id')
                ->where('type', Entry::TYPE_CREDIT)
                ->whereDate('created_at', '>=', $this->period->start)
                ->whereDate('created_at', '<=', $this->period->end)
                ->sum('amount')
        ]);

        return $this;
    }

    /**
     * Get only cash account
     *
     * @param bool $cashOnly
     * @return $this
     */
    public function cash(bool $cashOnly)
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
        } else {
            $this->query->whereNull('group_code');
        }

        return $this;
    }

    /**
     * Get specific account type_code.
     *
     * @param mixed ...$codes
     * @return $this
     */
    public function accountTypeCode(...$codes)
    {
        if (is_array($codes)) {
            $this->query->whereIn('type_code', $codes);
        } else {
            $this->query->where('type_code', $codes);
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
