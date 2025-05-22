<?php

declare(strict_types=1);

namespace Projom\Http\Route;

use Exception;

use Projom\Http\Request;
use Projom\Http\Response;
use Projom\Http\Controller;

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
			throw new Exception('Handler missing controller', 500);
		if (!$this->method)
			throw new Exception('Handler missing controller method', 500);

		if (! class_exists($this->controller))
			throw new Exception("Controller: {$this->controller}, does not exist", 500);

		// Note: This will match methods by its name, capitalization does not matter.
		if (! method_exists($this->controller, $this->method))
			throw new Exception("Controller method: {$this->method}, does not exist", 500);

		$base = Controller::class;
		if (! is_subclass_of($this->controller, $base))
			throw new Exception("Controller does not implement: $base", 500);
	}

	public function call(Request $request): void
	{
		$controller = $this->controller;
		$method = $this->method;
		(new $controller($request, Response::create()))->{$method}();
	}
}
