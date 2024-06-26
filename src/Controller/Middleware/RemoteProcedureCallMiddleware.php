<?php
declare(strict_types=1);
namespace Plinct\Cms\Controller\Middleware;

use Plinct\Cms\CmsFactory;
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
			$cookieApiToken =$request->getCookieParams()['API_TOKEN'] ?? null;
	    $RPC_Attr = ['apiHostName' => App::getApiHost(), 'database' => 'no', 'schema' => 'no'];
			if ($cookieApiToken) {
				$RPC_Attr['database'] = 'yes';
				$RPC_Attr['schema'] = 'yes';
			} else {
				// IF API IS NOT SETTING IN INDEX
				if ($RPC_Attr['apiHostName']) {
					// CHECK SCHEMA EXISTS
					$data = CmsFactory::model()->api()->get()->ready();
					// IF SCHEMA EXISTS
					if ($data['status'] === 'success') {
						$RPC_Attr['database'] = 'yes';
						// check if user table exists
						$data = CmsFactory::model()->api()->get('config/database', ['showTableStatus' => 'user'])->ready();
						if ($data['message'] === 'table exist') {
							$RPC_Attr['schema'] = 'yes';
						}
					}
				}
			}
	    $request = $request->withAttribute('RPC',$RPC_Attr);
      return $handler->handle($request);
    }
}
