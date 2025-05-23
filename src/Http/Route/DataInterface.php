<?php

declare(strict_types=1);

namespace Projom\Http\Route;

interface DataInterface
{
	public function expectsPayload(bool $expectsPayload = true): DataInterface;
	public function expectsQueryParameters(array $queryParameterDefinitions): DataInterface;
	public function optionalQueryParameters(array $queryParameterDefinitions): DataInterface;
	public function expectsRequestVars(array $requestVarDefinitions): DataInterface;
	public function optionalRequestVars(array $requestVarDefinitions): DataInterface;
}