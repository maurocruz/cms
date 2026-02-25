<?php

use Plinct\Cms\Http\Controllers\Auth\RegisterFormController;
use Plinct\Cms\Http\Controllers\Auth\UserController;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $route)
{
	$route->group('/user', function (RouteCollectorProxy $route) {
		$route->get('',[UserController::class, 'list'])->setName('user.list.read');
		$route->get('/edit/{id}',[UserController::class, 'show'])->setName('user.show.read');
		$route->get('/new', RegisterFormController::class)->setName('user.new.read');
	});
};