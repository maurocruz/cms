<?php
/*
 * ROUTES CMS ADMIN
 */

use Plinct\Cms\Factory\TemplateFactory;
use Plinct\Cms\Middleware\Authentication;
use Plinct\Cms\Middleware\GatewayMiddleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy as Route;
use Plinct\Cms\Server\Server;
use Plinct\Cms\Server\Sitemap;

return function (Route $route)
{
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
        $authRoutes = require __DIR__ . '/AuthRoutes.php';
        $authRoutes($route);

        /**
         * DEFAULT
         */
        $route->get('[/{type}[/{methodName}[/{id}]]]', function (Request $request, Response $response, $args)
        {
            $template = TemplateFactory::create();

            if (isset($_SESSION['userLogin']['admin'])) {
                $content = $template->viewContent($args, $request->getQueryParams());
            } else {
                $content = $template->login();
            }

            $response->getBody()->write($content);

            return $response;

        })->addMiddleware(new Authentication());

        /**
         * ADMIN POST
         */
        $route->post('/{type}/{action}', function (Request $request, Response $response, $args)
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
                $data = (new Server())->edit($type, $params);
                // sitemap
                Sitemap::create($type, $params);
            }
            // NEW
            elseif ($action == "new" || $action == "post" || $action == "add") {
                // put data
                $data = (new Server())->new($type, $params);
                // sitemap
                Sitemap::create($type, $params);
            }
            // DELETE
            elseif ($action == "delete" || $action == "erase") {
                // delete data
                $data = (new Server())->erase($type, $params);
                // sitemap
                Sitemap::create($type, $params);
            }
            // CREATE SQL TABLE
            elseif ($action == "createSqlTable") {
                (new Server())->createSqlTable($type);
                $data = $_SERVER['HTTP_REFERER'];
            }
            // SITEMAP
            elseif (($action == "sitemap")) {
                $data = $_SERVER['HTTP_REFERER'];
                // sitemap
                Sitemap::create($type, $params);
            }
            // GENERIC
            else {
                (new Server())->request($type, $action, $params);
                $data = $_SERVER['HTTP_REFERER'];
            }
            return $response->withHeader('Location', $data)->withStatus(301);
        })->addMiddleware(new Authentication());
    })->addMiddleware(new GatewayMiddleware());
};
