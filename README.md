# Logger for Slim framework 3
File log writter for Slim framework. Logger works well as class library (inject) or middleware for Slim framework 3 application. Logger created from [Slim-Logger](https://github.com/codeguy/Slim-Logger) and inspired from (Gin)[https://github.com/gin-gonic/gin] log

## Install
You can install Logger via [composer](https://getcomposer.org/).

``
$ composer require silalahi/slim-logger --dev dev-master
``

Requires Slim Framework 3 and PHP 5.5.0 or newer. Visit Logger repository at [Packagist](https://packagist.org/packages/silalahi/slim-logger).

## Usage

### Class Library

To use Logger as class library, you can simply inject Logger instance into Slim container.

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


The code above will create a log file:

```
[INFO] 2015-12-21T01:21:57+07:00 This message from logger class library
[CRITICAL] 2015-12-21T01:22:39+07:00 This is critical error log
[DEBUG] 2015-12-21T01:23:19+07:00 Default log was debug, if you not specified second argument.
```


### Middleware

Logger works as Slim middleware. You can use Logger to log request information such as response status code, latency (in seconds), client IP address, request method and request path:

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

The code above will create a log file:

```
[INFO] 2015-12-21T01:30:58+07:00 |200|0.003357 sec|::1|GET /
[INFO] 2015-12-21T01:31:04+07:00 |200|0.001672 sec|::1|GET /
[INFO] 2015-12-21T01:58:32+07:00 |404|0.001519 sec|::1|GET page-not-found
```

## Settings

Available setting for Logger are:

```
path:
(string) The relative or absolute filesystem path to a writable directory.

name_format:
(string) The log file name format; parsed with `date()`.

extension:
(string) The file extention to append to the filename`.

message_format:
(string) The log message format; available tokens are...
    %label%      Replaced with the log message level (e.g. FATAL, ERROR, WARN).
    %date%       Replaced with a ISO8601 date string for current timezone.
    %message%    Replaced with the log message, coerced to a string.
```

Example settings:

```php
$container['logger'] = function($c) {
  return new Silalahi\Slim\Logger(
    [
      'path' => '.',
      'name_format' => 'Y-m-d',
      'extension' => '.txt',
      'message_format' => '[%label%] %date% %message%'
    ]
  );
};
```