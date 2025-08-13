<?php

declare(strict_types=1);

namespace Projom\Http\Router;

use ReflectionMethod;

use Projom\Http\Request;

class Util
{
	public static function resolveParameters(string $controller, string $method, Request $request): array
	{
		$reflection = new ReflectionMethod($controller, $method);
		$reflectionParameters = $reflection->getParameters();
		if (!$reflectionParameters)
			return [];

		$resolvedParameters = [];
		foreach ($reflectionParameters as $parameter) {
			$parameterName = $parameter->getName();
			$typeName = $parameter->getType()->getName();
			$parameter = static::matchParameter($parameterName, $typeName, $request);
			$resolvedParameters[] = $parameter;
		}

		return $resolvedParameters;
	}

	private static function matchParameter(
		string $parameterName,
		string $typeName,
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

		$parameter = static::castParameter($parameter, $typeName);

		return $parameter;
	}

	private static function castParameter(mixed $parameter, string $typeName): mixed
	{
		$parameter = match ($typeName) {
			'int' => (int)$parameter,
			'float' => (float)$parameter,
			'string' => (string)$parameter,
			'bool' => (bool)$parameter,
			default => $parameter
		};
		return $parameter;
	}
}
