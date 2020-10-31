# Laravel Accounting

### Setup

Publish config & migration
```
php artisan vendor:publish --tag=accounting.config
php artisan vendor:publish --tag=accounting.migrations
```

Add **Accounting** facade alias into **config/app.php**
```php
'Accounting' => \DigitalEntropy\Accounting\Facade\Accounting::class
```

### How to use?

Get all account types
```php
Accounting::getAccountTypes();
```

Get all accounts
```php
Accounting::getAccounts();
```

Create an Account
```php
Accounting::createAccount(...);
```

Update an Account
```php
Accounting::updateAccount(...);
```

Delete an Account
```php
Accounting::deleteAccount(...);
```

### How to add a record?

Simply
```php
Accounting::makeJournal()
    ->addEntry($accountBank, 25750, null, Journal::TYPE_CREDIT)
    ->addEntry($accountAdminFee, 3000, null, Journal::TYPE_DEBIT)
    ->addEntry($accountExpense, 22750, null, Journal::TYPE_DEBIT)
    ->save("memo", "REFNO-01");
```

It's by default will check if the entries are balanced and would thrown an exception when it's not balanced.

If you wanted to disable the strict mode, just pass it like this
```php
Accounting::makeJournal()
    ->addEntry($accountBank, 25750, null, Journal::TYPE_CREDIT)
    ->addEntry($accountAdminFee, 3000, null, Journal::TYPE_DEBIT)
    ->addEntry($accountExpense, 22750, null, Journal::TYPE_DEBIT)
    ->save("memo", "REFNO-01", false); // false here means strict-mode = false
```

### How about the financial statements?

Work in progress...
