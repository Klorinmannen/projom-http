<?php

declare(strict_types=1);

namespace Projom\Http;

use Projom\Http\Response;
use Projom\Http\Request;
use Projom\Http\StatusCode;

abstract class Controller
{
	public function __construct(protected Request $request) {}

	public function get(): void
	{
		$this->methodNotImplemented();
	}

	public function post(): void
	{
		$this->methodNotImplemented();
	}

	public function put(): void
	{
		$this->methodNotImplemented();
	}

	public function delete(): void
	{
		$this->methodNotImplemented();
	}

	private function methodNotImplemented(): void
	{
		Response::abort(StatusCode::NOT_IMPLEMENTED);
	}
}
