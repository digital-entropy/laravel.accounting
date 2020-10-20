<?php


namespace DigitalEntropy\Accounting;


use DigitalEntropy\Accounting\Entities\Account;
use DigitalEntropy\Accounting\Exceptions\InvalidInputException;
use Illuminate\Support\Collection;

class Accounting
{
    protected $config;
    protected $accountTypes;
    protected $statements;

    public function __construct($config)
    {
        $this->config = $config;
        $this->accountTypes = $config['account_types'];
        $this->statements = $config['statements'];
    }

    /**
     * @return array
     */
    public function getAccountTypes()
    {
        $accountTypes = $this->accountTypes;
        $result = [];

        foreach ($accountTypes as $code => $name) {
            $result[] = [
                'code' => $code,
                'name' => $name
            ];
        }

        return $result;
    }

    /**
     * @param bool $sorted
     * @param bool $grouped
     * @return Collection
     */
    public function getAccounts(bool $sorted = true, bool $grouped = false)
    {
        $accountQuery = Account::query();

        if ($sorted) {
            $accountQuery = $accountQuery
                ->orderBy('type_code', 'ASC')
                ->orderBy('code', 'ASC');
        }

        $accounts = $accountQuery->get();

        if ($grouped) {
            $accounts = $accounts->groupBy('tenant_id');
        }

        return $accounts;
    }

    /**
     * @param string $code
     * @param string $accountTypeCode
     * @param string $description
     * @param bool $is_cash
     * @param string|null $groupCode
     * @return Account
     * @throws InvalidInputException
     */
    public function createAccount(string $code, string $accountTypeCode, string $description, bool $is_cash = false, ?string $groupCode = null): Account
    {
        if (!isset($this->config['account_types'][$accountTypeCode])) {
            throw new InvalidInputException("The given account type code ($accountTypeCode) is not available");
        }

        /** @var Account $account */
        $account = Account::query()->create([
            'code' => $code,
            'group_code' => $groupCode,
            'description' => $description,
            'type_code' => $accountTypeCode,
            'type_description' => $this->config['account_types'][$accountTypeCode],
            'is_cash' => $is_cash
        ]);

        return $account;
    }

    /**
     * @param string $code
     * @param string $accountTypeCode
     * @param string $description
     * @param bool $is_cash
     * @return Account
     * @throws InvalidInputException
     */
    public function updateAccount(string $code, string $accountTypeCode, string $description, bool $is_cash): Account
    {
        if (!isset($this->config['account_types'][$accountTypeCode])) {
            throw new InvalidInputException("The given account type code ($accountTypeCode) is not available");
        }

        /** @var Account $account */
        $account = Account::query()->findOrFail($code);

        $account->update([
            'code' => $code,
            'description' => $description,
            'type_code' => $accountTypeCode,
            'type_description' => $this->config['account_types'][$accountTypeCode],
            'is_cash' => $is_cash
        ]);

        return $account;
    }

    /**
     * @param $code
     * @return mixed
     */
    public function deleteAccount($code)
    {
        return Account::query()->where('code', $code)->delete();
    }

    public function getGroupSeparator()
    {
        return $this->config['separators']['group'] ?? '';
    }

    public function getTypeSeparator()
    {
        return $this->config['separators']['type'] ?? '';
    }

}