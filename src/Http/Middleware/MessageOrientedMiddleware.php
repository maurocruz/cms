<?php
namespace Plinct\Cms\Http\Middleware;

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
		$RPC_Attr = $request->getAttribute('RPC',[]);
		if (!is_array($RPC_Attr)) {
			$RPC_Attr = [];
		}

		$authAttr = $request->getAttribute('auth', []);
		if (!is_array($authAttr)) {
			$authAttr = [];
		}

		$apiHostName = $RPC_Attr['apiHostName'] ?? '';
		$schemaOk = (bool)($RPC_Attr['schema'] ?? false);
		$tablesOk = (bool)($RPC_Attr['tables'] ?? false);

		if ($apiHostName === '') {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->message()->warning(_("You need to set the API server on index.php. Insert cms->setApi(apiUrl, apiSecretKey)")));
		} else if($schemaOk === false) {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->message()->warning(_('Não foi possível conectar com a base de dados! Verifique sua existência e conexão.')));
		} else if ($tablesOk === false) {
			$request = $request->withAttribute('EntryPoint', 'initApplication');
		} else if (!$authAttr['uid'] && $request->getUri()->getPath() !== '/admin/auth/change_password') {
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->auth()->login());
		}
		return $handler->handle($request);
	}
}
