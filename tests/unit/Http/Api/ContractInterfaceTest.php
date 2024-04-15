<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http\Api;

use PHPUnit\Framework\TestCase;

use Projom\Http\Api\ContractInterface;

class ContractInterfaceTest extends TestCase
{
	public function test_methods_exists(): void
	{
		$methods = [
			'match'
		];
		
		foreach ($methods as $method) 
			$this->assertTrue(method_exists(ContractInterface::class, $method));

		$refletion = new \ReflectionClass(ContractInterface::class);
		$refletion->getMethods();
		$this->assertEquals(count($methods), count($refletion->getMethods()));
	}
}