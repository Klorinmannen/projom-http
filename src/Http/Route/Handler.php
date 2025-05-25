<?php

declare(strict_types=1);

namespace Projom\Http\Route;

use Exception;
use ReflectionClass;
use ReflectionMethod;

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
		$parameters = $this->resolveParameters($controller, $method, $request);
		(new $controller($request, Response::create()))->{$method}(...$parameters);
	}

	private function resolveParameters(string $controller, string $method, Request $request): array
	{
		$reflection = new ReflectionMethod($controller, $method);
		$reflectionParams = $reflection->getParameters();
		if (! $reflectionParams)
			return [];

		$parameters = [];
		foreach ($reflectionParams as $parameter) {

			$parameterName = $parameter->getName();
			$typeName = $parameter->getType()->getName();
			$parameter = $this->matchParameter($parameterName, $typeName, $request);
			$parameters[] = $parameter;
		}

		return $parameters;
	}

	private function matchParameter(
		string $parameterName,
		string $typeName,
		Request $request
	): mixed {

		if (class_exists($typeName))
			return $this->resolveClassParameter($typeName);

		return match ($parameterName) {
			'pathParameters' => $request->pathParameters(),
			'queryParameters' => $request->queryParameters(),
			'requestVars' => $request->vars(),
			'payload' => $request->payload(),
			'headers' => $request->headers(),
			'cookies' => $request->cookies(),
			'files' => $request->files(),
			default => $request->find($parameterName),
		};
	}

	private function resolveClassParameter(string $className): object
	{
		$class = new ReflectionClass($className);
		$constructor = $class->getConstructor();
		if ($constructor === null)
			return $class->newInstanceWithoutConstructor();

		$constructorParams = $constructor->getParameters();

		$dependencies = [];
		foreach ($constructorParams as $parameter) {

			if ($parameter->getType() === null)
				continue;

			$typeName = $parameter->getType()->getName();
			$resolvedClass = $this->resolveClassParameter($typeName);
			$dependencies[] = $resolvedClass;
		}

		return $class->newInstanceArgs($dependencies);
	}
}
