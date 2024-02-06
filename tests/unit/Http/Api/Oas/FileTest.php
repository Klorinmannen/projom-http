<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http\Api\Oas;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use Projom\Http\Api\Oas\File;

class FileStub extends File
{
	public function parseFile(string $fullFilePath): array
	{
		// Mocks part of a sub contract file.
		return [
			'path_name' => [
				'get' => [
					'operationId' => 'get_operation_id',
				],
				'patch' => [
					'operationId' => 'post_operation_id',
				],
				'delete' => [
					'operationId' => 'delete_operation_id',
				]
			],
			'another_path_name' => [
				'get' => [
					'operationId' => 'get_operation_id',
				],
				'post' => [
					'operationId' => 'post_operation_id',
				]
			]
		];
	}
}

class FileTest extends TestCase
{
	public function test_parseContract(): void
	{
		$fileStub = new FileStub('');

		$contactFile = [
			'paths' => [
				'/resource/path' => [
					'$ref' => 'path/to/contract.yml#/path_name',
				],
				'/resource/path/{id}' => [
					'$ref' => 'path/to/contract.yml#/another_path_name'
				]
			]
		];

		$actual = $fileStub->parseContract($contactFile);
		$this->assertIsArray($actual);
		$this->assertNotEmpty($actual);
	}

	public static function provider_test_splitContractRef(): array
	{
		return [
			'Good ref' => [
				[
					'$ref' => 'path/to/contract.yml#/path_name'
				],
				[
					'path/to/contract.yml',
					'path_name'
				]
			],
			'Bad ref' => [
				[
					'$ref' => 'path/to/contract.yml#path_name'
				],
				[
					'path/to/contract.yml#path_name'
				]
			]
		];
	}

	#[DataProvider('provider_test_splitContractRef')]
	public function test_splitConctractRef(
		array $contractRef,
		array $expected
	): void {
		$file = new File('');
		$actual = $file->splitContractRef($contractRef);
		$this->assertEquals($expected, $actual);
	}

	public function test_parseFile(): void
	{
		$file = new File('');
		$actual = $file->parseFile('');
		$this->assertIsArray($actual);
		$this->assertEmpty($actual);
	}

	public static function provider_test_routeController(): array
	{
		return [
			'Good path' => [
				'path/to/contract.yml',
				'\\path\\to\\contract\\Controller'
			],
			'Path without slash' => [
				'contract.yml',
				'\\contract\\Controller'
			],
			'Path without .yml' => [
				'path/to/contract',
				'\\path\\to\\contract\\Controller'
			],
			'Path with .yaml extension' => [
				'path/to/contract.yaml',
				'\\path\\to\\contract\\Controller'
			],
			'Path with extra slash' => [
				'/path/to/contract.yml',
				'\\path\\to\\contract\\Controller'
			],
			'Path with double slash' => [
				'path//to//contract.yml',
				'\\path\\to\\contract\\Controller'
			],
			'Empty path' => [
				'',
				''
			]
		];
	}

	#[DataProvider('provider_test_routeController')]
	public function test_routeController(
		string $filePath,
		string $expected
	): void {
		$file = new File('');
		$actual = $file->routeController($filePath);
		$this->assertEquals($expected, $actual);
	}

	public function test_contract(): void
	{
		$file = new File('');
		$actual = $file->contract();
		$this->assertIsArray($actual);
		$this->assertEmpty($actual);
	}
}
