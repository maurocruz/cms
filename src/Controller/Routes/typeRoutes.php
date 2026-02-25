<?php

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\Request\Server\Server;
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
	$route->get('/[{type}[/{methodName}[/{id}]]]', function (Request $request, Response $response, $args) {
		if (CmsFactory::controller()->user()->userLogged()->getIduser()) {
			$type = $args['type'] ?? null;
			if ($type === 'login') {
				return $response->withHeader("Location", "/admin")->withStatus(302);
			} else {
				if ($type && !CmsFactory::controller()->user()->userLogged()->hasPrivileges(1,'r',$type)) {
					CmsFactory::view()->addMain(
						CmsFactory::view()->fragment()->miscellaneous()->message(_("You don't have privileges to access this page!"))
					);
				} else {
					CmsFactory::controller()->typeController($request)->ready();
				}
			}
		} elseif ($request->getAttribute('EntryPoint') == 'initApplication') {
			CmsFactory::view()->webSite()->authenticationView()->installDatabase();
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
		$queryParams = $request->getQueryParams();
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
			$returns = CmsFactory::model()->type($type)->post($params, $queryParams);
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
