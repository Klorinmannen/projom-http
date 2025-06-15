<?php

declare(strict_types=1);

namespace Projom\Http\Request;

class Timer
{
	public function __construct(private float $startTime) {}

	public static function create(): Timer
	{
		return new Timer(microtime(as_float: true));
	}

	public function elapsed(): float
	{
		$now = microtime(as_float: true);
		return $now - $this->startTime;
	}
}
