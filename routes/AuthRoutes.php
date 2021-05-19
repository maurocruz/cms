<?php

use Firebase\JWT\JWT;
use Plinct\Cms\App;
use Plinct\Cms\Middleware\Authentication;
use Plinct\Cms\Server\Api;
use Plinct\Cms\Template\TemplateController;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy as Route;

return function (Route $route) {
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
            $template = new TemplateController();
            $template->login();
            $response->getBody()->write($template->ready());
            return $response;
        }
    })->addMiddleware(new Authentication());
    /**
     * POST LOGIN
     */
    $route->post('/login',  function (Request $request, Response $response) {
        $parseBody = $request->getParsedBody();
        $auth = Api::login($parseBody['email'], $parseBody['password']);
        // AUTHORIZED
        if ($auth['status'] == "Access authorized") {
            $token = $auth['data'];
            $tokenDecode = JWT::decode($token, App::getApiSecretKey(), ["HS256"]);
            if ($tokenDecode->admin) {
                setcookie('API_TOKEN', $token, $tokenDecode->exp);
                $location = pathinfo($_SERVER['HTTP_REFERER'])['basename'] == "register" ? "/admin" : $_SERVER['HTTP_REFERER'];
                return $response->withHeader("Location", $location)->withStatus(302);
            }
        }
        // UNAUTHORIZED
        $template = new TemplateController();
        $template->login($auth);
        $response->getBody()->write($template->ready());
        return $response;
    })->addMiddleware(new Authentication());
    /**
     * REGISTER GET
     */
    $route->get('/register', function (Request $request, Response $response) {
        $template = new TemplateController();
        $template->register();
        $response->getBody()->write($template->ready());
        return $response;
    });
    /**
     * REGISTER POST
     */
    $route->post('/register', function (Request $request, Response $response) {
        $authentication = Api::register($request->getParsedBody());
        $template = new TemplateController();
        $template->register($authentication);
        $response->getBody()->write($template->ready());
        return $response;
    });
};
