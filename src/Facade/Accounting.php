<?php


namespace DigitalEntropy\Accounting\Facade;


use DigitalEntropy\Accounting\AccountingManager;
use DigitalEntropy\Accounting\Entities\Account;
use DigitalEntropy\Accounting\JournalFactory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * Class Accounting
 * @package DigitalEntropy\Accounting\Facade
 *
 * @method static Collection getAccountTypes()
 * @method static Collection getAccounts(bool $sorted = true, bool $grouped = false)
 * @method static Account createAccount(string $code, string $accountTypeCode, string $description, bool $is_cash = false, ?string $groupCode = null)
 * @method static Account updateAccount(string $code, string $accountTypeCode, string $description, bool $is_cash)
 * @method static deleteAccount($code)
 * @method static JournalFactory makeJournal()
 */
class Accounting extends Facade
{
    protected static function getFacadeAccessor()
    {
        return AccountingManager::class;
    }

}