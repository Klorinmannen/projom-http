<?php

declare(strict_types=1);

namespace Projom\Http\Router\Route;

interface DataInterface
{
	public function requiredPayload(): DataInterface;
	public function mandatoryQueryParameters(array $definitions): void;
	public function requiredQueryParameters(array $definitions): DataInterface;
	public function optionalQueryParameters(array $definitions): DataInterface;
	public function mandatoryRequestVars(array $definitions): void;
	public function requiredRequestVars(array $definitions): DataInterface;
	public function optionalRequestVars(array $definitions): DataInterface;
}