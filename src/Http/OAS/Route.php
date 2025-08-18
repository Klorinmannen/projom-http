<?php

declare(strict_types=1);

namespace Projom\Http\OAS;

use Projom\Http\Method;
use Projom\Http\OAS\Route\Data;
use Projom\Http\Route\Action;
use Projom\Http\Route\RouteBase;

class Route extends RouteBase
{
	public function setData(Method $method, Data $data): void
	{
		$this->methodData[$method->name] = $data;
	}

	public function setup(): void
	{
		$controller = $this->matchedData->controllerDetails['controller'];
		$controllerMethod = $this->matchedData->controllerDetails['method'];
		$this->action = Action::create($controller, $controllerMethod);
	}
}
