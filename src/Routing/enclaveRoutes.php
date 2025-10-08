<?php

use Plinct\Cms\Request\Server\Server;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy as Route;

use Plinct\Cms\CmsFactory;

return function (Route $route)
{
	$route->group('/{controller:closure|enclave}', function (Route $route)
	{
		$route->get('/{className}', function (Request $request, Response $response, $args)
		{
			if (!CmsFactory::request()->user()->userLogged()->getIduser()) {
				return CmsFactory::response()->writeBody($response);
			}
			$queryParams = $request->getQueryParams();
			$className = $args['className'];
			if (!CmsFactory::request()->user()->userLogged()->hasPrivileges(1,'r',$className)) {
				CmsFactory::webSite()->addMain(
					CmsFactory::response()->fragment()->miscellaneous()->message(_("You don't have privileges to access this page!"))
				);
			} else {
				$ns = $queryParams['ns'] ?? "";
				$classNameSpace = "\\" . base64_decode($ns) . "\\" . ucfirst($className);
				CmsFactory::webSite()->enclave()->get($classNameSpace, $queryParams);
			}
			return CmsFactory::response()->writeBody($response);
		});

		$route->post('/{className}', function(Request $request, Response $response, $args)
		{
			// CHECK AUTHENTICATION
			if (!CmsFactory::request()->user()->userLogged()->getIduser()) {
				CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->auth()->login());
				return CmsFactory::response()->writeBody($response);
			}

			$parseBody = $request->getParsedBody();
			$queryParams = $request->getQueryParams();
			$ns = $queryParams['ns'] ?? "";
			$action = $queryParams['action'] ?? null;
			$className = $args['className'];
			$classNameSpace = "\\" . base64_decode($ns) . "\\" . ucfirst($className);

			$returns = match ($action) {
				'edit' => Server::enclave()->put($classNameSpace, $parseBody),
				'new', 'add' => Server::enclave()->post($classNameSpace, $parseBody),
				'delete' => Server::enclave()->delete($classNameSpace, $parseBody),
				default => CmsFactory::response()->fragment()->noContent(_("Action not recognized")),
			};
			if (is_array($returns)) {
				CmsFactory::webSite()->addMain($returns);
				return CmsFactory::response()->writeBody($response);
			} elseif (is_string($returns)) {
				return $response->withHeader('Location', $returns)->withStatus(301);
			} else {
				return $response->withHeader('Location', $_SERVER['HTTP_REFERER'])->withStatus(301);
			}
		});

	});
};
