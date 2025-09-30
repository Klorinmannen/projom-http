<?php

declare(strict_types=1);

namespace Projom\Http\Router\Route\Input;

/**
 * Public interface for route input definitions.
 */
interface DefinitionInterface
{
	public function requiredPayload(): DefinitionInterface;
	public function mandatoryQueryParameters(array $definitions): void;
	public function requiredQueryParameters(array $definitions): DefinitionInterface;
	public function optionalQueryParameters(array $definitions): DefinitionInterface;
	public function mandatoryRequestVars(array $definitions): void;
	public function requiredRequestVars(array $definitions): DefinitionInterface;
	public function optionalRequestVars(array $definitions): DefinitionInterface;
}