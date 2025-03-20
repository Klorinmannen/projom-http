<?php

declare(strict_types=1);

namespace Projom\Http\Route;

class Controller
{
	public function get(array $pathParamers, array $queryParameters, string $payload): void
	{
		$this->methodNotImplemented();
	}

	public function post(array $pathParamers, array $queryParameters, string $payload): void
	{
		$this->methodNotImplemented();
	}

	public function put(array $pathParamers, array $queryParameters, string $payload): void
	{
		$this->methodNotImplemented();
	}

	public function delete(array $pathParamers, array $queryParameters, string $payload): void
	{
		$this->methodNotImplemented();
	}

	private function methodNotImplemented(): void
	{
		throw new \Exception("Method not implemented", 405);
	}
}
