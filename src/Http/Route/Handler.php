<?php

declare(strict_types=1);

namespace Projom\Http\Route;

use Projom\Http\Request;
use Projom\Http\Response;
use Projom\Http\Controller;
use Projom\Http\Route\Util;

class Handler
{
	public function __construct(
		private string $controller,
		private string $method
	) {}

	public static function create(string $controller, string $method = ''): Handler
	{
		return new Handler($controller, $method);
	}

	public function setMethod(string $method): void
	{
		$this->method = $method;
	}

	public function verify(): void
	{
		if (!$this->controller)
			Response::abort('Handler missing controller');
		if (!$this->method)
			Response::abort('Handler missing controller method');

		if (!class_exists($this->controller))
			Response::abort("Controller: {$this->controller}, does not exist");

		// Note: This will match methods by its name, capitalization does not matter.
		if (!method_exists($this->controller, $this->method))
			Response::abort("Controller method: {$this->method}, does not exist");

		$base = Controller::class;
		if (!is_subclass_of($this->controller, $base))
			Response::abort("Controller does not extend: $base");
	}

	public function call(Request $request): void
	{
		$controller = $this->controller;
		$method = $this->method;
		$parameters = Util::resolveParameters($controller, $method, $request);
		(new $controller($request))->{$method}(...$parameters);
	}
}
