<?php

declare(strict_types=1);

namespace Projom\Http\Request;

class Input
{
	public function __construct(
		public array $request,
		public array $server,
		public array $files,
		public array $cookies,
		public string $payload
	) {}

	public static function create(): Input
	{
		return new Input(
			$_REQUEST ?? [],
			$_SERVER ?? [],
			$_FILES ?? [],
			$_COOKIE ?? [],
			file_get_contents('php://input') ?: ''
		);
	}
}
