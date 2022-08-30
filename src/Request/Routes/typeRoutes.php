<?php

declare(strict_types=1);

use Plinct\Cms\Request\Server\Server;
use Plinct\Cms\Request\Server\Sitemap;
use Plinct\Cms\Request\Server\Type\ClosureServer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy as Route;

use Plinct\Cms\CmsFactory;

return function (Route $route)
{
	/**
	 * GET
	 */
	$route->get('[/{type}[/{methodName}[/{id}]]]', function (Request $request, Response $response, $args)
	{
		CmsFactory::response()->isUserLogged(function() use ($request, $response, $args) {
			CmsFactory::webSite()->getContent($args, $request->getQueryParams());
		});

		return CmsFactory::response()->writeBody($response);

	})->addMiddleware(CmsFactory::middleware()->authentication());

	/**
	 * POST
	 */
	$route->post('/{type}/{action}[/{paramsUrl:.*}]', function (Request $request, Response $response, $args) {
		// CHECK AUTHENTICATION
		if (!CmsFactory::request()->user()->userLogged()->getIduser()) {
			CmsFactory::response()->webSite()->addMain(
				CmsFactory::response()->fragment()->auth()->login()
			);
			return CmsFactory::response()->writeBody($response);
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
			$returns = CmsFactory::request()->server()->edit($type, $params);
			// sitemap
			Sitemap::create($type, $params);
		}

		// NEW / POST
		elseif ($action == "new" || $action == "post" || $action == "add") {
			$returns = CmsFactory::request()->server()->new($type, $params);
			// sitemap
			Sitemap::create($type, $params);
		}

		// DELETE
		elseif ($action == "delete" || $action == "erase") {
			// delete data
			$returns = CmsFactory::request()->server()->erase($type, $params);
			// sitemap
			Sitemap::create($type, $params);
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
			CmsFactory::webSite()->addMain(
				CmsFactory::response()->message()->warning($returns['message']));
			return CmsFactory::response()->writeBody($response);
		}

	})->addMiddleware(CmsFactory::middleware()->authentication());
};
