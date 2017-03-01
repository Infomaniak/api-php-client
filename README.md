![infomaniak's logo](https://www.infomaniak.com/img/common/logo_infomaniak.jpg) api-php-client
=======================================================

Introduction
------------

infomaniak/api-php-client is a PHP client for Infomaniak API. This client will provide documentation of the services available, describing URIs, HTTP methods and input parameters.


Installation
------------

You can install infomaniak/api-php-client using Composer:

Quick integration with the following command:

```
composer require infomaniak/api-php-client
```

Or add it to the `require` section of your project's `composer.json`.

```
"infomaniak/api-php-client": "0.1"
```

Usage
-----

```php
<?php

require 'vendor/autoload.php';
use Infomaniak\Api;

$token = '123456789';
$client = new Api(['token' => $token]);

// Ping example
$result = $client->ping();
print_r($result);

// List mailbox example
$result = $client->listMailbox(
	array(
		'id'   => 123456789,
		'with' => '*'
	)
);
print_r($result);