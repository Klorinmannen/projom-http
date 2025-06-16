<?php

declare(strict_types=1);

namespace Projom\Http\Router;

use Closure;

use Projom\Http\MiddlewareInterface;
use Projom\Http\Router\MiddlewareContext;

class Middleware
{
	public function __construct(
		private MiddlewareInterface|Closure $middleware,
		private MiddlewareContext $context
	) {}

	public static function create(
		MiddlewareInterface|Closure $middleware,
		MiddlewareContext $context = MiddlewareContext::BEFORE_ROUTING
	): Middleware {
		return new Middleware($middleware, $context);
	}

	public function isContext(MiddlewareContext $context): bool
	{
		return $this->context === $context;
	}

	public function process(...$args): void
	{
		$this->middleware instanceof Closure
			? ($this->middleware)(...$args)
			: $this->middleware->process(...$args);
	}
}
