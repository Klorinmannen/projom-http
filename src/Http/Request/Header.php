<?php

declare(strict_types=1);

namespace Projom\Http\Request;

class Header
{
	private array $headers = [];

	public function __construct(array $server)
	{
		$this->setHeaders($server);
	}

	public static function create(array $server = []): Header
	{
		return new Header($server);
	}

	private function setHeaders(array $server): void
	{
		if (! $server)
			return;

		$pattern = '/^HTTP_.+$/';
		$serverKeys = array_keys($server);
		$foundHttpKeys = preg_grep($pattern, $serverKeys);
		$this->headers = array_intersect_key($server, array_flip($foundHttpKeys));
	}

	public function get(null|string $header = null): null|array|string
	{
		if ($header !== null) {
			$header = $this->normalizeHeader($header);
			return $this->headers[$header] ?? null;
		}

		return $this->headers;
	}

	private function normalizeHeader(string $header): string
	{
		$header = strtoupper($header);
		$header = str_replace('-', '_', $header);

		if (! str_starts_with($header, 'HTTP_'))
			$header = 'HTTP_' . $header;

		return $header;
	}

	public function exists(string $header): bool
	{
		$header = $this->normalizeHeader($header);
		return array_key_exists($header, $this->headers);
	}
}
