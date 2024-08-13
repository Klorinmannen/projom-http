<?php

declare(strict_types=1);

namespace Projom\Http;

use Projom\Util\File;

class Input
{
	private readonly array $request;
	private readonly string $method;
	private readonly string $url;
	private readonly array $headers;

	public function __construct(array $request, array $server)
	{
		$this->request = $request;
		$this->headers = $this->parseHeaders($server);
		$this->method = $server['REQUEST_METHOD'] ?? '';
		$this->url = $server['REQUEST_URI'] ?? '';
	}

	public static function create(array $request, array $server): Input
	{
		return new Input($request, $server);
	}

	public function parseHeaders(array $server): array
	{
		$pattern = '/^HTTP_.*$/';
		$resultKeys = preg_grep($pattern, array_keys($server));
		return array_intersect_key($server, array_flip($resultKeys));
	}

	public function get(string $key, mixed $default = null): mixed
	{
		return $this->request[$key] ?? $default;
	}

	public function data(string $source): string
	{
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
