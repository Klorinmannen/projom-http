<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http\Api;

use PHPUnit\Framework\TestCase;

use Projom\Http\Api\RouteContractInterface;

class RouteContractInterfaceTest extends TestCase
{
	public function test_methods_exists(): void
	{
		$methods = [
			'match',
			'verifyInputData',
			'verifyController',
			'verifyResponse',
			'hasAuth',
			'controller',
			'operation',
		];
		foreach ($methods as $method) 
			$this->assertTrue(method_exists(RouteContractInterface::class, $method));

		$refletion = new \ReflectionClass(RouteContractInterface::class);
		$this->assertEquals(count($methods), count($refletion->getMethods()));
	}
}
