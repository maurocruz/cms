<?php

declare(strict_types=1);

namespace Plinct\Cms\Authentication;

use Firebase\JWT\JWT;
use Plinct\Cms\App;
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
                $_SESSION['userLogin']['admin'] = $tokenDecode->admin;
                $_SESSION['userLogin']['uid'] = $tokenDecode->uid;

            } elseif (!$token && isset($userLogin)) {
                unset($_SESSION['userLogin']);
            }
        } else {
            $request = $request->withAttribute("ApiSecretKey", false);
        }

        return $handler->handle($request);
    }
}