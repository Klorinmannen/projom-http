# Projom http module
[![PHP version support][php-version-badge]][php]
[![PHPUnit][phpunit-ci-badge]][phpunit-action]

[php-version-badge]: https://img.shields.io/badge/php-%5E8.1-7A86B8
[php]: https://www.php.net/supported-versions.php
[phpunit-action]: https://github.com/Klorinmannen/projom-http/actions
[phpunit-ci-badge]: https://github.com/Klorinmannen/projom-http/workflows/PHPUnit/badge.svg

### Project goals
* Routing requests
* Support a selective scope of OAS 3.0.

### Docs
Visit the repository [wiki](https://github.com/Klorinmannen/projom-http/wiki) pages.

### Example usage
````
use Projom\Http\Request;
use Projom\Http\RouteInterface;
use Projom\Http\Route\Handler;

use Recipe\Auth\Controller as AuthController;
use Recipe\Auth\Preflight;
use Recipe\Controller as RecipeController;

$router = new Router();
$router->addRoute('/', Handler::create(RecipeController::class), function (RouteInterface $route) {
	$route->get();
	return $route;
});
$router->addRoute('/auth/login', Handler::create(AuthController::class, 'login'), function (RouteInterface $route) {
	$route->post();
	return $route;
});
$router->addMiddleware(Preflight::create());
$router->dispatch(Request::create());
````
