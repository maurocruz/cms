<?php

use Plinct\Api\Auth\AuthController;
use Plinct\Api\PlinctApi;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy as Route;

use Plinct\Cms\View\Html\HtmlView;
use Plinct\Api\Auth\SessionUser;

return function (Route $route) 
{
    /*
     * START APPLICATION
     */
    $route->post('/startApplication', function(Request $request, Response $response) {
        $params = $request->getParsedBody();
        unset($params['passwordRepeat']);

        $data = PlinctApi::starApplication($params);

        if (isset($data['error'])) {
            $view = new HtmlView();
            $content = $view->login($data['error']['message']);

            $response->getBody()->write($content);
            return $response;

        } else {
            return $response->withHeader("Location", "/admin")->withStatus(302);
        }
    });

    $route->group('', function(Route $route) 
    {
        /**
         * GET LOGIN
         */
        $route->get('/login', function (Request $request, Response $response)
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
        $route->post('/login',  function (Request $request, Response $response)
        {
            $data = (new AuthController())->login($request->getParsedBody());

            if (isset($data['error'])) {
                $code = $data['error']['code'];
                $message = $data['error']['message'];
                $content = (new HtmlView())->error($code, $message);
                $response->getBody()->write($content);
                return $response;

            } elseif ($data['message'] == "Session login started") {
                return $response->withHeader("Location", $_SERVER['HTTP_REFERER'] ?? "/admin")->withStatus(302);
                
            } else {
                $content = (new HtmlView())->login($data['message']);
                $response->getBody()->write($content);
                return $response;
            }            
        });

        $route->get('/registrar', function (Request $request, Response $response)
        {
            $content = (new HtmlView())->register();
            $response->getBody()->write($content);
            return $response;
        });

        $route->post('/register', function (Request $request, Response $response)
        {
            $params['name'] = $request->getParsedBody()['name'];
            $params['email'] = $request->getParsedBody()['email'];
            $params['password'] = $request->getParsedBody()['password'];

            $authentication = (new AuthController())->register($params);
            
            $htmlView = new HtmlView();

            $view = $htmlView->register($authentication);
            
            $response->getBody()->write($view);
            return $response;
        });

    });

    // LOGOUT
    $route->get('/logout',  function (Request $request, Response $response)
    {
        (new AuthController())->logout();
        return $response->withHeader("Location", $_SERVER['HTTP_REFERER'] ?? "/admin")->withStatus(302);
    });
};
