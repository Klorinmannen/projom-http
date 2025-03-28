<?php

declare(strict_types=1);

namespace Projom\Http\Request;

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

	public static function create(): Input
	{
		return new Input($_REQUEST ?? [], $_SERVER ?? [], file_get_contents('php://input') ?: '');
	}
}
