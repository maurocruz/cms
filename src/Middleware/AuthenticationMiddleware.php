<?php

declare(strict_types=1);

namespace Plinct\Cms\Middleware;

use Firebase\JWT\JWT;
use Plinct\Cms\App;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\Request\User\UserLogged;
use Plinct\Web\Debug\Debug;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthenticationMiddleware implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        if (session_status() === PHP_SESSION_NONE) session_start();

        $token = $_COOKIE['API_TOKEN'] ?? null;
        if (App::getApiSecretKey()) {
            $tokenDecode = $token ? JWT::decode($token, App::getApiSecretKey(), ["HS256"]) : null;
            $userLogin = $_SESSION['userLogin'] ?? null;

            if ($token && !$userLogin) {
                $_SESSION['userLogin']['name'] = $tokenDecode->name;
                $_SESSION['userLogin']['uid'] = $tokenDecode->uid;

            } elseif ($token && isset($userLogin)) {
	            CmsFactory::request()->user()->userLogged()->setIduser($tokenDecode->uid);
	            CmsFactory::request()->user()->userLogged()->setName($tokenDecode->name);
							CmsFactory::request()->user()->userLogged()->setToken($token);

            } else {
                unset($_SESSION['userLogin']);
            }
        } else {
            $request = $request->withAttribute("ApiSecretKey", false);
        }

        return $handler->handle($request);
    }
}