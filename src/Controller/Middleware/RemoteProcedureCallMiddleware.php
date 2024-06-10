<?php
declare(strict_types=1);
namespace Plinct\Cms\Controller\Middleware;

use Plinct\Cms\Controller\App;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RemoteProcedureCallMiddleware implements MiddlewareInterface {
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
	    // TODO há um erro neste processo que acontece em produção no servidor
	    $RPC_Attr = ['apiHostName' => App::getApiHost(), 'database' => 'yes', 'schema' => 'yes'];
	    $request = $request->withAttribute('RPC',$RPC_Attr);
      return $handler->handle($request);
    }
}
