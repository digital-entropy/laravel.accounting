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

