<?php

declare(strict_types=1);

namespace Projom\Http;

class Input
{
	public array $request;
	public array $server;
	public string $payload;

	public function __construct(array $request, array $server, string $payload)
	{
		$this->request = $request;
		$this->server = $server;
		$this->payload = $payload;
	}

	public static function create(array $request, array $server, string $payload): Input
	{
		return new Input($request, $server, $payload);
	}
}
