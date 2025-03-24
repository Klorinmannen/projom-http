<?php

declare(strict_types=1);

namespace Projom\Http\Route;

use Exception;

use Projom\Http\Method;
use Projom\Http\Request;
use Projom\Http\Route\Controller;

class Handler
{
	private array $handler;
	private bool $requiresDefaultMethod = false;

	public function __construct(string $handler, string $method)
	{
		$this->handler = [$handler];
		$this->requiresDefaultMethod = $method ? false : true;
		if (! $this->requiresDefaultMethod)
			$this->handler[] = $method;
	}

	public static function create(string $handler, string $method = ''): Handler
	{
		return new Handler($handler, $method);
	}

	public function requiresDefaultMethod(): bool
	{
		return $this->requiresDefaultMethod;
	}

	public function setDefaultMethod(Method $method): void
	{
		$this->handler[] = strtolower($method->name);
	}

	public function verify(): void
	{
		if (count($this->handler) !== 2)
			throw new Exception('Handler array requires two elements', 500);

		[$class, $method] = $this->handler;

		if (! class_exists($class))
			throw new Exception("Handler class: $class, does not exist", 500);

		// Note: This will match methods by its name, captialization does not matter.
		if (! method_exists($class, $method))
			throw new Exception("Handler class method: $method, does not exist", 500);

		$base = Controller::class;
		if (! is_subclass_of($class, $base))
			throw new Exception("Handler class has to be a child of: $base", 500);
	}

	public function call(Request $request): void {
		[$class, $method] = $this->handler;
		(new $class($request))->{$method}();
	}
}
