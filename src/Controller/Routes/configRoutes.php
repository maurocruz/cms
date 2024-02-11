<?php
declare(strict_types=1);

use Plinct\Cms\CmsFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy as Route;

return function (Route $route) {

	$route->group('/config', function (Route $route) {

		$route->get('/initApplication', function (Request $request, Response $response) {
			$data = CmsFactory::controller()->configuration()->initApplication();
			if ($data['status'] === 'success') {
				return $response->withHeader("Location", "/admin")->withStatus(302);
			} else {
				return CmsFactory::view()->writeBody($response);
			}
		});
	});
};
