<?php


namespace DigitalEntropy\Accounting\Contracts\Reports;


use Carbon\CarbonPeriod;
use DigitalEntropy\Accounting\Contracts\Account;
use Illuminate\Support\Collection;

interface Statement
{

    /**
     * Get balance of an account by the given type (debit/credit) and period.
     *
     * @param Account $account
     * @param bool $debit
     * @param CarbonPeriod|null $period
     * @return int
     */
    function getAccountBalance(Account $account, $debit = true, $cash_only = false, ?CarbonPeriod $period = null): int;

    /**
     * Get balance of an account type the given type (debit/credit) and period.
     *
     * @param string $accountTypeCode
     * @param bool $debit
     * @param CarbonPeriod|null $period
     * @return int
     */
    function getAccountTypeBalance(string $accountTypeCode, $debit = true, $cash_only = false, ?CarbonPeriod $period = null): int;

    /**
     * Get all accounts sorted by code.
     *
     * @param bool $sorted
     * @return Collection
     */
    function getAllAccounts($sorted = true): Collection;

    /**
     * Get all account types sorted by code.
     *
     * @param bool $sorted
     * @return mixed
     */
    function getAllAccountTypes($sorted = true): Collection;

    /**
     * Get a reports by it's name and by the given period.
     * Based con config.accounting.statements.*
     *
     * @param $name
     * @param CarbonPeriod|null $period
     * @return mixed
     */
    function getSingleReports($name, ?CarbonPeriod $period = null);

    /**
     * Get all reports by the given period.
     *
     * @param CarbonPeriod|null $period
     * @return mixed
     */
    function getAllReports(?CarbonPeriod $period = null);

}