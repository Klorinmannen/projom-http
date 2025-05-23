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
use Projom\Http\Route\RouteInterface;

use Recipe\Auth\PreflightMiddleware;
use Recipe\Controller as RecipeController;
use Recipe\Ingredient\Controller as RecipeIngredientController;

$router = new Router();

$router->addRoute('/', 
	RecipeController::class, 
	function (RouteInterface $route) {
		$route->get();
	}
);

$router->addRoute(
	'/recipes', 
	RecipeController::class, 
	function (RouteInterface $route) {
		
		$route->get()
			->optionalQueryParameters(['sort' => 'string'])
			->expectsQueryParameters(['page' => 'integer']);
		
		$route->post()->expectsPayload();
	}
);

$router->addRoute(
	'/recipes/{numeric_id:recipe_id}',
	RecipeController::class, 
	function (RouteInterface $route) {
		$route->get('getRecipe');
		$route->patch('updateRecipe')->expectsPayload();
	}
);

$router->addRoute(
	'/recipes/{numeric_id:recipe_id}/ingredients',
	RecipeIngredientController::class, 
	function (RouteInterface $route) {
		
		$route->get()
			->optionalQueryParameters(['sort' => 'string'])
			->expectsQueryParameters(['page' => 'integer']);
		
		$route->post()->expectsPayload();
	}
);

$router->addRoute(
	'/recipes/{numeric_id:recipe_id}/ingredients/{numeric_id:ingredient_id}',
	RecipeIngredientController::class, 
	function (RouteInterface $route) {
		$route->get('getIngredient');
		$route->patch('updateIngredient')->expectsPayload();
	}
);

$router->addMiddleware(PreflightMiddleware::create());

$router->dispatch(Request::create());
````
