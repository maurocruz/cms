<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy as Route;

use Plinct\Cms\CmsFactory;

return function (Route $route)
{
	/**
	 * PRIVILEGES
	 */
	$route->group('/privileges', function(Route $route)
	{
		$route->post('/{action}', function (Request $request, Response $response, $args) {
			$action = $args['action'];
			$params = $request->getParsedBody();
			unset($params['submit']);

			// NEW
			if ($action == 'add' || $action == 'new' || $action == 'post') {
				$data = CmsFactory::request()->server()->api()->post('user/privileges', $params)->ready();
			}
			// EDIT
			elseif ($action == 'edit' || $action == 'update' || $action == 'put') {
				$data = CmsFactory::request()->server()->api()->put('user/privileges', $params)->ready();
			}
			// DELETE
			elseif ($action == 'delete' || $action == 'del' || $action == 'erase') {
				$data = CmsFactory::request()->server()->api()->delete('user/privileges', $params)->ready();
			} else {
				$data = CmsFactory::response()->message()->warning('No action found');
			}

			// RESPONSE
			if (isset($data['status']) && $data['status'] == 'fail') {
				CmsFactory::webSite()->addMain(
					CmsFactory::response()->message()->warning($data['message'])
				);
				return CmsFactory::response()->writeBody($response);
			}

			return $response->withHeader('Location', $_SERVER['HTTP_REFERER'])->withStatus(301);
		});
	});

	/**
	 * GET
	 */
	$route->get('[/{action}[/{iduser}]]', function (Request $request, Response $response, $args)
	{
		$action = $args['action'] ?? 'index';
		$iduser = $args['iduser'] ?? null;
		$params = $request->getQueryParams();

		CmsFactory::response()->isUserLogged(function() use ($params, $action, $iduser) {
			if ($action == 'new') {
				CmsFactory::response()->view()->user()->new($params);
			} elseif($iduser && $action == 'edit') {
				CmsFactory::request()->user()->edit($iduser, $params);
			} else {
				CmsFactory::request()->user()->index($params);
			}
		});
		// RESPONSE
		return CmsFactory::response()->writeBody($response);
	});

	$route->post('/{action}', function (Request $request, Response $response, $args)
	{
		$action = $args['action'];
		$params = $request->getParsedBody();
		unset($params['submit']);
		// EDIT
		if ($action == 'edit') {
			$iduser = $params['iduser'] ?? $params['id'] ?? $params['idHasTable'] ?? null;

			if ($iduser) {
				$returns = CmsFactory::request()->server()->api()->put('user',$params)->ready();

				if (isset($returns['status']) && $returns['status'] == 'fail') {
					CmsFactory::webSite()->addMain(
						CmsFactory::response()->message()->warning($returns['message'])
					);
					// DATA
					$data = CmsFactory::request()->user()->get(['iduser' => $iduser, 'properties' => 'privileges']);
					// VIEW
					CmsFactory::response()->view()->user()->edit($data);

					return CmsFactory::response()->writeBody($response);
				}
				return $response->withHeader('Location', $_SERVER['HTTP_REFERER'])->withStatus(301);

			} else {
				CmsFactory::webSite()->addMain(
					CmsFactory::response()->message()->warning('missing mandatory data')
				);
			}
		}
		// RESPONSE
		return CmsFactory::response()->writeBody($response);
	});
};
