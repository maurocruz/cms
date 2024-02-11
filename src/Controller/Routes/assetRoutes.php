<?php

declare(strict_types=1);

use Plinct\Cms\Controller\App;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy as Route;

return function (Route $route)
{
	$route->get('/assets/{type}/{filename}', function(Request $request, Response $response, array $args)
	{
		$filename = $args['filename'];
		$type = $args['type'];

		$file = realpath(App::getBASEDIR() . "/static/$type/$filename" .".".$type);
		$script = file_get_contents($file);
		$contentType = $type == 'js' ? "application/javascript" : ($type == 'css' ? "text/css" : "text/html" );

		$newResponse = $response->withHeader("Content-type", $contentType);
		$newResponse->getBody()->write($script);

		return $newResponse;
	});
};
