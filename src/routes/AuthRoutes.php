<?php

use Slim\App;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteCollectorProxy as Route;

use Plinct\Cms\View\Html\HtmlView;
use Plinct\Api\Auth\SessionUser;

return function (Route $route) 
{
    $route->group('', function(Route $route) 
    {
        /**
         *   GETLOGIN
         */
        $route->get('/login', function (Request $request, Response $response, $args) 
        {
            if (SessionUser::getStatus() === '1') {
                return $response->withHeader("Location", "/admin")->withStatus(302);
                
            } else {
                $content = (new HtmlView())->login();
                
                $response->getBody()->write($content);
                return $response;
            }
        });    

        /**
         * POST LOGIN
         */
        $route->post('/login',  function (Request $request, Response $response, $args)
        {
            $data = (new Plinct\Api\Auth\AuthController())->login($request->getParsedBody());
            
            if ($data['message'] == "Session login started") {
                return $response->withHeader("Location", $_SERVER['HTTP_REFERER'] ?? "/admin")->withStatus(302);
                
            } else {
                $content = (new HtmlView())->login($data['message']);
                
                $response->getBody()->write($content);
                return $response;
            }            
        });

        $route->get('/registrar', function (Request $request, Response $response, $args)
        {
            $content = (new HtmlView())->register();
            
            $response->getBody()->write($content);
            
            return $response;
        });

       $route->post('/register', function (Request $request, Response $response, $args)
        {
            $request = (new \fwc\Cms\Auth\AuthController($this))->registerPost($request);
            
            $cmsView = new \fwc\Cms\View\CmsHtmlView($this);
            
            if ($request->getAttribute('repeatPasswordNotWork')) {
                $view = $cmsView->register("repeatPasswordNotWork");
                
            } elseif ($request->getAttribute('emailExists')) {
                $view = $cmsView->register("emailExists");
            
            } elseif ($request->getAttribute('userAdded')) {
                $view = $cmsView->register("userAdded");
                
            } else {
                $view = $cmsView->register("error");
            }
            
            $response->getBody()->write($view);
            return $response;
        });

    });

    // LOGOUT
    $route->get('/logout',  function (Request $request, Response $response, $args)
    {
        (new Plinct\Api\Auth\AuthController())->logout();
        
        return $response->withHeader("Location", $_SERVER['HTTP_REFERER'] ?? "/admin")->withStatus(302);
    });
};
