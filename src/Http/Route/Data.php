<?php

declare(strict_types=1);

namespace Projom\Http\Route;

use Projom\Http\Method;
use Projom\Http\Route\DataInterface;

class Data implements DataInterface
{
	private bool $expectsPayload = false;
	private array $expectsQueryParameters = [];

	public function __construct(
		private Method $method,
		private string $controllerMethod
	) {}

	public static function create(Method $method, string $controllerMethod = ''): Data
	{
		return new Data($method, $controllerMethod);
	}

	public function method(): Method
	{
		return $this->method;
	}

	public function controllerMethod(): string
	{
		return $this->controllerMethod;
	}

	public function hasControllerMethod(): bool
	{
		return $this->controllerMethod !== '';
	}

	public function expectsPayload(bool $expectsPayload = true): Data
	{
		$this->expectsPayload = $expectsPayload;
		return $this;
	}

	public function expectsQueryParameters(array $expectsQueryParameters): Data
	{
		$this->expectsQueryParameters = $expectsQueryParameters;
		return $this;
	}

	public function expectedInput(): array
	{
		return [
			'query' => $this->expectsQueryParameters,
			'payload' => $this->expectsPayload,
		];
	}
}
