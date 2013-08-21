# Middleware

This is a generalized library for using middleware patterns within
your PHP projects.

`php-middleware` is a PHP port of the ruby [middleware](https://github.com/mitchellh/middleware) library.

Only a subset of ruby `middleware` is implemented yet. Stay tuned to see more features ported.

To get started, the best place to look is the [the user guide](https://github.com/mitchellh/middleware/blob/master/user_guide.md).

## Installation

This project is distributed as a [composer](http://getcomposer.org/) package.

in your project root folder create a `composer.json` file
```json
{
  "require": {
    "hypercharge/php-middleware": "*"
  }
}
````
In a shell `cd` to your project root folder and run the command
```console
$ php composer.phar install
```

## A Basic Example

Below is a basic example of the library in use. If you don't understand what middleware is, please read [the ruby middleware doc](https://github.com/mitchellh/middleware/blob/master/user_guide.md#middleware). This example is simply meant to give you a quick idea of what the library looks like.

```php
<?php
// basic env instance class
class LogEnv {
	public $log = array();

	public function to_string() {
		return join($this->log, "\n");
	}
}

// Basic middleware that just logs the inbound and
// outbound steps to env
class Trace implements Middleware\Middleware {
	private $app;
	private $value;
	public function __construct($app, $value) {
		$this->app = $app;
		$this->value = $value;
	}
	public function call($env) {
		$env->log[] = '--> '.$this->value;
		$this->app->call($env);
		$env->log[] = '<-- '.$this->value;
	}
}

// the env object passed to each middleware call($env) method
$env = new LogEnv();

// build the actual middleware stack which runs a sequence
// of slightly different versions of our middleware
$stack = new Middleware\Builder();
$stack
	->uses('Trace', 'A')
	->uses('Trace', 'B')
	->uses('Trace', 'C');

// run it
$stack->call($env);

echo $env->to_string();
```
And the output:
```
--> A
--> B
--> C
<-- C
<-- B
<-- A
```

## Run The Tests

```console
$ ./vendor/bin/phpunit
```