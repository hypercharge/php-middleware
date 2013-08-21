<?php
namespace Middleware;

class MyRunner implements Middleware {
	public $stack;
	public function __construct($stack) {
		$this->stack = $stack;
	}

	public function call($env) {
		// do nothing
	}
}

class BuilderTest extends \PHPUnit_Framework_TestCase {

	function testConstructorShouldConfigureRunner() {
		$b = new Builder(array('runner_klass'=>'Middleware\MyRunner'));
		$b->uses('MW 1');
		$runner = $b->toApp();
		$this->assertEquals(array(
				array('MW 1', array())
			),
			$runner->stack
		);
	}

	function testUsesIsChainable2() {
		$b = new Builder();
		$b->uses('Middleware 1')->uses('Middleware 2');
		$this->assertEquals(array(
				 array('Middleware 1', array())
				,array('Middleware 2', array())
			)
			,$b->getStack()
		);
	}

	function testUsesIsChainable3() {
		$b = new Builder();
		$b->uses('Middleware 1')->uses('Middleware 2')->uses('Middleware 3');
		$this->assertEquals(array(
				 array('Middleware 1', array())
				,array('Middleware 2', array())
				,array('Middleware 3', array())
			)
			,$b->getStack()
		);
	}

	function testToAppShouldHandleEmptyStack() {
		$b = new Builder();
		$a = $b->toApp();
		$this->assertInstanceOf('Middleware\Runner', $a);
		$env = new \StdClass();
		$a->call($env);
	}
}