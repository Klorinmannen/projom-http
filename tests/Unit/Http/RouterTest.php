<?php

declare(strict_types=1);

namespace Projom\Tests\Unit;

use Closure;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use Projom\Http\Controller;
use Projom\Http\Request;
use Projom\Http\Request\Input;
use Projom\Http\Router;
use Projom\Http\Router\DispatcherInterface;
use Projom\Http\Router\ParameterType;
use Projom\Http\Router\RouteInterface;
use Projom\Http\Router\Route\Action;

class InvoiceController extends Controller
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
				'/invoices',
				Input::create(
					server: ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/invoices?page=1'],
				),
				function (RouteInterface $route) {
					$route->get()
						->requiredQueryParameters(['page' => ParameterType::INT])
						->optionalQueryParameters(['limit' => ParameterType::INT]);
				},
				'GET'
			],
			[
				'/invoices/{numeric_id:invoice_id}/lines',
				Input::create(
					server: ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/invoices/16/lines?page=1&limit=10&sort=asc'],
				),
				function (RouteInterface $route) {
					$route->get()
						->requiredQueryParameters(['page,limit' => ParameterType::INT])
						->optionalQueryParameters(['sort,lang' => ParameterType::STR]);
				},
				'GET'
			],
			[
				'/invoices/{numeric_id:invoice_id}/lines',
				Input::create(
					server: ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/invoices/16/lines?page=1&limit=10'],
				),
				function (RouteInterface $route) {
					$route->get()
						->requiredQueryParameters(['sort,lang' => ParameterType::STR]) // This will be overridden by the mandatoryQueryParameters call.
						->mandatoryQueryParameters(['page,limit' => ParameterType::INT]);
				},
				'GET'
			],
			[
				'/invoices/{numeric_id:invoice_id}/lines/{numeric_id:invoice_line_id}',
				Input::create(
					server: ['REQUEST_METHOD' => 'GET', 'REQUEST_URI' => '/invoices/16/lines/446?lang=en'],
				),
				function (RouteInterface $route) {
					$route->get()->requiredQueryParameters(['lang' => ParameterType::STR]);
				},
				'GET'
			]
		];
	}

	#[Test]
	#[DataProvider('routeDataProvider')]
	public function route(string $requestPath, Input $input, Closure $closure, string $expectedMethod): void
	{
		$router = new Router();
		$router->addRoute($requestPath, InvoiceController::class, $closure);
		[$controller, $controllerMethod] = $router->route(Request::create($input));

		$this->assertEquals(InvoiceController::class, $controller);
		$this->assertEquals($expectedMethod, $controllerMethod);
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

		$router->addRoute('/users', InvoiceController::class, function (RouteInterface $route) {
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
