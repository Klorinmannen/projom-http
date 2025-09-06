<?php

declare(strict_types=1);

namespace Projom\Http\Router;

use ReflectionMethod;

use Projom\Http\Request;
use Projom\Http\Router\DispatcherInterface;
use Projom\Http\Router\Route\Action;

class Dispatcher implements DispatcherInterface
{
	public function __construct() {}

	public function processAction(Action $action, Request $request): void
	{
		$action->verify();
		[$controller, $method] = $action->get();
		$parameters = $this->resolveMethodParameters($controller, $method, $request);
		$this->call($controller, $method, $parameters, $request);
	}

	public function call(string $controller, string $method, array $methodParameters, Request $request): void
	{
		(new $controller($request))->{$method}(...$methodParameters);
	}

	public function resolveMethodParameters(string $controller, string $method, Request $request): array
	{
		$reflection = new ReflectionMethod($controller, $method);
		$reflectionParameters = $reflection->getParameters();
		if (!$reflectionParameters)
			return [];

		$resolvedParameters = [];
		foreach ($reflectionParameters as $parameter) {

			$parameterName = $parameter->getName();
			$matchedParameter = static::matchParameter($parameterName, $request);

			$typeName = $parameter->getType()->getName();
			$parameter = static::castParameter($matchedParameter, $parameterName, $typeName);

			$resolvedParameters[] = $parameter;
		}

		return $resolvedParameters;
	}

	private static function matchParameter(
		string $parameterName,
		Request $request
	): mixed {

		$parameter = match ($parameterName) {
			'pathParameters' => $request->pathParameters(),
			'queryParameters' => $request->queryParameters(),
			'requestVars' => $request->vars(),
			'payload' => $request->payload(),
			'headers' => $request->headers(),
			'cookies' => $request->cookies(),
			'files' => $request->files(),
			default => $request->find($parameterName),
		};

		return $parameter;
	}

	private static function castParameter(mixed $parameter, string $parameterName, string $typeName): mixed
	{
		$parameter = match ($typeName) {
			'int' => (int)$parameter,
			'float' => (float)$parameter,
			'string' => (string)$parameter,
			'bool' => (bool)$parameter,
			'object' => (object)[$parameterName => $parameter],
			default => $parameter
		};
		return $parameter;
	}
}
