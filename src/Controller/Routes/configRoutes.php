<?php
use Plinct\Cms\CmsFactory;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Routing\RouteCollectorProxy as Route;

return function (Route $route) {

	$route->group('/config', function (Route $route) {

		$route->get('[/{method}]', function (Request $request, Response $response) {
			$method = $request->getAttribute('method') ?? 'index';
			$functionRequired = 4;
			if (CmsFactory::controller()->user()->userLogged()->hasPrivileges($functionRequired, 'crud', 'config')) {
				$controller = CmsFactory::controller()->configuration();
				$controller->$method();
			} else {
				var_dump($request->getAttributes());
				CmsFactory::view()->addMain(
					CmsFactory::view()->fragment()->miscellaneous()->message(_("You don't have privileges to access this page!"))
				);
			}
			return CmsFactory::view()->writeBody($response);
		});

		$route->post('/installModule', function (Request $request, Response $response) {
			$module = $request->getParsedBody()['module'] ?? null;
			if ($module) {
				$data = CmsFactory::controller()->configuration()->installModule($module);
				if ($data['status'] === 'success') {
					return $response->withHeader("Location", "/admin/".lcfirst($module))->withStatus(302);
				} else {
					return CmsFactory::view()->writeBody($response);
				}
			} else {
				CmsFactory::view()->addMain(CmsFactory::view()->fragment()->message()->warning(_('Module name is null')));
				return CmsFactory::view()->writeBody($response);
			}
		});
	});
};
