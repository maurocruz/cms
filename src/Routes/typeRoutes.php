<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy as Route;

use Plinct\Cms\CmsFactory;

return function (Route $route)
{
	$route->get('[/{type}[/{methodName}[/{id}]]]', function (Request $request, Response $response, $args)
	{
		$type = $args['type'] ?? null;
		//$methodName = $args['methodName'] ?? null;
		//$id = $args['id'] ?? null;

		if (CmsFactory::request()->user()->userLogged()->getIduser()) {
			if ($type == 'login') {
				return $response->withHeader('Location', '/admin')->withStatus(301);
			}
			CmsFactory::webSite()->getContent($args, $request->getQueryParams());
		} else {
			CmsFactory::webSite()->addMain(
				CmsFactory::response()->fragment()->auth()->login()
			);
		}

		return CmsFactory::response()->writeBody($response);

	})->addMiddleware(CmsFactory::middleware()->authentication());
};
