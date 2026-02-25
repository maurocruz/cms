<?php

use Plinct\Cms\Http\Controllers\Config\ConfigController;
use Slim\Routing\RouteCollectorProxy;

return function (RouteCollectorProxy $route)
{
	$route->group('/config', function (RouteCollectorProxy $route)
	{
		$route->get('', [ConfigController::class, 'dashboard'])->setName('config.dashboard.read');
		$route->post('/installModule', [ConfigController::class, 'installModule'])->setName('config.installModule.send');
	});
};