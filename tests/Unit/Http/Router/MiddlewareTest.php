<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http\Router;

use Closure;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use Projom\Http\Middleware\MiddlewareContext;
use Projom\Http\Middleware\MiddlewareInterface;
use Projom\Http\Request;
use Projom\Http\Router\Middleware;

class MiddlewareTest extends TestCase
{
	#[Test]
	public function createReturnsMiddlewareInstance()
	{
		$middleware = $this->createMock(MiddlewareInterface::class);
		$context = MiddlewareContext::BEFORE_ROUTING;
		$instance = Middleware::create($middleware, $context);
		$this->assertInstanceOf(Middleware::class, $instance);
	}

	#[Test]
	public function isContextReturnsTrueForMatchingContext()
	{
		$middleware = $this->createMock(MiddlewareInterface::class);
		$context = MiddlewareContext::BEFORE_DISPATCHING;
		$instance = Middleware::create($middleware, $context);
		$this->assertTrue($instance->isContext(MiddlewareContext::BEFORE_DISPATCHING));
	}

	#[Test]
	public function isContextReturnsFalseForNonMatchingContext()
	{
		$middleware = $this->createMock(MiddlewareInterface::class);
		$context = MiddlewareContext::BEFORE_DISPATCHING_RESPONSE;
		$instance = Middleware::create($middleware, $context);
		$this->assertFalse($instance->isContext(MiddlewareContext::BEFORE_ROUTING));
	}

	#[Test]
	public function processCallsMiddlewareInterface()
	{
		$called = false;
		$closure = function () use (&$called) {
			$called = true;
		};

		$class = new class($closure) implements MiddlewareInterface {
			public function __construct(private Closure $fn) {}
			public function process(Request $request): void
			{
				($this->fn)();
			}
		};
		$instance = Middleware::create($class);
		$instance->process(Request::create());
		$this->assertTrue($called);
	}

	#[Test]
	public function processCallsClosure()
	{
		$called = false;
		$closure = function (Request $request) use (&$called) {
			$called = true;
		};
		$instance = Middleware::create($closure);
		$instance->process(Request::create());
		$this->assertTrue($called);
	}
}
