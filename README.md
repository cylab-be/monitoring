# Monitoring

[![pipeline status](https://gitlab.cylab.be/cylab/monitoring/badges/master/pipeline.svg)](https://gitlab.cylab.be/cylab/monitoring/-/commits/master)
[![coverage report](https://gitlab.cylab.be/cylab/monitoring/badges/master/coverage.svg)](https://gitlab.cylab.be/cylab/monitoring/-/commits/master)

## Contributing

To run this tool localy, you will need:

### Installation

```
composer install
touch storage/app/db.sqlite
cp env.dev .env
php artisan migrate
php artisan key:generate
```

To check your installation is correct, you can run the phpunit tests:

```
./vendor/bin/phpunit
```


### Frontend

To build CSS and JS code:

```
npm install
npm run watch
```

### Launch monitoring

```
php artisan serve
```

Then, see your monitoring interface at http://127.0.0.1:8000/
