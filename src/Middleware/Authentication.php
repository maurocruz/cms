<?php
namespace Plinct\Cms\Middleware;

use Firebase\JWT\JWT;
use Plinct\Cms\App;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Authentication implements MiddlewareInterface {
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        //var_dump(session_status());
        //var_dump(PHP_SESSION_NONE);
        if (session_status() === PHP_SESSION_NONE) session_start();
        $token = $_COOKIE['API_TOKEN'] ?? null;
        $tokenDecode = $token ? JWT::decode($token, App::getApiSecretKey(), ["HS256"]) : null;
        $userLogin = $_SESSION['userLogin'] ?? null;
        if ($token && !$userLogin) {
            $_SESSION['userLogin']['name'] = $tokenDecode->name;
            $_SESSION['userLogin']['admin'] = $tokenDecode->admin;
            $_SESSION['userLogin']['uid'] = $tokenDecode->uid;
        } elseif(!$token && isset($userLogin) ) {
            unset($_SESSION['userLogin']);
        }
        return $handler->handle($request);
    }
}