<?php

declare(strict_types=1);

namespace Projom\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use Projom\Http\Controller;
use Projom\Http\Request;
use Projom\Http\Request\Input;
use Projom\Http\Route\Action;
use Projom\Http\Route\RouteInterface;
use Projom\Http\Router;
use Projom\Http\Router\DispatcherInterface;

class UserController extends Controller
{
	public function get(): void
	{
		// Simulate processing of get action - do nothing.

	}

	public function post(): void
	{
		// Simulate processing of post action - do nothing.
	}
}

class RouterTest extends TestCase
{
	public static function routeDataProvider(): array
	{
		return [
			[
				'GET',
				Request::create(
					Input::create(
						server: ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/users?page=1&limit=10'],
					)
				)
			],
			[
				'POST',
				Request::create(
					Input::create(
						server: ['REQUEST_METHOD' => 'POST', 'REQUEST_URI' => '/users'],
						payload: '{"number": 123}'
					)
				)

			],
		];
	}

	#[Test]
	#[DataProvider('routeDataProvider')]
	public function route(string $method, Request $request): void
	{
		$router = new Router();

		$router->addRoute('/users', UserController::class, function (RouteInterface $route) {
			$route->get()->optionalQueryParameters(['page' => 'integer', 'limit' => 'integer']);
			$route->post()->expectsPayload();
		});

		[$controller, $controllerMethod] = $router->route($request);

		$this->assertEquals(UserController::class, $controller);
		$this->assertEquals($method, $controllerMethod);
	}

	#[Test]
	public function dispatch(): void
	{
		$this->expectNotToPerformAssertions();

		$dispatcher = new class implements DispatcherInterface {
			public function processAction(Action $action, Request $request): void
			{
				// Simulate processing of the action and request - do nothing.
			}
		};
		$router = new Router($dispatcher);

		$router->addRoute('/users', UserController::class, function (RouteInterface $route) {
			$route->get()->optionalQueryParameters(['page' => 'integer', 'limit' => 'integer']);
		});

		$request = Request::create(
			Input::create(
				server: ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/users?page=1&limit=10'],
			)
		);
		$router->dispatch($request);
	}
}
