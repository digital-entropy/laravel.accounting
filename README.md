# Laravel Accounting

![LaravelAccounting Build Status](https://github.com/digital-entropy/laravel.accounting/workflows/Build/badge.svg)

### Setup

Publish config & migration
```
php artisan vendor:publish --tag=accounting.config
php artisan vendor:publish --tag=accounting.migrations
```

Add **Accounting** facade alias into **config/app.php**
```php
'Accounting' => \Dentro\Accounting\Facade\Accounting::class
```

### How to use?

Work in progress...
