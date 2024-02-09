<?php
namespace Plinct\Cms\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class RemoveEndBar implements MiddlewareInterface {

    public function process(Request $request, RequestHandler $handler): ResponseInterface {
        $uri = $request->getUri();
        $path = $uri->getPath();
        if ($path != '/' && substr($path, -1) == '/') {
            $uri = $uri->withPath(substr($path, 0, -1));
            if ($request->getMethod() == 'GET') {
                $response = new Response();
                return $response->withHeader('Location', (string) $uri)->withStatus(301);
            } else {
                $request = $request->withUri($uri);
            }
        }
        return $handler->handle($request);
    }
}
