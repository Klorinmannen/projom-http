<?php

declare(strict_types=1);

namespace Projom\Http;

use Projom\Util\File;

class Input
{
	private $request = [];
	private $server	= [];

	public function __construct(
		array $request,
		array $server
	) {
		$this->request = $request;
		$this->server = $server;
	}

	public function get(
		string $key,
		mixed $default = ''
	): mixed {
		return $this->request[$key] ?? $default;
	}

	public function data(string $source): string
	{
		if (!$source)
			return '';

		if (!File::isReadable($source))
			return '';

		return file_get_contents($source);
	}

	public function method(): string
	{
		return $this->server['REQUEST_METHOD'] ?? '';
	}

	public function url(): string
	{
		return $this->server['REQUEST_URI'] ?? '';
	}

	public function headers(): array
	{
		$pattern = '/^HTTP_.*$/';
		$resultKeys = preg_grep($pattern, array_keys($this->server));
		return array_intersect_key($this->server, array_flip($resultKeys));
	}
}
