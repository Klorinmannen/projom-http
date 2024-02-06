<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http\Api\Oas;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use Projom\Http\Response;
use Projom\Http\Api\Oas\PathContract;

class PathContractTest extends TestCase
{
	public static function provider_test_parameters(): array
	{
		return [
			'Good test' => [
				'pathContract' => [
					'parameters' => [
						[
							'name' => 'id',
							'in' => 'path',
							'required' => 'true',
							'schema' => [
								'type' => 'integer'
							]
						],
						[
							'name' => 'sort',
							'in' => 'query',
							'required' => 'true',
							'schema' => [
								'type' => 'string'
							]
						]
					]
				],
				'inputPathParameters' => [
					'1'
				],
				'inputQueryParameters' => [
					'sort' => 'asc'
				],
				'expected' => [
					'path_parameters' => true,
					'query_parameters' => true
				]
			]
		];
	}

	#[DataProvider('provider_test_parameters')]
	public function test_parameters(
		array $pathContract,
		array $inputPathParameters,
		array $inputQueryParameters,
		array $expected
	): void {
		$pathContractClass = new PathContract($pathContract);

		$actual = $pathContractClass->verifyPathParameters($inputPathParameters);
		$this->assertEquals($expected['path_parameters'], $actual);

		$actual = $pathContractClass->verifyQueryParameters($inputQueryParameters);
		$this->assertEquals($expected['query_parameters'], $actual);
	}

	public static function provider_test_payload(): array
	{
		return [
			'Good payload input' => [
				'pathContract' => [
					'requestBody' => [
						'content' => [
							'application/json' => []
						]
					]
				],
				'payload' => '{"id": 1}',
				'expected' => true
			],
			'Bad payload input' => [
				'pathContract' => [
					'requestBody' => [
						'content' => [
							'application/json' => []
						],
						'required' => 'true'
					],
				],
				'payload' => '',
				'expected' => false
			]
		];
	}

	#[DataProvider('provider_test_payload')]
	public function test_payload(
		array $pathContract,
		string $payload,
		bool $expected
	): void {
		$pathContractClass = new PathContract($pathContract);
		$actual = $pathContractClass->verifyPayload($payload);
		$this->assertEquals($expected, $actual);
	}

	public static function provider_test_response(): array
	{
		return [
			'Good response' => [
				'pathContract' => [
					'responses' => [
						'200' => [
							'content' => [
								'application/json' => []
							]
						]
					]
				],
				'response' => new Response(
					[],
					200,
					'application/json',
				),
				'expected' => true
			]
		];
	}

	#[DataProvider('provider_test_response')]
	public function test_response(
		array $pathContract,
		Response $response,
		bool $expected
	): void {
		$pathContractClass = new PathContract($pathContract);
		$actual = $pathContractClass->verifyResponse($response);
		$this->assertEquals($expected, $actual);
	}

	public static function provider_test_operation(): array
	{
		return [
			'Good operation' => [
				'pathContract' => [
					'operationId' => 'get_by_id'
				],
				'expected' => 'get_by_id'
			]
		];
	}

	#[DataProvider('provider_test_operation')]
	public function test_operation(
		array $pathContract,
		string $expected
	): void {
		$pathContractClass = new PathContract($pathContract);
		$actual = $pathContractClass->operation();
		$this->assertEquals($expected, $actual);
	}

	public static function provider_test_auth(): array
	{
		return [
			'Good auth' => [
				'pathContract' => [
					'security' => [[]]
				],
				'expected' => true
			],
			'Bad auth' => [
				'pathContract' => [
					'security' => []
				],
				'expected' => false
			]
		];
	}

	#[DataProvider('provider_test_auth')]
	public function test_auth(
		array $pathContract,
		bool $expected
	): void {
		$pathContractClass = new PathContract($pathContract);
		$actual = $pathContractClass->hasAuth();
		$this->assertEquals($expected, $actual);
	}
}
