<?php

declare(strict_types=1);

use Firebase\JWT\JWT;
use Plinct\Cms\App;
use Plinct\Cms\Middleware\Authentication;
use Plinct\Cms\Server\Api;
use Plinct\Cms\WebSite\Fragment\Fragment;
use Plinct\Cms\WebSite\WebSite;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy as Route;

return function (Route $route)
{
    /**
     * LOGOUT
     */
    $route->get('/logout',  function (Request $request, Response $response)
    {
        session_start();
        unset($_SESSION['userLogin']);
        setcookie("API_TOKEN", "", time() - 3600);
        return $response->withHeader("Location", $_SERVER['HTTP_REFERER'] ?? "/admin")->withStatus(302);
    });

    /**
     * GET LOGIN
     */
    $route->get('/login', function (Request $request, Response $response)
    {
        if (isset($_SESSION['userLogin']['admin'])) {
            return $response->withHeader("Location", "/admin")->withStatus(302);

        } else {
            WebSite::addMain(Fragment::user()->login());
            $response->getBody()->write(WebSite::ready());
            return $response;
        }

    })->addMiddleware(new Authentication());

    /**
     * POST LOGIN
     */
    $route->post('/login',  function (Request $request, Response $response)
    {
        $parseBody = $request->getParsedBody();
        $authentication = Api::login($parseBody['email'], $parseBody['password']);

        // AUTHORIZED
        if ($authentication['status'] == "success") {
            $token = $authentication['token'];
            $tokenDecode = JWT::decode($token, App::getApiSecretKey(), ["HS256"]);

            if ($tokenDecode->admin) {
                setcookie('API_TOKEN', $token, $tokenDecode->exp);
                $location = pathinfo($_SERVER['HTTP_REFERER'])['basename'] == "register" ? "/admin" : $_SERVER['HTTP_REFERER'];
                return $response->withHeader("Location", $location)->withStatus(302);
            } else {
                $authentication['status'] = 'fail';
                $authentication['message'] = "Sorry. The user exists but is not authorized. Contact administrator.";
            }
        }

        // UNAUTHORIZED
        WebSite::addMain(Fragment::user()->login($authentication));
        $response->getBody()->write(WebSite::ready());
        return $response;

    })->addMiddleware(new Authentication());

    /**
     * REGISTER GET
     */
    $route->get('/register', function (Request $request, Response $response)
    {
        WebSite::addMain(Fragment::user()->register());
        $response->getBody()->write(WebSite::ready());
        return $response;
    });

    /**
     * REGISTER POST
     */
    $route->post('/register', function (Request $request, Response $response)
    {
        $authentication = Api::register($request->getParsedBody());
        WebSite::addMain(Fragment::user()->register($authentication));
        $response->getBody()->write(WebSite::ready());
        return $response;
    });
};
