<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy as Route;

use Plinct\Cms\CmsFactory;

return function (Route $route)
{
	/**
	 * INDEX
	 */
	$route->get('', function (Request $request, Response $response, $args)
	{
		if (CmsFactory::request()->user()->userLogged()->getIduser()) {
			$params = $request->getQueryParams();
			$params['orderBy'] = $params['orderBy'] ?? 'dateModified';
			$params['ordering'] = $params['ordering'] ?? 'desc';
			// DATA
			$data = CmsFactory::request()->user()->get($params);

			if (isset($data['status']) && $data['status'] == 'fail') {
				CmsFactory::response()->webSite()->addMain(
					CmsFactory::response()->message()->warning($data['message'])
				);
			} else {
				// VIEW
				CmsFactory::response()->view()->user()->index($data, $params['orderBy'], $params['ordering']);
			}
		} else {
			CmsFactory::webSite()->addMain(
				CmsFactory::response()->fragment()->auth()->login()
			);
		}
		// RESPONSE
		return CmsFactory::response()->writeBody($response);
	});

	/**
	 * NEW
	 */
	$route->get('/new', function (Request $request, Response $response, $args)
	{
		if (CmsFactory::request()->user()->userLogged()->getIduser()) {
			$params = $request->getQueryParams();
			// VIEW
			CmsFactory::response()->view()->user()->new($params);
		} else {
			CmsFactory::webSite()->addMain(
				CmsFactory::response()->fragment()->auth()->login()
			);
		}
		// RESPONSE
		return CmsFactory::response()->writeBody($response);
	});

	/**
	 * EDIT
	 */
	$route->get('/edit[/{iduser}]', function (Request $request, Response $response, $args)
	{
		if (CmsFactory::request()->user()->userLogged()->getIduser()) {
			$params = $request->getQueryParams();
			$iduser = $args['iduser'] ?? $params['iduser'] ?? null;
			// DATA
			$data = $iduser ? CmsFactory::request()->user()->get(['iduser' => $iduser, 'properties' => 'privileges']) : null;
			// VIEW
			CmsFactory::response()->view()->user()->edit($data);
		} else {
			CmsFactory::webSite()->addMain(
				CmsFactory::response()->fragment()->auth()->login()
			);
		}
		// RESPONSE
		return CmsFactory::response()->writeBody($response);
	});

	$route->group('/privileges', function(Route $route) {
		$route->post('', function (Request $request, Response $response) {
			$params = $request->getParsedBody();
			unset($params['submit']);
			$data = CmsFactory::server()->api()->post('user/privileges', $params)->ready();
			if (isset($data['status']) && $data['status'] == 'fail') {
				CmsFactory::webSite()->addMain(
					CmsFactory::response()->message()->warning($data['message'])
				);
				return CmsFactory::response()->writeBody($response);
			}
			return $response->withHeader('Location', $_SERVER['HTTP_REFERER'])->withStatus(301);
		});
	});
};
