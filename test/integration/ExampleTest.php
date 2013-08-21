<?php

class ExampleTest extends \PHPUnit_Framework_TestCase {

	function testShouldWork() {
		$env = run();

		$this->assertEquals(
			array(
				 '--> A'
				,'--> B'
				,'--> C'
				,'<-- C'
				,'<-- B'
				,'<-- A'
	 		),
	 		$env->log
 		);
		$this->assertEquals(
				"--> A\n".
				"--> B\n".
				"--> C\n".
				"<-- C\n".
				"<-- B\n".
				"<-- A"
	 		,
	 		$env->to_string()
 		);
	}
}


function run() {
	//////////////////
	// begin Example

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

	// echo $env->to_string();

	// end Example
	//////////////////

	return $env;
}
