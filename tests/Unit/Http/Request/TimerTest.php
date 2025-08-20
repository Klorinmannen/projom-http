<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http\Request;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use Projom\Http\Request\Timer;

class TimerTest extends TestCase
{
	#[Test]
	public function elapsedReturnsFloat()
	{
		$timer = Timer::create();
		usleep(1000); // sleep for 1ms
		$elapsed = $timer->elapsed();
		$this->assertIsFloat($elapsed);
		$this->assertGreaterThanOrEqual(0, $elapsed);
	}

	#[Test]
	public function elapsedIncreasesOverTime()
	{
		$timer = Timer::create();
		$first = $timer->elapsed();
		usleep(2000); // sleep for 2ms
		$second = $timer->elapsed();
		$this->assertGreaterThan($first, $second);
	}
}
