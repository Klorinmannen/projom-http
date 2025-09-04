<?php

declare(strict_types=1);

namespace Projom\Http\Router\Route;

interface InputDefinitionInterface
{
	public function requiredPayload(): InputDefinitionInterface;
	public function mandatoryQueryParameters(array $definitions): void;
	public function requiredQueryParameters(array $definitions): InputDefinitionInterface;
	public function optionalQueryParameters(array $definitions): InputDefinitionInterface;
	public function mandatoryRequestVars(array $definitions): void;
	public function requiredRequestVars(array $definitions): InputDefinitionInterface;
	public function optionalRequestVars(array $definitions): InputDefinitionInterface;
}