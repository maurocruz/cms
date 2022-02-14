<?php
/*
 * ROUTES CMS ADMIN
 */

declare(strict_types=1);

use Plinct\Cms\Authentication\AuthenticationMiddleware;
use Plinct\Cms\Middleware\GatewayMiddleware;
use Plinct\Cms\Server\Type\ClosureServer;
use Plinct\Cms\WebSite\Fragment\Fragment;
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
         * DEFAULT
         */
        $route->get('[/{type}[/{methodName}[/{id}]]]', function (Request $request, Response $response, $args)
        {
            if (isset($_SESSION['userLogin']['admin'])) {
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