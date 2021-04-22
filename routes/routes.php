<?php
/*
 * ROUTES CMS ADMIN
 */

use Plinct\Cms\Middleware\Authentication;
use Plinct\Cms\Middleware\GatewayMiddleware;
use Plinct\Cms\Server\Api;
use Plinct\Cms\View\Template\TemplateController;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy as Route;
use Plinct\Cms\Server\Server;
use Plinct\Cms\Server\Sitemap;

return function (Route $route) {
    /**
     * ASSETS
     */
    $route->get('/admin/assets/{type}/{filename}', function(Request $request, Response $response, array $args) {
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
    $route->group('/admin', function(Route $route) {
        /**
         * AUTHENTICATION ROUTES
         */
        $authRoutes = require __DIR__ . '/AuthRoutes.php';
        $authRoutes($route);
        /**
         * GET
         */
        $route->get('[/{type}[/{action}[/{identifier}[/{has}[/{hasAction}[/{hasId]]]]]]', function (Request $request, Response $response) {
            $template = new TemplateController();
            if (isset($_SESSION['userLogin']['admin'])) {
                $template->getContent($request);
            } else {
                $template->login();
            }
            $response->getBody()->write($template->ready());
            return $response;
        })->addMiddleware(new Authentication());
        /**
         * ADMIN POST
         */
        $route->post('/{type}/{action}', function (Request $request, Response $response, $args) {
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
                $redirectLocation = (new Server())->edit($type, $params);
            }
            // NEW
            elseif ($action == "new" || $action == "post" || $action == "add") {
                // put data
                $redirectLocation = (new Server())->new($type, $params);
            }
            // DELETE
            elseif ($action == "delete" || $action == "erase") {
                // delete data
                $redirectLocation = (new Server())->erase($type, $params);
            }
            // CREATE SQL TABLE
            elseif ($action == "createSqlTable") {
                (new Server())->createSqlTable($type);
                $redirectLocation = $_SERVER['HTTP_REFERER'];
            }
            // SITEMAP
            elseif (($action == "sitemap")) {
                // sitemap
                Sitemap::create($type, $params);
                // redir
                $redirectLocation = $_SERVER['HTTP_REFERER'];
            }
            // GENERIC
            else {
                Api::request($type, $action, $params);
                $redirectLocation = $_SERVER['HTTP_REFERER'];
            }
            return $response->withHeader('Location', $redirectLocation)->withStatus(301);
        })->addMiddleware(new Authentication());
    })->addMiddleware(new GatewayMiddleware());
};
