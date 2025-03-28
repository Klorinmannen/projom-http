<?php

declare(strict_types=1);

namespace Projom\Http\OAS;

class Payload
{
	public static function normalize(array $expectedPayload): array
	{
		if (!$expectedPayload)
			return [];

		$type = '';
		if ($content = $expectedPayload['content'] ?? [])
			$type = key($content);

		$required = (bool) ($expectedPayload['required'] ?? true);

		return [
			'type' => $type,
			'required' => $required
		];
	}

	public static function verify(string $inputPayload, array $expectedPayload): bool
	{
		// Nothing to check.
		if (!$expectedPayload)
			return true;

		$type = $expectedPayload['type'];
		if (!$type)
			return false;

		if ($expectedPayload['required']) {
			if (!$inputPayload)
				return false;
		} else {
			if (!$inputPayload)
				return true;
		}

		return match ($type) {
			'application/json' => ! empty($inputPayload),
			default => false
		};
	}
}
