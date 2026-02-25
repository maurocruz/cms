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

		$route->post('/installDatabase', function (Request $request, Response $response) {
			$password = $request->getParsedBody()['password'] ?? null;
			$passwordRepeat = $request->getParsedBody()['passwordRepeat'] ?? null;
			$email = $request->getParsedBody()['email'] ?? null;
			if ($email && filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
				CmsFactory::view()->webSite()->authenticationView()->installDatabase("Email inválido!");
			} elseif ($password && $passwordRepeat && $password == $passwordRepeat) {
				$params = $request->getParsedBody();
				unset($params['repeatPassword']);
				unset($params['submit']);
				$data = CmsFactory::model()->api()->post('config/installDatabase',$params)->ready();
				if (isset($data['status']) && $data['status'] == 'success') {
					return $response->withHeader("Location", "/admin/")->withStatus(302);
				} else {
					CmsFactory::view()->addMain(CmsFactory::view()->fragment()->message()->warning($data['message']));
				}
			} else {
				CmsFactory::view()->webSite()->authenticationView()->installDatabase("A senha não é igual a sua repetição!");
			}
			return CmsFactory::view()->writeBody($response);
		});
	});
};
