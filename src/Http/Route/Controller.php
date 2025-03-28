<?php

declare(strict_types=1);

namespace Projom\Http\Route;

use Exception;

use Projom\Http\Request;

abstract class Controller
{
	abstract public function __construct(Request $request);

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
		throw new Exception('Method not implemented', 405);
	}
}
