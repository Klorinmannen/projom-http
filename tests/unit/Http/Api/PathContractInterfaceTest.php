<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http\Api;

use PHPUnit\Framework\TestCase;
use Projom\Http\Api\PathContractInterface;

class PathContractInterfaceTest extends TestCase
{
	public function test_methods_exists(): void
	{
		$methods = [
			'verifyInputPathParameters',
			'verifyInputQueryParameters',
			'verifyInputPayload',
			'verifyController',
			'verifyResponse',
			'controller',
			'operation',
			'hasAuth'
		];
		foreach ($methods as $method) 
			$this->assertTrue(method_exists(PathContractInterface::class, $method));

		$refletion = new \ReflectionClass(PathContractInterface::class);
		$this->assertEquals(count($methods), count($refletion->getMethods()));
	}
}
