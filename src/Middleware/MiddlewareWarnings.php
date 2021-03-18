<?php
namespace Plinct\Cms\Middleware;

use Plinct\PDO\PDOConnect;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MiddlewareWarnings implements MiddlewareInterface {

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
        $error = PDOConnect::getError();
        if ($error) {
            $request = $request->withAttribute("warnings", $error['error']['message']);
        }
        return $handler->handle($request);
    }
}