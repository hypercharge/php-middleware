<?php
namespace Middleware;


class Builder implements Middleware {
	private $stack = array();
	private $runnerKlass = 'Middleware\Runner';

	public function __construct($opts = array()) {
		// should impment middleware!
		if(!empty($opts['runner_klass'])) {
			$this->runnerKlass = $opts['runner_klass'];
		}
	}

	/**
	* additional (1..n) params are passed to constructor of $middleware (s. Middleware\Runner#build_call_chain)
	* note in ruby middleware its called 'use' but in php it has to be 'uses' because 'use' is a reserved word in php.
	* @param string $middleware a class name implementing Middleware\Middleware
	*/
	public function uses() {
		$middleware = func_get_arg(0);
		$args = array_slice(func_get_args(), 1, func_num_args()-1);
		$this->stack[] = array($middleware, $args);
		return $this;
	}

	/**
	* implement Middleware#call($env)
	* @param Object $env
	* @return void
	*/
  public function call($env = null) {
    $this->toApp()->call($env);
  }

	/**
	* @return array a copy of stack
	*/
	public function getStack() {
		return $this->stack;
	}

	/**
	* @return Middleware the entry point of the stack
	*/
	public function toApp() {
		return new $this->runnerKlass($this->stack);
	}
}
