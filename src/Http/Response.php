<?php

declare(strict_types=1);

namespace Projom\Http;

use Exception;

use Projom\Http\ContentType;
use Projom\Http\StatusCode;

class Response extends Exception
{
	public function __construct(
		int $code,
		string $message = '',
		public array $headers = []
	) {
		parent::__construct($message, $code);
	}

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

	public static function redirect(string $url, StatusCode $code = StatusCode::FOUND): void
	{
		throw new Response($code->value, headers: ['Location: ' . $url]);
	}

	public static function ok(StatusCode $code = StatusCode::OK): void
	{
		throw new Response($code->value);
	}

	public static function abort(StatusCode $code = StatusCode::INTERNAL_SERVER_ERROR): void
	{
		throw new Response($code->value);
	}

	public static function reject(string $message, StatusCode $code = StatusCode::BAD_REQUEST): void
	{
		static::json(['message' => $message], $code);
	}

	public function send(): void
	{
		http_response_code($this->code);

		foreach ($this->headers as $header)
			header($header);

		if ($this->message)
			echo $this->message;

		exit;
	}
}
