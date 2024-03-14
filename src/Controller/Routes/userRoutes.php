<?php

use Plinct\Cms\CmsFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy as Route;

return function (Route $route)
{
	/**
	 * PRIVILEGES
	 */
	$route->group('/privileges', function(Route $route)
	{
		$route->post('/{action}', function (Request $request, Response $response, $args)
		{
			if (!CmsFactory::controller()->user()->userLogged()->getIduser()) {
				return CmsFactory::view()->writeBody($response);
			}
			$action = $args['action'];
			$params = $request->getParsedBody();
			unset($params['submit']);

			// NEW
			if ($action == 'add' || $action == 'new' || $action == 'post') {
				$data = CmsFactory::model()->api()->post('user/privileges', $params)->ready();
			}
			// EDIT
			elseif ($action == 'edit' || $action == 'update' || $action == 'put') {
				$data = CmsFactory::model()->api()->put('user/privileges', $params)->ready();
			}
			// DELETE
			elseif ($action == 'delete' || $action == 'del' || $action == 'erase') {
				$data = CmsFactory::model()->api()->delete('user/privileges', $params)->ready();
			} else {
				$data = CmsFactory::response()->message()->warning('No action found');
			}
			// RESPONSE
			if (isset($data['status']) && $data['status'] == 'fail') {
				CmsFactory::view()->addMain(
					CmsFactory::view()->fragment()->message()->warning($data['message'])
				);
				return CmsFactory::view()->writeBody($response);
			}
			return $response->withHeader('Location', $_SERVER['HTTP_REFERER'])->withStatus(301);
		});
	});

	/**
	 * GET
	 */
	$route->get('[/{action}[/{iduser}]]', function (Request $request, Response $response, $args)
	{
		if (!CmsFactory::controller()->user()->userLogged()->getIduser()) {
			return CmsFactory::view()->writeBody($response);
		}
		$action = $args['action'] ?? 'index';
		$iduser = $args['iduser'] ?? null;
		$params = $request->getQueryParams();
		if ($action == 'new') {
			CmsFactory::view()->user()->new();
		} elseif($iduser && $action == 'edit') {
			CmsFactory::controller()->user()->edit($iduser);
		} else {
			CmsFactory::controller()->user()->index($params);
		}
		// RESPONSE
		return CmsFactory::view()->writeBody($response);
	});

	/**
	 * POST
	 */
	$route->post('/{action}', function (Request $request, Response $response, $args)
	{
		if (!CmsFactory::request()->user()->userLogged()->getIduser()) {
			return CmsFactory::response()->writeBody($response);
		}

		$action = $args['action'];
		$params = $request->getParsedBody();
		$iduser = $params['iduser'] ?? $params['id'] ?? $params['idHasTable'] ?? null;
		$params['dateModified'] = date('Y-m-d H:i:s');
		unset($params['submit']);

		// EDIT
		if ($action == 'edit' && $iduser) {
				$returns = CmsFactory::model()->api()->put('user',$params)->ready();

				if (isset($returns['status']) && $returns['status'] == 'fail') {
					CmsFactory::view()->addMain(
						CmsFactory::view()->fragment()->message()->warning($returns['message'])
					);
					// DATA
					$data = CmsFactory::model()->api()->get('user',['iduser' => $iduser, 'properties' => 'privileges']);
					// VIEW
					CmsFactory::view()->user()->edit($data);

					return CmsFactory::view()->writeBody($response);
				}
				return $response->withHeader('Location', $_SERVER['HTTP_REFERER'])->withStatus(301);

		} elseif ($action == 'erase' && $iduser) {
			CmsFactory::model()->api()->delete('user',['iduser'=>$iduser])->ready();
			return $response->withHeader('Location', '/admin/user')->withStatus(301);
		} else {
			CmsFactory::view()->addMain(
				CmsFactory::view()->fragment()->message()->warning('missing mandatory data')
			);
		}
		// RESPONSE
		return CmsFactory::view()->writeBody($response);
	});
};
