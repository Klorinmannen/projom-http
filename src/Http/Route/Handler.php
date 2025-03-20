<?php

declare(strict_types=1);

namespace Projom\Http\Route;

use Projom\Http\Method;

class Handler
{
	private array $handler;
	private bool $defaultMethod = false;

	public function __construct(string $handler, string $method)
	{
		$this->handler = [$handler];
		if ($method)
			$this->handler[] = $method;
		
		$this->defaultMethod = $method ? false : true;
	}

	public static function create(string $handler, string $method = ''): Handler
	{
		return new Handler($handler, $method);
	}

	public function requiresDefaultMethod(): bool
	{
		return $this->defaultMethod;
	}

	public function setDefaultMethod(Method $method): void
	{
		$this->handler = [$this->handler, strtolower($method->name)];
	}

	public function verify(): void
	{
		if (count($this->handler) !== 2)
			throw new \Exception('Handler array requires two elements', 500);

		[$class, $method] = $this->handler;

		if (! class_exists($class))
			throw new \Exception('Handler class does not exist', 500);

		// Note: This will match methods by its name, captialization does not matter.
		if (! method_exists($class, $method))
			throw new \Exception('Handler class method does not exist', 500);

		if (! is_subclass_of($class, Controller::class))
			throw new \Exception('Handler class invalid', 500);
	}

	public function call(array $params): void
	{
		[$class, $method] = $this->handler;
		(new $class)->{$method}(...$params);
	}
}
