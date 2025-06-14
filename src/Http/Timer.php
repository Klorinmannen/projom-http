<?php

declare(strict_types=1);

namespace Projom\Http;

class Timer
{
	public function __construct(private float $startTime, private float $endTime) {}

	public static function create(): Timer
	{
		return new Timer(0.0, 0.0);
	}

	public function start(): void
	{
		$this->startTime = microtime(true);
	}

	public function stop(): void
	{
		$this->endTime = microtime(true);
	}

	public function elapsed(): float
	{
		return $this->endTime - $this->startTime;
	}
}
