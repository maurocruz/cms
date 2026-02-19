<?php

use Plinct\Cms\Http\Controllers\HomeController;
use Plinct\Cms\Http\Middleware\AuthenticationMiddleware;
use Plinct\Cms\Http\Middleware\MessageOrientedMiddleware;
use Plinct\Cms\Http\Middleware\RemoteProcedureCallMiddleware;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $route)
{
	//
	$route->group('/admin', function (RouteCollectorProxy $route)
	{
		//  HOME
		$route->get('/[{module}[/{methodName}[/{id}]]]', [HomeController::class, 'home']);

	})->addMiddleware(new MessageOrientedMiddleware())
		->add(AuthenticationMiddleware::class)
		->add(RemoteProcedureCallMiddleware::class);
};
