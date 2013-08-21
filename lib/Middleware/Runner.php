<?php
namespace Middleware;

class Runner implements Middleware {
	private $kickoff;

	function __construct($stack) {
		$this->kickoff = $this->build_call_chain($stack);
	}
	/**
	* implement Middleware#call($env)
	* @param Object $env
	* @return void
	*/
	public function call($env) {
		$this->kickoff->call($env);
	}
	/**
	* @param array $stack format: [ [<string middleware_class_name>, <array constructor_params>], ... ]
	* @return Middleware\Middleware the entry point of the middleware chain
	* @throws Middleware\BuildError
	*/
	private function build_call_chain($stack) {
		$nextMware = null;
		$stack[] = array('Middleware\Terminator', array());

		foreach(array_reverse($stack) as $mwaredef) {
			list($klass, $args) = $mwaredef;
			if($args == null) $args = array();

			$middleware = new \ReflectionClass($klass);
			if(!$middleware->implementsInterface('Middleware\Middleware')) {
				throw new BuildError("class '$klass' must implement Middleware\\Middleware");
			}
			array_unshift($args, $nextMware);
			$constructor = $middleware->getMethod('__construct');
			if(sizeof($args) < $constructor->getNumberOfRequiredParameters() || $constructor->getNumberOfParameters() < sizeof($args)) {
				throw new BuildError(
						$klass
						.' constructor parameter count (excl. $app) is '
						.($constructor->getNumberOfParameters() -1)
						.' but you tried: '
						.(sizeof($args) -1)
				);
			}
			// instantiate the middleware and pass the arguments to its constructor
   		$nextMware = $middleware->newInstanceArgs($args);
		}
		return $nextMware;
	}
}

class Terminator implements Middleware {

	public function __construct($app) {
		// app not needed and null anyway
	}
	/**
	* @return void
	*/
	public function call($env) {
		// do nothing
	}
}

