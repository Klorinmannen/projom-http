<?php

declare(strict_types=1);

namespace Projom\Http\Router;

enum MiddlewareContext
{
	case BEFORE_MATCHING_ROUTE;
	case BEFORE_DISPATCHING_ROUTE;
	case BEFORE_SENDING_RESPONSE;
}
