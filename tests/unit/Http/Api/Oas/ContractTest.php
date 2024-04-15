<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http\Api\Oas;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use Projom\Http\Request;
use Projom\Http\Api\Oas\Contract;
use Projom\Http\Api\Oas\File;
use Projom\Http\Api\PathContractInterface;

class ContractTest extends TestCase
{
	public function test_match(): void
	{
		$contract = new Contract(__DIR__ . '/test_contract.yml');

		$request = $this->createStub(Request::class);
		$request->method('matchPattern')->willReturn(true);
		$request->method('httpMethod')->willReturn('GET');

		$actual = $contract->match($request);
		$this->assertNotNull($actual);
		$this->assertInstanceOf(PathContractInterface::class, $actual);
	}

	public function test_match_null(): void
	{
		$request = $this->createStub(Request::class);
		$request->method('matchPattern')->willReturn(true);
		$request->method('httpMethod')->willReturn('GET');

		$contract = new Contract('');
		$actual = $contract->match($request);
		$this->assertNull($actual);
	}
}
