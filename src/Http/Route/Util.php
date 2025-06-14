<?php

declare(strict_types=1);

namespace Projom\Http\Route;

use ReflectionClass;
use ReflectionMethod;

use Projom\Http\Request;

class Util
{
	public static function resolveParameters(string $controller, string $method, Request $request): array
	{
		$reflection = new ReflectionMethod($controller, $method);
		$reflectionParams = $reflection->getParameters();
		if (! $reflectionParams)
			return [];

		$parameters = [];
		foreach ($reflectionParams as $parameter) {

			$parameterName = $parameter->getName();
			$typeName = $parameter->getType()->getName();
			$parameter = static::matchParameter($parameterName, $typeName, $request);
			$parameters[] = $parameter;
		}

		return $parameters;
	}

	private static function matchParameter(
		string $parameterName,
		string $typeName,
		Request $request
	): mixed {

		if (class_exists($typeName))
			return static::resolveClassParameter($typeName);

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

	private static function resolveClassParameter(string $className): object
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
			$resolvedClass = static::resolveClassParameter($typeName);
			$dependencies[] = $resolvedClass;
		}

		return $class->newInstanceArgs($dependencies);
	}
}
