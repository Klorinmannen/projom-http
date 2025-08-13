<?php

declare(strict_types=1);

namespace Projom\Http\Router;

use Closure;

use Projom\Http\Request;
use Projom\Http\Router\MiddlewareContext;
use Projom\Http\Router\MiddlewareInterface;

class Middleware
{
	public function __construct(
		private MiddlewareInterface|Closure $middleware,
		private MiddlewareContext|null $context
	) {}

	public static function create(
		MiddlewareInterface|Closure $middleware,
		MiddlewareContext|null $context = null
	): Middleware {
		return new Middleware($middleware, $context);
	}

	public function isContext(MiddlewareContext $context): bool
	{
		return $this->context === $context;
	}

	public function process(Request $request): void
	{
		$this->middleware instanceof Closure
			? ($this->middleware)($request)
			: $this->middleware->process($request);
	}
}
