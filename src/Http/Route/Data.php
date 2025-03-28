<?php

declare(strict_types=1);

namespace Projom\Http\Route;

use Projom\Http\Method;
use Projom\Http\Route\Handler;
use Projom\Http\Route\DataInterface;

class Data implements DataInterface
{
	private readonly Method $method;
	private readonly null|Handler $handler;
	private bool $expectsPayload = false;
	private array $expectsQueryParameters = [];

	public function __construct(Method $method, null|Handler $handler)
	{
		$this->method = $method;
		$this->handler = $handler;
	}

	public function method(): Method
	{
		return $this->method;
	}

	public function handler(): null|Handler
	{
		return $this->handler;
	}

	public function hasHandler(): bool
	{
		return $this->handler !== null;
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
