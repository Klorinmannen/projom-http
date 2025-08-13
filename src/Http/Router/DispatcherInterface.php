<?php

declare(strict_types=1);

namespace Projom\Http\Router;

use Projom\Http\Request;
use Projom\Http\Route\Action;

interface DispatcherInterface
{
	public function processAction(Action $action, Request $request): void;
}
