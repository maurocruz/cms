<?php

use Plinct\Cms\Http\Controllers\HomeController;
use Plinct\Cms\Http\Middleware\HttpExceptionHandlerMiddleware;
use Plinct\Cms\Http\Middleware\MessageOrientedMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $route)
{
	/**
	 * ROUTES
	 * for setName use [namespace].[useCase].[action]
	 */
	$route->group('/admin', function (RouteCollectorProxy $route) {
		// AUTHENTICATION
		(require __DIR__.'/authRoute.php')($route);
		//  HOME
		$route->get('/', [HomeController::class, 'home'])->setName('home');
		// USER
		(require __DIR__.'/userRoute.php')($route);
		// CONFIG
		(require __DIR__.'/configRoute.php')($route);

	})->add(HttpExceptionHandlerMiddleware::class)
		->addMiddleware(new MessageOrientedMiddleware());
};
