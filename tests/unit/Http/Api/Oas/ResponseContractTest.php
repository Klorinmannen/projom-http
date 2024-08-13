<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http\Api\Oas;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use Projom\Http\Api\Oas\ResponseContract;

class ResponseContractTest extends TestCase
{
	public static function provider_parseList(): array
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

	#[Test]
	#[DataProvider('provider_parseList')]
	public function parseList(array $responseContracts, array $expected): void
	{
		$responseContract = ResponseContract::create($responseContracts);
		$acutal = $responseContract->parseContracts($responseContracts);
		$this->assertEquals($expected, $acutal);
	}

	public static function provider_verify(): array
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

	#[Test]
	#[DataProvider('provider_verify')]
	public function verify(array $responseContracts, int $statusCode, string $contentType, bool $expected): void
	{
		$responseContract = ResponseContract::create($responseContracts);
		$actual = $responseContract->verify($statusCode, $contentType);
		$this->assertEquals($expected, $actual);
	}
}
