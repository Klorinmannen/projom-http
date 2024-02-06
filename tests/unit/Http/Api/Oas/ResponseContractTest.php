<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http\Api\Oas;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use Projom\Http\Api\Oas\ResponseContract;

class ResponseContractTest extends TestCase
{
	public static function provider_test_parseList(): array
	{
		return [
			'Good test' => [
				'responseContracts' => [
					'200' => [
						'content' => [
							'application/json' => []
						]
					],
					'400' => [
						'content' => [
							'application/json' => []
						]
					]
				],
				'expected' => [
					'200' => 'application/json',
					'400' => 'application/json'
				]
				],
				'Content missing' => [
					'responseContracts' => [
						'200' => [],
						],
					'expected' => [
						'200' => '',
					]
				],
				'Content empty' => [
					'responseContracts' => [
						'200' => [
							'content' => []
						]
					],
					'expected' => [
						'200' => '',
					]
				]
		];
	}

	#[DataProvider('provider_test_parseList')]
	public function test_parseList(
		array $responseContracts,
		array $expected
	): void {
		$responseContract = new ResponseContract($responseContracts);
		$acutal = $responseContract->parseList($responseContracts);
		$this->assertEquals($expected, $acutal);
	}

	public static function provider_test_verify(): array
	{
		return [
			'Good test' => [
				'responseContracts' => [
					'200' => [
						'content' => [
							'application/json' => []
						]
					]
				],
				'statusCode' => 200,
				'contentType' => 'application/json',
				'expected' => true
			],
			'Status code not found in contract' => [
				'responseContracts' => [
					'200' => [
						'content' => [
							'application/json' => []
						]
					]
				],
				'statusCode' => 400,
				'contentType' => 'application/json',
				'expected' => false
			],
			'Content type not found in contract' => [
				'responseContracts' => [
					'200' => [
						'content' => [
							'application/json' => []
						]
					]
				],
				'statusCode' => 200,
				'contentType' => 'application/xml',
				'expected' => false
			]
		];
	}

	#[DataProvider('provider_test_verify')]
	public function test_verify(
		array $responseContracts,
		int $statusCode,
		string $contentType,
		bool $expected
	): void {
		$responseContract = new ResponseContract($responseContracts);
		$actual = $responseContract->verify($statusCode, $contentType);
		$this->assertEquals($expected, $actual);
	}
}
