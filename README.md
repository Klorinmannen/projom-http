# Projom http module
[![PHP version support][php-version-badge]][php]
[![PHPUnit][phpunit-ci-badge]][phpunit-action]

[php-version-badge]: https://img.shields.io/badge/php-%5E8.2-7A86B8
[php]: https://www.php.net/supported-versions.php
[phpunit-action]: https://github.com/Klorinmannen/projom-http/actions
[phpunit-ci-badge]: https://github.com/Klorinmannen/projom-http/workflows/PHPUnit/badge.svg

### Project goals
* Routing requests
* Dispatching requests
* Support a selective scope of OAS 3.0.

### Docs
Visit the repository [wiki](https://github.com/Klorinmannen/projom-http/wiki) pages.

### Example usage
````
use Projom\Http\Router;
use Projom\Http\Router\RouteInterface;
use Projom\Http\Router\ParameterType;

use Recipe\Controller as RecipeController;
use Recipe\Ingredient\Controller as RecipeIngredientController;

$router = new Router();

$router->addRoute('/', 
	RootController::class, 
	function (RouteInterface $route) {
		$route->get();
	}
);

$router->addRoute(
	'/recipes', 
	RecipeController::class, 
	function (RouteInterface $route) {
		
		$route->get()
			->optionalQueryParameters(['sort' => ParameterType::STR])
			->requiredQueryParameters(['page' => ParameterType::INT, 
									   'limit' => ParameterType::INT]);
		
		$route->post()->requiredPayload();
	}
);

$router->addRoute(
	'/recipes/{numeric_id:recipe_id}',
	RecipeController::class, 
	function (RouteInterface $route) {

		$route->get('getRecipe');
		$route->patch('patchRecipe')->requiredPayload();
	}
);

$router->addRoute(
	'/recipes/{numeric_id:recipe_id}/ingredients',
	RecipeIngredientController::class, 
	function (RouteInterface $route) {
		
		$route->get()
			->optionalQueryParameters(['sort' => ParameterType::STR])
			->requiredQueryParameters(['page' => ParameterType::INT, 
									   'limit' => ParameterType::INT]);
		
		$route->post()->requiredPayload();
	}
);

$router->addRoute(
	'/recipes/{numeric_id:recipe_id}/ingredients/{numeric_id:ingredient_id}',
	RecipeIngredientController::class, 
	function (RouteInterface $route) {
		$route->get('getIngredient');
		$route->patch('patchIngredient')->requiredPayload();
	}
);

$router->dispatch();
````
