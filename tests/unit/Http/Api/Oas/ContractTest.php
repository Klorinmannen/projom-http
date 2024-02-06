<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http\Api\Oas;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use Projom\Http\Request;
use Projom\Http\Api\RouteContractInterface;
use Projom\Http\Api\Oas\Contract;
use Projom\Http\Api\Oas\PathContract;
use Projom\Http\Api\Oas\File;

class ContractTest extends TestCase
{
	public static function provider_test_sortRouteContracts(): array
	{
		return [
			'Sort good paths' => [
				[
					'/resource/path' => [],
					'/resource/path/2' => [],
					'/resource/path/1' => []
				],
				[
					'/resource/path/2' => [],
					'/resource/path/1' => [],
					'/resource/path' => []
				]
			],
			'Empty paths' => [
				[],
				[]
			],
			'Sorted paths' => [
				[
					'/resource/path/1' => [],
					'/resource/path/2' => [],
					'/resource/path/3' => []
				],
				[
					'/resource/path/1' => [],
					'/resource/path/2' => [],
					'/resource/path/3' => []
				]
			],
		];
	}

	#[DataProvider('provider_test_sortRouteContracts')]
	public function test_sortRouteContracts(
		array $contractPaths,
		array $expected
	): void {

		$file = $this->createStub(File::class);
		$contract = new Contract($file);

		$actual = $contract->sortRouteContracts($contractPaths);
		$this->assertEquals($expected, $actual);
	}

	public function test_match(): void
	{
		$file = $this->createStub(File::class);
		$file->method('contract')->willReturn([
			'/resource/path' => [
				'route_path_contracts' => [
					'get' => [
						new PathContract()
					]
				],
				'route_pattern' => '/resource/path',
				'route_controller' => '\\Resource\\Path\\Controller'
			]
		]);

		$contract = new Contract($file);
		$contract->load();

		$request = $this->createStub(Request::class);
		$request->method('matchPattern')->willReturn(true);
		$request->method('httpMethod')->willReturn('GET');

		$actual = $contract->match($request);
		$this->assertNotNull($actual);
		$this->assertInstanceOf(RouteContractInterface::class, $actual);
	}

	public function test_match_null(): void
	{
		$file = $this->createStub(File::class);
		$file->method('contract')->willReturn([]);

		$contract = new Contract($file);
		$contract->load();

		$request = $this->createStub(Request::class);
		$request->method('matchPattern')->willReturn(true);
		$request->method('httpMethod')->willReturn('GET');

		$actual = $contract->match($request);
		$this->assertNull($actual);
	}
}
