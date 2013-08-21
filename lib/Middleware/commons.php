<?php
namespace Middleware;

/**
*
* must have a constructor where first argument is $app and more arguments if you need
*
* public function __construct(Middleware $app, ...) {
*   $this->app;
*   ...
* }
*/
interface Middleware {
	/**
	* public function call($env) {
	*   // do something with $env
	*
	*   // call the next middleware stack
  *   $this->app->call($env);
  *
  *   // do something with $env
	* }
	*
	* $env has to be a variable which So it should be an instance of StdClass or
	* an instance of a class of your own. But do not use array! (arrays are passed by copy in php)
	*
	* @param Object $env
	* @return void
	*/
	public function call($env);

}

class BuildError extends \Exception {

}
