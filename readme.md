Sphinx Search for Laravel 5 Lumen
=======================
Simple Laravel 5 Lumen package for make queries to Sphinx Search.
Forked from sngrl/sphinxsearch package for Laravel 5.

This package was created to import to the site packagist.org and allow installation through Composer (https://getcomposer.org/).

Installation
=======================

Require this package in your composer.json:
 
```php
	"require": {
        /*** Some others packages ***/
		"wneuteboom/sphinxsearch": "dev-master",
	},
```

Run in your console `composer update` command to pull down the latest version of Sphinx Search.


Or just run this in console:

```php
composer require wneuteboom/sphinxsearch:dev-master
```

After updating composer, add the ServiceProvider to the "providers" array in config/app.php:

```php
	'providers' => array(
        /*** Some others providers ***/
        WNeuteboom\SphinxSearch\SphinxSearchServiceProvider::class,
    ),
```

You can add this line to the files, where you may use SphinxSearch:

```php
use WNeuteboom\SphinxSearch;
```

Configuration
=======================

To use Sphinx Search, you need to configure your indexes and what model it should query. To do so, publish the configuration into your app.

```php
php artisan vendor:publish --provider=WNeuteboom\SphinxSearch\SphinxSearchServiceProvider --force
```

This will create the file `config/sphinxsearch.php`. Modify as needed the host and port.

```php
return array (
	'host'    => '127.0.0.1',
	'port'    => 9312,
	'timeout' => 30
);
```


Usage
=======================

Basic query (raw sphinx results)
```php
$sphinx = new SphinxSearch();
	
$sphinx
	->index('products')
	->select('id, name')
	->weights([
		'name' => 1
	])
	->search("string to search")
	->skip(0)
	->take(100)
	->get();

```

Basic query (with Eloquent)
```php
$sphinx = new SphinxSearch;
$sphinx
	->index('products')
	->select('id, name')
	->table(\App\SpecificModel)
	->weights([
		'name' => 1
	])
	->search("string to search")
	->skip(0)
	->take(100)
	->get();
```

License
=======================

WNeuteboom Sphinx Search is open-sourced software licensed under the MIT license
