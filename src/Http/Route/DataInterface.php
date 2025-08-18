<?php

declare(strict_types=1);

namespace Projom\Http\Route;

interface DataInterface
{
	public function requiredPayload(): DataInterface;
	public function mandatoryQueryParameters(array $queryParameterDefinitions): void;
	public function requiredQueryParameters(array $queryParameterDefinitions): DataInterface;
	public function optionalQueryParameters(array $queryParameterDefinitions): DataInterface;
	public function mandatoryRequestVars(array $requestVarDefinitions): void;
	public function requiredRequestVars(array $requestVarDefinitions): DataInterface;
	public function optionalRequestVars(array $requestVarDefinitions): DataInterface;
}