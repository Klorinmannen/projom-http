<?php

declare(strict_types=1);

namespace Projom\Http;

use Projom\Http\ContentType;
use Projom\Http\ResponseBase;
use Projom\Http\Response\Code;

class Response extends ResponseBase
{
	public static function json(array|object $data, Code $code = Code::OK): void
	{
		throw new Response(
			$code->value,
			json_encode($data),
			[ContentType::APPLICATION_JSON->value]
		);
	}

	public static function text(string $text, Code $code = Code::OK): void
	{
		throw new Response(
			$code->value,
			$text,
			[ContentType::TEXT_PLAIN->value]
		);
	}

	public static function html(string $html, Code $code = Code::OK): void
	{
		throw new Response(
			$code->value,
			$html,
			[ContentType::TEXT_HTML->value]
		);
	}

	public static function redirect(string $url, array $headers = [], Code $code = Code::MOVED_PERMANENTLY): void
	{
		$headers[] = "Location: $url";
		throw new Response($code->value, headers: $headers);
	}

	public static function ok(null|string $message = null, Code $code = Code::OK): void
	{
		if ($message !== null)
			static::json(['message' => $message], $code);
		throw new Response($code->value);
	}

	public static function abort(null|string $message = null, Code $code = Code::INTERNAL_SERVER_ERROR): void
	{
		if ($message !== null)
			static::json(['message' => $message], $code);
		throw new Response($code->value);
	}

	public static function reject(null|string $message = null, Code $code = Code::BAD_REQUEST): void
	{
		if ($message !== null)
			static::json(['message' => $message], $code);
		throw new Response($code->value);
	}
}
