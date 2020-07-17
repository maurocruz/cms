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

        
        // COMPONENTS JSX
       /*$route->get('/main.js.map', function(Request $request, Response $response, array $args)
        {           
            $file = '/ReactComponents/main.js.map';
           
            $script = file_get_contents(__DIR__ . $file);
            
            $newResponse = $response->withHeader("Content-type", "application/javascript");
            
            $newResponse->getBody()->write($script);
            
            return $newResponse;
        });
        
        // COMPONENTS JSX
        $route->get('/components[/{params: .*}]', function(Request $request, Response $response, array $args)
        {
            if (isset($args['params'])) { 
               // $file = '/ReactComponents/src/components/'.$args['params'];
            } else {
                $file = __DIR__.'/../views/html/assets/js/main.js';
            }
            $script = file_get_contents(__DIR__ . $file);
            
            $newResponse = $response->withHeader("Content-type", "application/javascript");
            
            $newResponse->getBody()->write($script);
            
            return $newResponse;
        });*/
        
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
         * ADMIN POST THING
         */
        $route->post('/{type}/{action}', function (Request $request, Response $response, $args) 
        {                
            $type = $args['type'];
            $action = $args['action'];
            $params = $request->getParsedBody();
            unset($params['submit']);
            unset($params['submit_x']);
            unset($params['submit_y']);
            
            $className = "\\Plinct\\Api\\Type\\".ucfirst($type);
                            
            if (class_exists($className)) {
                
                //  EDIT
                if ($action == "edit" || $action == "put") {
                    $idName = "id".$type;
                    $idValue = $params[$idName];
                    unset($params[$idName]);
                    unset($params['output']);

                    (new $className())->put($idValue,$params);
                    
                    return $response->withHeader('Location', $_SERVER['HTTP_REFERER'])->withStatus(301);
                } 
                
                // NEW
                elseif ($action == "new" || $action == "post" || $action == "add") {
                    
                    $output = $params['output'] ?? null;
                    unset($params['output']);
                    
                    $data = (new $className($request))->post($params);
                    
                    $id = $data['id'];
                    
                    $return = $output == "referer" ? $_SERVER['HTTP_REFERER'] : dirname($_SERVER['HTTP_REFERER'])."/edit/$id";                    
                    
                    return $response->withHeader('Location', $return)->withStatus(301);
                    
                } 
                
                // DELETE
                elseif ($action == "delete" || $action == "erase") {
                    $idName = "id".$type;
                    $idValue = $params[$idName];
                                        
                    $output = $params['output'] ?? null;
                    unset($params['output']);
                                        
                    (new $className())->delete($params);
                                        
                    $return = $output == "referer" ? $_SERVER['HTTP_REFERER'] : dirname($_SERVER['REQUEST_URI']);     
                    
                    return $response->withHeader('Location', $return)->withStatus(301);
                } 
                
            } else {            
                return false;
            }
        }); 
        
    })->add(new AuthMiddleware())->add(new InitialChecking());
};
