<?php

declare(strict_types=1);

namespace Projom\Http;

use Projom\Http\ContentType;
use Projom\Http\StatusCode;

class Response extends ResponseBase
{
	public static function json(array|object $data, StatusCode $code = StatusCode::OK): void
	{
		throw new Response(
			$code->value,
			json_encode($data),
			[ContentType::APPLICATION_JSON->value]
		);
	}

	public static function text(string $text, StatusCode $code = StatusCode::OK): void
	{
		throw new Response(
			$code->value,
			$text,
			[ContentType::TEXT_PLAIN->value]
		);
	}

	public static function html(string $html, StatusCode $code = StatusCode::OK): void
	{
		throw new Response(
			$code->value,
			$html,
			[ContentType::TEXT_HTML->value]
		);
	}

	public static function redirect(string $url, array $headers = [], StatusCode $code = StatusCode::MOVED_PERMANENTLY): void
	{
		$headers[] = "Location: $url";
		throw new Response($code->value, headers: $headers);
	}

	public static function ok(null|string $message = null, StatusCode $code = StatusCode::OK): void
	{
		if ($message !== null)
			static::json(['message' => $message], $code);
		throw new Response($code->value);
	}

	public static function abort(null|string $message = null, StatusCode $code = StatusCode::INTERNAL_SERVER_ERROR): void
	{
		if ($message !== null)
			static::json(['message' => $message], $code);
		throw new Response($code->value);
	}

	public static function reject(null|string $message = null, StatusCode $code = StatusCode::BAD_REQUEST): void
	{
		if ($message !== null)
			static::json(['message' => $message], $code);
		throw new Response($code->value);
	}
}
