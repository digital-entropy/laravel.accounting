# Laravel Accounting

### Setup

Publish config & migration
```
php artisan vendor:publish --tag=accounting.config
php artisan vendor:publish --tag=accounting.migrations
```

Add **Accounting** facade alias into **config/app.php**
```
'Accounting' => \DigitalEntropy\Accounting\Facade\Accounting::class
```

### How to use?

Get all account types
```
Accounting::getAccountTypes();
```

Get all accounts
```
Accounting::getAccounts();
```

Create an Account
```
Accounting::createAccount(...);
```

Update an Account
```
Accounting::updateAccount(...);
```

Delete an Account
```
Accounting::deleteAccount(...);
```

### How to add a record?

Simply
```
Accounting::makeJournal()
    ->addEntry($accountBank, 25750, null, Journal::TYPE_CREDIT)
    ->addEntry($accountAdminFee, 3000, null, Journal::TYPE_DEBIT)
    ->addEntry($accountExpense, 22750, null, Journal::TYPE_DEBIT)
    ->save("memo", "REFNO-01");
```

It's by default will check if the entries are balanced and would thrown an exception when it's not balanced.

If you wanted to disable the strict mode, just pass it like this
```
Accounting::makeJournal()
    ->addEntry($accountBank, 25750, null, Journal::TYPE_CREDIT)
    ->addEntry($accountAdminFee, 3000, null, Journal::TYPE_DEBIT)
    ->addEntry($accountExpense, 22750, null, Journal::TYPE_DEBIT)
    ->save("memo", "REFNO-01", false); // false here means strict-mode = false
```

### How about the financial statements?

Work in progress...