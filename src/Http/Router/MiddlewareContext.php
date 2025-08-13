<?php

declare(strict_types=1);

namespace Projom\Http\Router;

enum MiddlewareContext
{
	case BEFORE_ROUTING;
	case BEFORE_DISPATCHING;
}
