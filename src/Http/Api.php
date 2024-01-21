<?php

declare(strict_types=1);

namespace Projom\Http;

use Projom\Http\Request;
use Projom\Http\Api\Router;
use Projom\Http\Api\Oas\Contract;

class Api
{
	private Router $router;
	private Request $request;
	private Contract $contract;

	public function load(string $apiContractFilePath, string $JWTclaimsFilePath): void
	{
		$this->contract = Contract::create($apiContractFilePath);
		$this->router = Router::create($JWTclaimsFilePath);
		$this->request = Request::create();
	}

	public function start(): void
	{
		$this->router->start($this->request, $this->contract);
	}
}
