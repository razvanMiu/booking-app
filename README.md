# Booking App

[![N|Solid](https://cldup.com/dTxpPi9lDf.thumb.png)](https://nodesource.com/products/nsolid)

## MongoDB

Instalation using composer:
```
composer require jenssegers/mongodb
```

Add the service provider in config/app.php:
```
Jenssegers\Mongodb\MongodbServiceProvider::class,
```

Every model should extend this class:
```
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
```

### Configuration
In ```config/database.php```
```
'mongodb' => [
    'driver'   => 'mongodb',
    'host'     => env('DB_HOST', 'localhost'),
    'port'     => env('DB_PORT', 27017),
    'database' => env('DB_DATABASE'),
    'username' => env('DB_USERNAME'),
    'password' => env('DB_PASSWORD'),
],
```
And also change the constants in ```.env``` file

## Depencencies
Run the following commands in terminal
- ```composer require guzzlehttp/guzzle```
- ```npm install image-map```
- ```npm install --save vue-full-calendar```
- ```npm install --save-dev babel-preset-stage-2```
- ```npm install axios --save```
- ```npm install sweetalert --save```
- ```npm install datatables.net```
- ```npm install datatables.net-dt```
- ```npm install vue-tables-2```
- ```npm install vuetify --save```
- ```npm install --save vue-flash-message```
Don't forget about ```@import '~fullcalendar/dist/fullcalendar.css';```
Issues:
- Can add event with 1 day before current day
- In week view you can add event in every day
https://laravel.com/docs/5.6/passport
