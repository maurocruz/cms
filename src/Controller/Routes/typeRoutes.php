<?php
declare(strict_types=1);

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\Request\Server\Server;
use Plinct\Cms\Controller\Request\Server\Sitemap;
use Plinct\Cms\Controller\Request\Server\Type\ClosureServer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy as Route;

return function (Route $route)
{
	$route->get('/favicon.ico', function () {
		return null;
	});
	/**
	 * GET
	 */
	$route->get('/[{type}[/{methodName}[/{id}]]]', function (Request $request, Response $response) {
		if (CmsFactory::controller()->user()->userLogged()->getIduser()) {
			CmsFactory::controller()->typeController($request)->ready();
		}
		return CmsFactory::view()->writeBody($response);
	});

	/**
	 * POST
	 */
	$route->post('/{type}/{action}[/{paramsUrl:.*}]', function (Request $request, Response $response, $args) {
		// CHECK AUTHENTICATION
		if (!CmsFactory::controller()->user()->userLogged()->getIduser()) {
			return CmsFactory::view()->writeBody($response);
		}
		$type = $args['type'];
		$action = $args['action'];
		$params = $request->getParsedBody();
		unset($params['submit']);
		unset($params['submit_x']);
		unset($params['submit_y']);
		unset($params['x']);
		unset($params['y']);
		//  EDIT / PUT
		if ($action == "edit" || $action == "put") {
			$returns = CmsFactory::model()->type($type)->put($params);
		}
		// NEW / POST
		elseif ($action == "new" || $action == "post" || $action == "add") {
			$returns = CmsFactory::model()->type($type)->post($params);
		}
		// DELETE
		elseif ($action == "delete" || $action == "erase") {
			$returns = CmsFactory::model()->type($type)->erase($params);
		}

		// CREATE SQL TABLE
		elseif ($action == "createSqlTable") {
			(new Server())->createSqlTable($type);
			$returns = $_SERVER['HTTP_REFERER'];
		}

		// SITEMAP
		elseif (($action == "sitemap")) {
			$returns = $_SERVER['HTTP_REFERER'];
			// sitemap
			Sitemap::create($type, $params);
		}

		// CLOSURE
		elseif($type == "closure") {
			$server = new ClosureServer($params);
			$returns = $server->getReturn();
		}

		// GENERIC
		else {
			(new Server())->request($type, $action, $params);
			$returns = $_SERVER['HTTP_REFERER'];
		}

		if (is_string($returns)) {
			return $response->withHeader('Location', $returns)->withStatus(301);
		} else {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->message()->warning($returns));
			return CmsFactory::view()->writeBody($response);
		}

	});
};
