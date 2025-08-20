<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http\Router;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use Projom\Http\Request;
use Projom\Http\Request\Input;
use Projom\Http\Router\Dispatcher;
use Projom\Http\Router\Route\Action;

class DummyController
{
	public static bool $called = false;

	public function __construct(Request $request) {}

	public function foo(): void
	{
		static::$called = true;
	}

	public function bar(int $invoiceID): void
	{
		// This method is intentionally left empty for testing purposes.
	}
}

class ActionStub extends Action
{
	public function __construct(string $controller, string $method)
	{
		parent::__construct($controller, $method);
	}

	public function verify(): void
	{
		// Override to prevent actual verification in tests.
	}
}

class DispatcherTest extends TestCase
{
	#[Test]
	public function processActionCallsControllerMethod()
	{
		$action = new ActionStub(DummyController::class, 'foo');
		$dispatcher = new Dispatcher();
		DummyController::$called = false;
		$dispatcher->processAction($action, Request::create());
		$this->assertTrue(DummyController::$called);
	}

	#[Test]
	public function resolveMethodParametersReturnsParameters()
	{
		$request = Request::create(
			Input::create(request: [
				'invoiceID' => '123',
			])
		);
		$dispatcher = new Dispatcher();
		$result = $dispatcher->resolveMethodParameters(DummyController::class, 'bar', $request);
		$this->assertSame([123], $result);
	}

	#[Test]
	public function callInstantiatesControllerAndCallsMethod()
	{
		$dispatcher = new Dispatcher();
		DummyController::$called = false;
		$dispatcher->call(DummyController::class, 'foo', [], Request::create());
		$this->assertTrue(DummyController::$called);
	}
}
