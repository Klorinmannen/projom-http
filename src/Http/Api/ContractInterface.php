<?php

declare(strict_types=1);

namespace Projom\Http\Api;

use Projom\Http\Request;

interface ContractInterface
{
	public function match(Request $request): PathContractInterface|null;
}
