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

Work in progress...
