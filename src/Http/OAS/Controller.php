<?php

declare(strict_types=1);

namespace Projom\Http\OAS;

class Controller
{
	public static function normalize(string $controller): array
	{
		$controllerDetails = explode('@', $controller);
		$resourceController = array_shift($controllerDetails) ?? '';
		$resourceController = str_replace('_', '\\',  ucwords($resourceController, '\\'));
		$resourceOperation = array_shift($controllerDetails) ?? '';
		return [
			'controller' => $resourceController,
			'method' =>  $resourceOperation
		];
	}
}
