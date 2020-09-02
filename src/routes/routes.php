<?php

/*
 * ROUTES CMS ADMIN
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy as Route;

use Plinct\Api\Auth\AuthMiddleware;
use Plinct\Cms\Middleware\InitialChecking;

use Plinct\Cms\View\ViewBuilder;
use Plinct\Cms\Server\Server;
use Plinct\Cms\Server\Sitemap;

return function (Route $route) 
{
    // ASSETS
    $route->get('/admin/assets/{type}/{filename}', function(Request $request, Response $response, array $args)
    {
        $filename = $args['filename'];
        $type = $args['type'];

        $script = file_get_contents( __DIR__ ."/../views/html/assets/".$type."/".$filename.".".$type);

        $contentType = $type == 'js' ? "application/javascript" : ($type == 'css' ? "text/css" : "text/html" );

        $newResponse = $response->withHeader("Content-type", $contentType);

        $newResponse->getBody()->write($script);

        return $newResponse;
    });
    /*
     * ADMIN ROUTES
     */
    $route->group('/admin', function(Route $route)
    {                  
        /* AUTHENTICATION ROUTES */
        $authRoutes = require __DIR__ . '/AuthRoutes.php';
        $authRoutes($route);

        
        /*
         * DEFAULTS
         */
        $route->get('[/{type}[/{action}[/{identifier}[/{has}[/{hasAction}[/{hasId]]]]]]', function (Request $request, Response $response)
        {
            $viewBuilder = new ViewBuilder();
                        
            if ($request->getAttribute('userAuth') === false) {
                $content = $viewBuilder->login($request, $response);
                
            } else {
                $content = $viewBuilder->build($request);
            }

            $response->getBody()->write($content);
            return $response;
        });

        /*
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
            
            $className = "\\Plinct\\Api\\Type\\".ucfirst($type);

            if (class_exists($className)) {
                //  EDIT
                if ($action == "edit" || $action == "put") {                  
                    $data = (new Server())->edit($className, $params);
                    // sitemap
                    Sitemap::create($type);
                } 
                
                // NEW
                elseif ($action == "new" || $action == "post" || $action == "add") {
                    // put data
                    $data = (new Server())->new($className, $params);
                    // sitemap
                    Sitemap::create($type);
                } 
                
                // DELETE
                elseif ($action == "delete" || $action == "erase") {
                    // delete data
                    $data = (new Server())->delete($className, $params);
                    // sitemap
                    Sitemap::create($type);
                } 
                                        
                return $response->withHeader('Location', $data)->withStatus(301);
                
            } else {            
                return false;
            }
        }); 
        
    })->add(new AuthMiddleware())->add(new InitialChecking());
};
