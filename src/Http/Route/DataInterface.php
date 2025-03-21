<?php

declare(strict_types=1);

namespace Projom\Http\Route;

interface DataInterface
{
	public function expectsPayload(bool $expectsPayload): DataInterface;
	public function expectsQueryParameters(array $expectsQueryParameters): DataInterface;
}