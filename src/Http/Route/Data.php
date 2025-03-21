<?php

declare(strict_types=1);

namespace Projom\Http\Route;

use Projom\Http\Route\Handler;
use Projom\Http\Route\DataInterface;

class Data implements DataInterface
{
	private readonly null|Handler $handler;
	private bool $expectsPayload = false;
	private array $expectsQueryParameters = [];

	public function __construct(null|Handler $handler)
	{
		$this->handler = $handler;
	}

	public function handler(): null|Handler
	{
		return $this->handler;
	}

	public function hasHandler(): bool
	{
		return $this->handler !== null;
	}

	public function expectsPayload(bool $expectsPayload): Data
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
