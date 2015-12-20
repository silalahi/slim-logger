# Logger for Slim framework
File log writter class and middleware for Slim framework

## Install
Via [composer](https://getcomposer.org/)

``
$ composer require silalahi/slim-logger --dev dev-master
``

Requires Slim Framework 3 and PHP 5.5.0 or newer.

## Usage

### Library class

Example usage in Slim framework

```php
<?php

require "vendor/autoload.php";

// Don't forget to set timezone
date_default_timezone_set("Asia/Jakarta");

$container = new \Slim\Container();

// Adding logger to Slim container
$container['logger'] = function($c) {
  return new Silalahi\Slim\Logger();
};

$app = new \Slim\App($container);

$app->get('/', function ($request, $response, $args) {

  // Info level log
  $this->logger->write("This message from logger class library", Silalahi\Slim\Logger::INFO);
  // Critical level log
  $this->logger->write("This is critical error log", Silalahi\Slim\Logger::CRITICAL);
  // Debug level log as default
  $this->logger->write("Default log was debug, if you not specified second argument.");

  return $response->write("Hello, world!");
});


$app->run();

```


Output in file:

```
[INFO] 2015-12-21T01:21:57+07:00 This message from logger class library
[CRITICAL] 2015-12-21T01:22:39+07:00 This is critical error log
[DEBUG] 2015-12-21T01:23:19+07:00 Default log was debug, if you not specified second argument.
```

### Middleware

Example usage in Slim framework as Middleware

```php
<?php

require "vendor/autoload.php";

date_default_timezone_set("Asia/Jakarta");

$app = new \Slim\App;

// Adding middleware to Slim App
$app->add(new Silalahi\Slim\Logger());

$app->get('/', function ($request, $response, $args) {
  return $response->write("Hello, World!");
});

$app->run();

```

The output file:

```
[INFO] 2015-12-21T01:30:58+07:00 |200|0.003357 sec|::1|GET /
[INFO] 2015-12-21T01:31:04+07:00 |200|0.001672 sec|::1|GET /
```