<?php

declare(strict_types=1);

namespace Projom\Http\Middleware;

enum MiddlewareContext
{
	case BEFORE_ROUTING;
	case BEFORE_DISPATCHING;
	case BEFORE_DISPATCHING_RESPONSE;
}
