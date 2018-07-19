# Monitoring - Webapp


## Installation

Requires PHP mongodb extension:

https://docs.mongodb.com/ecosystem/drivers/php/
https://pecl.php.net/package/mongodb

```
sudo pecl install mongodb-1.4.4
```

```
composer install
touch storage/app/db.sqlite
cp .env.example .env
php artisan migrate
php artisan key:generate
```