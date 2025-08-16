<?php

declare(strict_types=1);

namespace Projom\Http\Request;

use SensitiveParameter;

class Input
{
	public function __construct(
		#[SensitiveParameter] public readonly array $request,
		#[SensitiveParameter] public readonly array $server,
		#[SensitiveParameter] public readonly array $files,
		#[SensitiveParameter] public readonly array $cookies,
		#[SensitiveParameter] public readonly string $payload
	) {}

	public static function create(
		#[SensitiveParameter] array $request = [],
		#[SensitiveParameter] array $server = [],
		#[SensitiveParameter] array $files = [],
		#[SensitiveParameter] array $cookies = [],
		#[SensitiveParameter] string $payload = ''
	): Input {
		$input = new Input(
			$request ?: ($_REQUEST ?? []),
			$server ?: ($_SERVER ?? []),
			$files ?: ($_FILES ?? []),
			$cookies ?: ($_COOKIE ?? []),
			$payload ?: (file_get_contents('php://input') ?: '')
		);
		return $input;
	}
}
