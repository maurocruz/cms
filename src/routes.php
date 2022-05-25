<?php
/*
 * ROUTES CMS ADMIN
 */

declare(strict_types=1);

use Plinct\Cms\Authentication\AuthenticationMiddleware;
use Plinct\Cms\Middleware\GatewayMiddleware;
use Plinct\Cms\Server\Type\ClosureServer;
use Plinct\Cms\WebSite\Fragment\Fragment;
use Plinct\Cms\WebSite\Section\User\UserController;
use Plinct\Cms\WebSite\Section\User\UserView;
use Plinct\Cms\WebSite\WebSite;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy as Route;
use Plinct\Cms\Server\Server;
use Plinct\Cms\Server\Sitemap;

return function (Route $route)
{
  WebSite::create();

  /**
   * ASSETS
   */
  $route->get('/admin/assets/{type}/{filename}', function(Request $request, Response $response, array $args)
  {
    $filename = $args['filename'];
    $type = $args['type'];

    $file = realpath(__DIR__ . "/../static/$type/$filename" .".".$type);
    $script = file_get_contents($file);
    $contentType = $type == 'js' ? "application/javascript" : ($type == 'css' ? "text/css" : "text/html" );

    $newResponse = $response->withHeader("Content-type", $contentType);
    $newResponse->getBody()->write($script);

    return $newResponse;
  });

  /**
   * ADMIN ROUTES
   */
  $route->group('/admin', function(Route $route)
  {
    /**
     * AUTHENTICATION ROUTES
     */
    $authRoutes = require __DIR__ . '/Authentication/AuthRoutes.php';
    $authRoutes($route);

	  /**
	   * USER
	   */
		$route->get('/user[/{action}[/{iduser}]]', function (Request $request, Response $response, $args)
		{
			$params = $request->getQueryParams();

			$data = (new UserController())->index($params);
			(new UserView())->index($data);

			$response->getBody()->write(WebSite::ready());
			return $response;

		})->addMiddleware(new AuthenticationMiddleware());

    /**
     * ENCLAVE
     */
    $route->group('/{controller:closure|enclave}', function (Route $route)
    {
      $route->get('/{className}', function (Request $request, Response $response, $args)
      {
				// CHECK AUTHENTICATION
				if (!isset($_SESSION['userLogin']['admin'])) {
					WebSite::addMain(Fragment::auth()->login());
					$response->getBody()->write(WebSite::ready());
					return $response;
				}

        $queryParams = $request->getQueryParams();
        $ns = $queryParams['ns'] ?? "";
        $className = $args['className'];
        $classNameSpace = "\\" . base64_decode($ns) . "\\" . ucfirst($className);

        WebSite::enclave()->get($classNameSpace, $queryParams);

        $response->getBody()->write(WebSite::ready());
        return $response;

      })->addMiddleware(new AuthenticationMiddleware());

      $route->post('/{className}', function(Request $request, Response $response, $args)
      {
	      // CHECK AUTHENTICATION
	      if (!isset($_SESSION['userLogin']['admin'])) {
		      WebSite::addMain(Fragment::auth()->login());
		      $response->getBody()->write(WebSite::ready());
		      return $response;
	      }

        $parseBody = $request->getParsedBody();

        $queryParams = $request->getQueryParams();
        $ns = $queryParams['ns'] ?? "";
        $action = $queryParams['action'] ?? null;
        $className = $args['className'];
        $classNameSpace = "\\" . base64_decode($ns) . "\\" . ucfirst($className);

        switch ($action) {
          case 'edit':
            $returns = Server::enclave()->post($classNameSpace, $parseBody);
            break;
          case 'new':
          case 'add':
            $returns = Server::enclave()->put($classNameSpace, $parseBody);
            break;
          case 'delete':
            $returns = Server::enclave()->delete($classNameSpace, $parseBody);
            break;
          default:
            $returns = Fragment::noContent(_("Action not recognized"));
        }
        if (is_array($returns)) {
          WebSite::addMain($returns);
          $response->getBody()->write(WebSite::ready());
          return $response;
        } elseif (is_string($returns)) {
          return $response->withHeader('Location', $returns)->withStatus(301);
        } else {
          return $response->withHeader('Location', $_SERVER['HTTP_REFERER'])->withStatus(301);
        }
      })->addMiddleware(new AuthenticationMiddleware());
    });

    /**
     * DEFAULT
     */
    $route->get('[/{type}[/{methodName}[/{id}]]]', function (Request $request, Response $response, $args)
    {
			$type = $args['type'] ?? null;
      if (isset($_SESSION['userLogin']['admin'])) {
				if ($type == 'login') {
					return $response->withHeader('Location', '/admin')->withStatus(301);
				}
        WebSite::getContent($args, $request->getQueryParams());
      } else {
        if ($request->getAttribute('status') !== "fail") WebSite::addMain(Fragment::auth()->login());
      }

      $response->getBody()->write(WebSite::ready());
      return $response;

    })->addMiddleware(new AuthenticationMiddleware());

    /**
     * ADMIN POST
     */
    $route->post('/{type}/{action}[/{paramsUrl:.*}]', function (Request $request, Response $response, $args)
    {
	    // CHECK AUTHENTICATION
	    if (!isset($_SESSION['userLogin']['admin'])) {
		    WebSite::addMain(Fragment::auth()->login());
		    $response->getBody()->write(WebSite::ready());
		    return $response;
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
        $returns = (new Server())->erase($type, $params);
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

    })->addMiddleware(new AuthenticationMiddleware());

  })->addMiddleware(new GatewayMiddleware());
};
