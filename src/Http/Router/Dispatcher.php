<?php

declare(strict_types=1);

namespace Projom\Http\Router;

use Projom\Http\Request;
use Projom\Http\Route\Action;
use Projom\Http\Router\DispatcherInterface;
use Projom\Http\Router\Util;

class Dispatcher implements DispatcherInterface
{
	public function processAction(Action $action, Request $request): void
	{
		$action->verify();
		[$controller, $method] = $action->get();
		$parameters = Util::resolveMethodParameters($controller, $method, $request);
		$this->call($controller, $method, $parameters, $request);
	}

	public function call(string $controller, string $method, array $methodParameters, Request $request): void
	{
		(new $controller($request))->{$method}(...$methodParameters);
	}
}
