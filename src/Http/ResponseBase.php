<?php

declare(strict_types=1);

namespace Projom\Http;

use Exception;

class ResponseBase extends Exception
{
	private array $headers = [];

	public function __construct(
		int $code,
		string $message = '',
		array $headers = []
	) {
		parent::__construct($message, $code);
		$this->headers = $headers;
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
