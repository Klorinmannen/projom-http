<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http\Api\Oas;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use Projom\Http\Api\Oas\PayloadContract;

class PayloadContractTest extends TestCase
{
	public static function provider_test_parse(): array
	{
		return [
			'Good test' => [
				'payloadContract' => [
					'content' => [
						'application/json' => []
					],
					'required' => 'true'
				],
				'expected' => [
					'type' => 'application/json',
					'required' => true
				]
			],
			'Required false' => [
				'payloadContract' => [
					'content' => [
						'application/json' => []
					],
					'required' => 'false'
				],
				'expected' => [
					'type' => 'application/json',
					'required' => false
				]
			],
			'Required missing' => [
				'payloadContract' => [
					'content' => [
						'application/json' => []
					]
				],
				'expected' => [
					'type' => 'application/json',
					'required' => false
				]
			],
			'Required bad name' => [
				'payloadContract' => [
					'content' => [
						'application/json' => []
					],
					'required' => 'bad'
				],
				'expected' => [
					'type' => 'application/json',
					'required' => false
				]
			],
			'Content missing' => [
				'payloadContract' => [
					'required' => 'true'
				],
				'expected' => [
					'type' => '',
					'required' => true
				]
			],
			'Empty test' => [
				'payloadContract' => [],
				'expected' => []
			],
		];
	}

	#[DataProvider('provider_test_parse')]
	public function test_parse(
		array $payloadContract,
		array $expected
	): void {
		$payloadContractClass = new PayloadContract($payloadContract);
		$actual = $payloadContractClass->parse($payloadContract);
		$this->assertEquals($expected, $actual);
	}

	public static function provider_test_verify(): array
	{
		return [
			'Good test' => [
				'payloadContract' => [
					'content' => [
						'application/json' => []
					],
					'required' => 'true'
				],
				'inputPayload' => '{"user_id": "1"}',
				'expected' => true
			],
			'Not required' => [
				'payloadContract' => [
					'content' => [
						'application/json' => []
					],
					'required' => 'false'
				],
				'inputPayload' => '',
				'expected' => true
			],
			'Not required with payload' => [
				'payloadContract' => [
					'content' => [
						'application/json' => []
					],
					'required' => 'false'
				],
				'inputPayload' => '{}',
				'expected' => true
			],
			'Required no payload' => [
				'payloadContract' => [
					'content' => [
						'application/json' => []
					],
					'required' => 'true'
				],
				'inputPayload' => '',
				'expected' => false
			],
			'Missing type' => [
				'payloadContract' => [
					'required' => 'true'
				],
				'inputPayload' => '{}',
				'expected' => false			
			],
			'Bad type' => [
				'payloadContract' => [
					'content' => [
						'text/html' => []
					],
					'required' => 'true'
				],
				'inputPayload' => '{}',
				'expected' => false
			],
			'Empty' => [
				'payloadContract' => [],
				'inputPayload' => '',
				'expected' => true	
			]
		];
	}

	#[DataProvider('provider_test_verify')]
	public function test_verify(
		array $payloadContract,
		string $inputPayload,
		bool $expected
	): void {
		$payloadContractClass = new PayloadContract($payloadContract);
		$actual = $payloadContractClass->verify($inputPayload);
		$this->assertEquals($expected, $actual);
	}
}
