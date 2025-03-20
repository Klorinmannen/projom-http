<?php

declare(strict_types=1);

namespace Projom\Http\Route;

class Data
{
	public bool $expectsPayload = false;
	public array $expectsQueryParameters = [];
}
