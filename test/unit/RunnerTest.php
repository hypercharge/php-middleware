<?php
namespace Middleware;

class DummyEnv {
	public $foo = 'bar';
	public $before = array();
	public $after  = array();
}

class MiddlewareWithoutParams implements Middleware {
	private $app;
	private $name;
	public function __construct($app) {
		$this->app = $app;
	}
	public function call($env) {
		$env->before[] = '/ ->';
		$this->app->call($env);
		$env->after[]  = '/ <-';
	}
}

class SimpleMiddleware implements Middleware {
	private $app;
	private $name;
	public function __construct($app, $name) {
		$this->app = $app;
		$this->name = $name;
	}
	public function call($env) {
		$env->before[] = $this->name.' ->';
		$this->app->call($env);
		$env->after[]  = $this->name.' <-';
	}
}

class MiddlewareWithOptionalParam implements Middleware {
	private $app;
	private $a, $b;
	public function __construct($app, $a, $b='default') {
		$this->app = $app;
		$this->a = $a;
		$this->b = $b;
	}
	public function call($env) {
		$env->before[] = $this->a.'-'.$this->b.' ->';
		$this->app->call($env);
		$env->after[]  = $this->a.'-'.$this->b.' <-';
	}
}


class RunnerTest extends \PHPUnit_Framework_TestCase {

	function testShouldHandleEmptyStack() {
		$runner = new Runner(array());
		$env = new DummyEnv();
		$this->assertNull($runner->call($env));
		$this->assertEquals('bar', $env->foo);
	}

	function testShouldHandleOneMiddleware() {
		$runner = new Runner(array(
			array('Middleware\SimpleMiddleware', array('A'))
		));
		$env = new DummyEnv();
		$this->assertNull($runner->call($env));
		$this->assertEquals(array('A ->'), $env->before);
		$this->assertEquals(array('A <-'), $env->after);
	}

	function testShouldHandleTwoMiddleware() {
		$runner = new Runner(array(
			array('Middleware\SimpleMiddleware', array('A'))
		 ,array('Middleware\SimpleMiddleware', array('B'))
		));
		$env = new DummyEnv();
		$this->assertNull($runner->call($env));
		$this->assertEquals(array('A ->','B ->'), $env->before);
		$this->assertEquals(array('B <-','A <-'), $env->after);
	}

	/**
	* @expectedException Middleware\BuildError
	* @expectedExceptionMessage Middleware\SimpleMiddleware constructor parameter count (excl. $app) is 1 but you tried: 0
	*/
	function testShouldThrowIfParamsEmpty() {
		$runner = new Runner(array(
			array('Middleware\SimpleMiddleware', array())
		));
	}

	/**
	* @expectedException Middleware\BuildError
	* @expectedExceptionMessage Middleware\SimpleMiddleware constructor parameter count (excl. $app) is 1 but you tried: 2
	*/
	function testShouldThrowIfParamCount1TooMutch() {
		$runner = new Runner(array(
			array('Middleware\SimpleMiddleware', array('A', 1))
		));
	}

	function testShouldThrowIfParamCountInvalidWithOptionalParamOmited() {
		$runner = new Runner(array(
			array('Middleware\MiddlewareWithOptionalParam', array('A'))
		));
		$env = new DummyEnv();
		$this->assertNull($runner->call($env));
		$this->assertEquals(array('A-default ->'), $env->before);
		$this->assertEquals(array('A-default <-'), $env->after);
	}

	function testShouldThrowIfParamCountInvalidWithOptionalParamSet() {
		$runner = new Runner(array(
			array('Middleware\MiddlewareWithOptionalParam', array('A', 'optional'))
		));
		$env = new DummyEnv();
		$this->assertNull($runner->call($env));
		$this->assertEquals(array('A-optional ->'), $env->before);
		$this->assertEquals(array('A-optional <-'), $env->after);
	}
}
