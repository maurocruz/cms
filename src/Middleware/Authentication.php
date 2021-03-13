<?php
namespace Plinct\Cms\Middleware;

use Firebase\JWT\JWT;
use Plinct\Api\PlinctApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Authentication implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $token = $_COOKIE['API_TOKEN'] ?? null;
        if ($token && !isset($_SESSION['userLogin'])) {
            $tokenDecode = JWT::decode($token, PlinctApi::$JWT_SECRET_API_KEY, ["HS256"]);
            $_SESSION['userLogin']['name'] = $tokenDecode->name;
            $_SESSION['userLogin']['admin'] = $tokenDecode->admin;
            $_SESSION['userLogin']['uid'] = $tokenDecode->uid;
        } elseif(!$token && isset($_SESSION['userLogin'])) {
            unset($_SESSION['userLogin']);
        }
        return $handler->handle($request);
    }
}