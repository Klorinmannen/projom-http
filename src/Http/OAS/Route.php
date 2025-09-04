<?php

declare(strict_types=1);

namespace Projom\Http\OAS;

use Projom\Http\Method;
use Projom\Http\OAS\Route\InputDefinition;
use Projom\Http\Router\Route\Action;
use Projom\Http\Router\Route\RouteBase;

class Route extends RouteBase
{
	public function setData(Method $method, InputDefinition $definition): void
	{
		$this->inputDefinitions[$method->name] = $definition;
	}

	public function setup(): void
	{
		$controller = $this->inputDefinition->controllerDetails['controller'];
		$controllerMethod = $this->inputDefinition->controllerDetails['method'];
		$this->action = Action::create($controller, $controllerMethod);
	}
}
