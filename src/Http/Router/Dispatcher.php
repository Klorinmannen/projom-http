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
		$parameters = Util::resolveParameters($controller, $method, $request);
		(new $controller($request))->{$method}(...$parameters);
	}
}
