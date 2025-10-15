<?php
namespace Plinct\Cms\Controller\Middleware;

use Plinct\Cms\CmsFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MessageOrientedMiddleware implements MiddlewareInterface
{
	/**
	 * @param ServerRequestInterface $request
	 * @param RequestHandlerInterface $handler
	 * @return ResponseInterface
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$RPC_Attr = $request->getAttribute('RPC');
		$authAttr = $request->getAttribute('auth');
		if (!$RPC_Attr['apiHostName']) {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->message()->warning(_("You need to set the API server on index.php. Insert cms->setApi(apiUrl, apiSecretKey)")));
		} else if($RPC_Attr['database'] === 'no') {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->message()->warning(_('You need to create the database before starting the application.')));
		} else if ($RPC_Attr['schema'] === 'no') {
			CmsFactory::View()->addMain("<div class='warning'><p>"._('Tables do not exist!')."</p><button class='button'><a href='/admin/config/initApplication'>"._('Launch application?')."</a></button> </div>");
		} else if (!$authAttr['uid'] && $request->getUri()->getPath() !== '/admin/auth/change_password') {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->auth()->login());
		}
		return $handler->handle($request);
	}
}
