<?php

declare(strict_types=1);

namespace Projom\Http\Request;

class Input
{
	public function __construct(
		public readonly array $request,
		public readonly array $server,
		public readonly array $files,
		public readonly array $cookies,
		public readonly string $payload
	) {}

	public static function create(
		array $request = [],
		array $server = [],
		array $files = [],
		array $cookies = [],
		string $payload = ''
	): Input {
		return new Input(
			$request ?: ($_REQUEST ?? []),
			$server ?: ($_SERVER ?? []),
			$files ?: ($_FILES ?? []),
			$cookies ?: ($_COOKIE ?? []),
			$payload ?: (file_get_contents('php://input') ?: '')
		);
	}
}
