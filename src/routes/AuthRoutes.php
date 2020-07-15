<?php

use Slim\App;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteCollectorProxy as Route;

return function (Route $route) 
{
    $route->group('', function(Route $route) 
    {
        // LOGIN
        $route->get('/login', function (Request $request, Response $response, $args) 
        {
            if (fwc\helpers\UsersHelper::getPermission() === '1') {
                return $response->withHeader("Location", "/admin")->withStatus(302);
            } else {
                $view = new \fwc\Cms\View\CmsHtmlView($this);
                $response->getBody()->write($view->login($request, $response));
                return $response;
            }
        });    

        $route->post('/login',  function (Request $request, Response $response, $args)
        {             
            $request = (new \fwc\Cms\Auth\AuthController($this))->loginPost($request);
            
            $cmsView = new \fwc\Cms\View\CmsHtmlView($this);
            
            if ($request->getAttribute("userStatus") == "passwordNotMatch") {
                $view = $cmsView->login($request, $response);
                
            } elseif ($request->getAttribute("userStatus") == "emailNotMatch") {                
                $view = $cmsView->login($request, $response);
                
            } elseif ($request->getAttribute("userStatus") == "authorized" || $request->getAttribute("userStatus") == "logged") {  
                return $response->withHeader("Location", $_SERVER['HTTP_REFERER'] ?? "/admin")->withStatus(302);
            }       
            
            $response->getBody()->write($view);
            return $response;
        });

        $route->get('/registrar', function (Request $request, Response $response, $args)
        {
            $view = new \fwc\Cms\View\CmsHtmlView($this);
            $response->getBody()->write($view->register());
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
        (new \fwc\Cms\Auth\AuthController($this))->logout();
         return $response->withHeader("Location", $_SERVER['HTTP_REFERER'] ?? "/admin")->withStatus(302);
    });
};
