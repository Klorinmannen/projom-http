<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http\Api\Oas;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use Projom\Http\Api\Oas\Contract;
use Projom\Http\Api\PathContractInterface;
use Projom\Http\Request;

class ContractTest extends TestCase
{
	#[Test]
	public function match(): void
	{
		$contract = Contract::create(__DIR__ . '/test_contract.yml');

		$request = $this->createStub(Request::class);
		$request->method('matchPattern')->willReturn(true);
		$request->method('httpMethod')->willReturn('GET');

		$actual = $contract->match($request);
		$this->assertNotNull($actual);
		$this->assertInstanceOf(PathContractInterface::class, $actual);
	}
	
	#[Test]
	public function match_null(): void
	{
		$request = $this->createStub(Request::class);
		$request->method('matchPattern')->willReturn(true);
		$request->method('httpMethod')->willReturn('GET');

		$contract = new Contract('');
		$actual = $contract->match($request);
		$this->assertNull($actual);
	}
}
