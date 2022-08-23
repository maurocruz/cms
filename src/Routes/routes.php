<?php
/*
 * ROUTES CMS ADMIN
 */

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy as Route;

use Plinct\Cms\Server\Server;
use Plinct\Cms\Server\Sitemap;
use Plinct\Cms\Server\Type\ClosureServer;

use Plinct\Cms\CmsFactory;

/**
 * ADMIN ROUTES
 */
return function (Route $route)
{
  CmsFactory::webSite()->create();

  $route->group('/admin', function(Route $route)
  {
	  /**
	   * ASSETS
	   */
	  CmsFactory::routes()->assets($route);

    /**
     * AUTHENTICATION ROUTES
     */
		CmsFactory::routes()->authentication($route);

	  /**
	   * USER
	   */
		$route->group('/user', function (Route $route) {
			CmsFactory::routes()->user($route);
		})->addMiddleware(CmsFactory::middleware()->authentication());

    /**
     * ENCLAVE
     */
		CmsFactory::routes()->enclave($route);

    /**
     * TYPES
     */
		CmsFactory::routes()->type($route);

    /**
     * ADMIN POST
     */
    $route->post('/{type}/{action}[/{paramsUrl:.*}]', function (Request $request, Response $response, $args)
    {
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

      //  EDIT
      if ($action == "edit" || $action == "put") {
        $returns = (new Server())->edit($type, $params);
        // sitemap
        Sitemap::create($type, $params);
      }

      // NEW
      elseif ($action == "new" || $action == "post" || $action == "add") {
        // put data
        $returns = (new Server())->new($type, $params);
        // sitemap
        Sitemap::create($type, $params);
      }

      // DELETE
      elseif ($action == "delete" || $action == "erase") {
        // delete data
	      $returns = CmsFactory::server()->erase($type, $params);
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

      return $response->withHeader('Location', $returns)->withStatus(301);

    })->addMiddleware(CmsFactory::middleware()->authentication());

  })->addMiddleware(CmsFactory::middleware()->gateway());
};
