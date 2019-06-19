# Monitoring - Webapp


## Contributing

To run this tool localy, you will need:

### Mongodb server

```
sudo apt-get install mongodb
```

On Ubuntu 16.04, this will install mongodb version 2.6 by default.

### Mongodb PHP extension

* https://docs.mongodb.com/ecosystem/drivers/php/
* https://pecl.php.net/package/mongodb

With mongodb 2.6, you will need mongodb-1.4.4:

```
sudo pecl install mongodb-1.4.4
```

### PHP

The extension mongodb-1.4.4 is compatible with PHP 7.2 or less (thus NOT with PHP 7.3).

If you try with PHP7.3, you will encounter errors like

```
PHP symbol lookup error: /usr/lib/php/20180731/mongodb.so: undefined symbol: ZEND_HASH_GET_APPLY_COUNT
```

You can find the complete compatibility matrix at https://docs.mongodb.com/ecosystem/drivers/php/

### Installation

```
composer install
touch storage/app/db.sqlite
cp .env.example .env
php artisan migrate
php artisan key:generate
```

### Fronted

```
npm install
npm run watch
```
