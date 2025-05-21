<?php

declare(strict_types=1);

namespace Projom\Http\Request;

use Projom\Http\StatusCode;

class Response
{
	public function __construct() {}

	public static function create(): Response
	{
		return new Response();
	}

	public function json(array|object $data, StatusCode $code = StatusCode::OK): void
	{
		header('Content-Type: application/json', response_code: $code->value);
		echo json_encode($data);
		exit;
	}

	public function text(string $text, StatusCode $code = StatusCode::OK): void
	{
		header('Content-Type: text/plain', response_code: $code->value);
		echo $text;
		exit;
	}

	public function html(string $html, StatusCode $code = StatusCode::OK): void
	{
		header('Content-Type: text/html', response_code: $code->value);
		echo $html;
		exit;
	}

	public function redirect(string $url, StatusCode $code = StatusCode::FOUND): void
	{
		header('Location: ' . $url, response_code: $code->value);
		exit;
	}

	public function ok(): void
	{
		http_response_code(StatusCode::OK->value);
		exit;
	}

	public function abort(StatusCode $code = StatusCode::INTERNAL_SERVER_ERROR): void
	{
		http_response_code($code->value);
		exit;
	}

	public function reject(string $errorMessage, StatusCode $code = StatusCode::BAD_REQUEST): void
	{
		$data = [
			'error_message' => $errorMessage
		];
		$this->json($data, $code);
	}
}
