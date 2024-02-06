<?php

declare(strict_types=1);

namespace Projom\Tests\Unit\Http\Api;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

use Projom\Http\Input;
use Projom\Http\Request;
use Projom\Http\Response;
use Projom\Http\Api\ControllerBase;
use Projom\Http\Api\ContractInterface;
use Projom\Http\Api\Router;
use Projom\Http\Api\RouteContractInterface;

class RouterStub_1 extends Router
{
	public function __construct()
	{
	}

	public static function dispatch(
		Request $request,
		RouteContractInterface $routeContract
	): Response {
		return new Response(['message' => 'Hej']);
	}
}

class RouterStub_2 extends Router
{
	public function __construct()
	{
	}

	public static function processRouteContract(
		Request $request,
		RouteContractInterface $routeContract
	): void {
		return;
	}
}

class RouterTest extends TestCase
{
	public function test_start(): void
	{
		$this->expectNotToPerformAssertions();

		$input = new Input([], []);
		$request = new Request($input);

		$contract = $this->createMock(ContractInterface::class);
		$routeContract = $this->createMock(RouteContractInterface::class);
		$contract->method('match')->willReturn($routeContract);

		$routerStub_3 = new RouterStub_2();
		$routerStub_3->start($request, $contract);
	}

	public function test_start_404(): void
	{
		$input = new Input([], []);
		$request = new Request($input);

		$contract = $this->createMock(ContractInterface::class);
		$contract->method('match')->willReturn(null);

		$this->expectExceptionCode(404);
		$routerStub_3 = new RouterStub_2();
		$routerStub_3->start($request, $contract);
	}

	public function test_routeContract(): void
	{
		$input = new Input([], []);
		$request = new Request($input);

		$routeContract = $this->createMock(RouteContractInterface::class);
		$routeContract->method('hasAuth')->willReturn(true);
		$routeContract->method('controller')->willReturn(ControllerBase::class);
		$routeContract->method('operation')->willReturn('operation');
		$routeContract->method('verifyInputData')->willReturn(true);
		$routeContract->method('verifyController')->willReturn(true);
		$routeContract->method('verifyResponse')->willReturn(true);

		$this->expectOutputString('{"message":"Hej"}');
		$routerStub_1 = new RouterStub_1();
		$routerStub_1->processRouteContract($request, $routeContract);
	}

	public function test_routeContract_400(): void
	{
		$input = new Input([], []);
		$request = new Request($input);

		$routeContract = $this->createMock(RouteContractInterface::class);
		$routeContract->method('verifyInputData')->willReturn(false);

		$contract = $this->createMock(ContractInterface::class);
		$contract->method('match')->willReturn($routeContract);

		$this->expectExceptionCode(400);
		$routerStub_1 = new RouterStub_1();
		$routerStub_1->processRouteContract($request, $routeContract);
	}

	public function test_routeContract_501(): void
	{
		$input = new Input([], []);
		$request = new Request($input);

		$routeContract = $this->createMock(RouteContractInterface::class);
		$routeContract->method('verifyInputData')->willReturn(true);
		$routeContract->method('verifyController')->willReturn(false);

		$contract = $this->createMock(ContractInterface::class);
		$contract->method('match')->willReturn($routeContract);

		$this->expectExceptionCode(501);
		$routerStub_1 = new RouterStub_1();
		$routerStub_1->processRouteContract($request, $routeContract);
	}

	public function test_routeContract_500(): void
	{
		$input = new Input([], []);
		$request = new Request($input);

		$routeContract = $this->createMock(RouteContractInterface::class);
		$routeContract->method('verifyInputData')->willReturn(true);
		$routeContract->method('verifyController')->willReturn(true);
		$routeContract->method('verifyResponse')->willReturn(false);

		$contract = $this->createMock(ContractInterface::class);
		$contract->method('match')->willReturn($routeContract);

		$this->expectExceptionCode(500);
		$routerStub_1 = new RouterStub_1();
		$routerStub_1->processRouteContract($request, $routeContract);
	}
}
