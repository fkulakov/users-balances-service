# Users balances service

## Used technologies:

- PHP 8.2
- PostgreSQL 15
- Redis 7
- Laravel 10

## Installation

```bash
git clone git@github.com:fkulakov/users-balances-service.git
```
```bash
cd users-balances-service
```
```bash
./vendor/bin/sail up
```
Then go to the `laravel.test` container and run the following commands:

This will create 1000 test users with random balances:

```bash
php artisan migrate --seed
```

This will produce 1000 random jobs:
```bash
php artisan jobs:produce-random 1000
```

This will produce AccrualJob for accrual 1000 money for user_id 1:

```bash
php artisan jobs:produce-accrual 1 1000
```

This will produce WriteOffJob for write off 1000 money from user_id 1:

```bash
php artisan jobs:produce-write-off 1 1000
```

This will produce TransferJob for transfer 1000 money from user_id 1 to user_id 2:

```bash
php artisan jobs:produce-write-off 1 2 1000
```

Then run this command to handle produced jobs:

```bash
php artisan queue:listen
```
