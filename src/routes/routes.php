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

return function (Route $route) 
{    
    /*
     * ADMIN ROUTES
     */
    $route->group('/admin', function(Route $route)
    {                  
        /* AUTHENTICATION ROUTES */
        $authRoutes = require __DIR__ . '/AuthRoutes.php';
        $authRoutes($route);
        
        // ASSETS 
        $route->get('/assets/{type}/{filename}', function(Request $request, Response $response, array $args) 
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
         * DEFAULTS
         */
        $route->get('[/{type}[/{action}[/{identifier}[/{has}[/{hasAction}[/{hasId]]]]]]', function (Request $request, Response $response, $args) 
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
         * UPGRADE
        */
        $route->post('/update[/{type}]', function(Request $request, Response $response, $args)
        {
            $response = new Slim\Psr7\Response(); 
            switch ($args['type']) {
                case "installMysqlDatabase":
                    $content = (new \fwc\Cms\Update\Update())->installMysqlDatabase();
                    break;
                default:
                    $content = "[INSTALL PAGE CMS Cruz]";
                    break;
            } 
            if ($content === true) {
                return $response->withHeader('Location', "/admin")->withStatus(301);        
            } else {
                $response->getBody()->write($content);
                return $response;    
            }
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
                } 
                
                // NEW
                elseif ($action == "new" || $action == "post" || $action == "add") {                    
                    $data = (new Server())->new($className, $params);                  
                } 
                
                // DELETE
                elseif ($action == "delete" || $action == "erase") {                    
                    $data = (new Server())->delete($className, $params);
                } 
                                        
                return $response->withHeader('Location', $data)->withStatus(301);  
                
            } else {            
                return false;
            }
        }); 
        
    })->add(new AuthMiddleware())->add(new InitialChecking());
};
