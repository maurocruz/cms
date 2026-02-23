<?php
namespace Plinct\Cms\Http\Middleware;

use Plinct\Cms\Application\Context\RequestContext;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Routing\RouteContext;

class HttpExceptionHandlerMiddleware implements MiddlewareInterface
{
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$context = $request->getAttribute(RequestContext::class);
		$user = $context->getUser();
		$routeContext = RouteContext::fromRequest($request);
		$route = $routeContext->getRoute();
		$nameRoute = $route->getName();
		$nameParts = is_string($nameRoute) ? explode(".",$nameRoute) : [];
		$namespace = reset($nameParts);
		$action = match ($request->getMethod()) {
			"GET" => "r",
			"POST" => "c",
			"PUT" => "u",
			"DELETE" => "d",
			default => "x"
		};
		// VERIFICAR LOGIN
		if(!$user && $namespace != 'auth') {
			throw  new HttpUnauthorizedException($request);
    }
		// VERIFICAR PRIVILEGIOS
		if ($namespace != 'auth' && $namespace != 'home' && ($user && !$user->hasPrivilege(1,$action,$namespace))) {
			throw new HttpForbiddenException($request);
		}
		return $handler->handle($request);
	}
}
