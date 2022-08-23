<?php

declare(strict_types=1);

use Plinct\Cms\Server\Server;
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
			// CHECK AUTHENTICATION
			if (!isset($_SESSION['userLogin']['admin'])) {
				CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->auth()->login());
				$response->getBody()->write(CmsFactory::webSite()->ready());
				return $response;
			}

			$queryParams = $request->getQueryParams();
			$ns = $queryParams['ns'] ?? "";
			$className = $args['className'];
			$classNameSpace = "\\" . base64_decode($ns) . "\\" . ucfirst($className);

			CmsFactory::webSite()->enclave()->get($classNameSpace, $queryParams);

			$response->getBody()->write(CmsFactory::webSite()->ready());
			return $response;

		})->addMiddleware(CmsFactory::middleware()->authentication());

		$route->post('/{className}', function(Request $request, Response $response, $args)
		{
			// CHECK AUTHENTICATION
			if (!isset($_SESSION['userLogin']['admin'])) {
				CmsFactory::webSite()->addMain(CmsFactory::response()->fragment()->auth()->login());
				$response->getBody()->write(CmsFactory::webSite()->ready());
				return $response;
			}

			$parseBody = $request->getParsedBody();

			$queryParams = $request->getQueryParams();
			$ns = $queryParams['ns'] ?? "";
			$action = $queryParams['action'] ?? null;
			$className = $args['className'];
			$classNameSpace = "\\" . base64_decode($ns) . "\\" . ucfirst($className);

			switch ($action) {
				case 'edit':
					$returns = Server::enclave()->post($classNameSpace, $parseBody);
					break;
				case 'new':
				case 'add':
					$returns = Server::enclave()->put($classNameSpace, $parseBody);
					break;
				case 'delete':
					$returns = Server::enclave()->delete($classNameSpace, $parseBody);
					break;
				default:
					$returns = CmsFactory::response()->fragment()->noContent(_("Action not recognized"));
			}
			if (is_array($returns)) {
				CmsFactory::webSite()->addMain($returns);
				$response->getBody()->write(CmsFactory::webSite()->ready());
				return $response;
			} elseif (is_string($returns)) {
				return $response->withHeader('Location', $returns)->withStatus(301);
			} else {
				return $response->withHeader('Location', $_SERVER['HTTP_REFERER'])->withStatus(301);
			}
		})->addMiddleware(CmsFactory::middleware()->authentication());
	});
};
