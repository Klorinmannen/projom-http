<?php

declare(strict_types=1);

namespace Projom\Http;

use Projom\Util\File;

class Input
{
	private $request = [];
	private $method = '';
	private $url = '';
	private $headers = [];

	public function __construct(
		array $request,
		array $server
	) {
		$this->request = $request;
		$this->headers = $this->parseHeaders($server);
		$this->method = $server['REQUEST_METHOD'] ?? '';
		$this->url = $server['REQUEST_URI'] ?? '';
	}

	public function parseHeaders(array $server): array
	{
		$pattern = '/^HTTP_.*$/';
		$resultKeys = preg_grep($pattern, array_keys($server));
		return array_intersect_key($server, array_flip($resultKeys));
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
		return $this->method;
	}

	public function url(): string
	{
		return $this->url;
	}

	public function headers(): array
	{
		return $this->headers;
	}
}
