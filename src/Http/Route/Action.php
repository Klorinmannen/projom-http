<?php

declare(strict_types=1);

namespace Projom\Http\Route;

class Action
{
	private array $action = '';

	public function __construct(array $action)
	{
		$this->action = $action;	
	}

	public static function create(string|array $action): Action
	{
		return new Action($action);
	}
}