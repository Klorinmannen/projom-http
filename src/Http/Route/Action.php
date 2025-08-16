<?php

declare(strict_types=1);

namespace Projom\Http\Route;

use Projom\Http\Controller;
use Projom\Http\Response;

class Action
{
	public function __construct(
		private string $controller,
		private string $method
	) {}

	public static function create(string $controller, string $method = ''): Action
	{
		return new Action($controller, $method);
	}

	public function setMethod(string $method): void
	{
		$this->method = $method;
	}

	/**
	 * Verification of the built-in action.
	 */
	public function verify(): void
	{
		if (!$this->controller)
			Response::abort('Handler missing controller');
		if (!$this->method)
			Response::abort('Handler missing controller method');

		if (!class_exists($this->controller))
			Response::abort("Controller: {$this->controller}, does not exist");

		// Note: This will match methods by its name, capitalization does not matter.
		if (!method_exists($this->controller, $this->method))
			Response::abort("Controller method: {$this->method}, does not exist");

		$base = Controller::class;
		if (!is_subclass_of($this->controller, $base))
			Response::abort("Controller does not extend: $base");
	}

	/**
	 * Returns the controller and method as an array.
	 *
	 * @return array [$controller: string, $method: string]
	 */
	public function get(): array
	{
		return [$this->controller, $this->method];
	}
}
