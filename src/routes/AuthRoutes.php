<?php

use Firebase\JWT\JWT;
use Plinct\Api\Auth\AuthController;
use Plinct\Api\PlinctApi;
use Plinct\Cms\Middleware\Authentication;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy as Route;
use Plinct\Cms\View\Html\HtmlView;

return function (Route $route) {
    /**
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
    /**
     * LOGOUT
     */
    $route->get('/logout',  function (Request $request, Response $response) {
        session_start();
        unset($_SESSION['userLogin']);
        setcookie("API_TOKEN", "", time() - 3600);
        return $response->withHeader("Location", $_SERVER['HTTP_REFERER'] ?? "/admin")->withStatus(302);
    });
    /**
     * GET LOGIN
     */
    $route->get('/login', function (Request $request, Response $response) {
        if (isset($_SESSION['userLogin']['admin'])) {
            return $response->withHeader("Location", "/admin")->withStatus(302);
        } else {
            $content = (new HtmlView())->login();
            $response->getBody()->write($content);
            return $response;
        }
    })->addMiddleware(new Authentication());
    /**
     * POST LOGIN
     */
    $route->post('/login',  function (Request $request, Response $response) {
        $token = (new AuthController())->token($request->getParsedBody());
        if ($token) {
            $tokenDecode = JWT::decode($token, PlinctApi::$JWT_SECRET_API_KEY, ["HS256"]);
            if ($tokenDecode->admin) {
                setcookie('API_TOKEN', $token, time() + PlinctApi::$JWT_EXPIRE);
                return $response->withHeader("Location", $_SERVER['HTTP_REFERER'] ?? "/admin")->withStatus(302);
            }
        }
        $content = (new HtmlView())->login($token);
        $response->getBody()->write($content);
        return $response;
    })->addMiddleware(new Authentication());
    /**
     * REGISTER GET
     */
    $route->get('/registrar', function (Request $request, Response $response) {
        $content = (new HtmlView())->register();
        $response->getBody()->write($content);
        return $response;
    });
    /**
     * REGISTER POST
     */
    $route->post('/register', function (Request $request, Response $response) {
        $params['name'] = $request->getParsedBody()['name'];
        $params['email'] = $request->getParsedBody()['email'];
        $params['password'] = $request->getParsedBody()['password'];
        $authentication = (new AuthController())->register($params);
        $htmlView = new HtmlView();
        $view = $htmlView->register($authentication);
        $response->getBody()->write($view);
        return $response;
    });
};
