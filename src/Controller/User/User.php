<?php
namespace Plinct\Cms\Controller\User;

use Plinct\Cms\CmsFactory;

class User
{
	/**
	 * @return UserLogged
	 */
	public function userLogged(): UserLogged {
		return new UserLogged();
	}

	/**
	 * @param $params
	 * @return void
	 */
	public function index($params): void
	{
		$params['orderBy'] = $params['orderBy'] ?? 'dateModified';
		$params['ordering'] = $params['ordering'] ?? 'desc';
		$params['apiToken'] = CmsFactory::controller()->user()->userLogged()->getToken();
		// DATA
		$data = CmsFactory::model()->api()->get('user', $params)->ready();

		if (isset($data['status']) && $data['status'] == 'fail') {
			// fail
			CmsFactory::view()->addMain(CmsFactory::view()->fragment()->message()->warning($data['message']));
		} else {
			// view
			CmsFactory::view()->user()->index($data, $params['orderBy'], $params['ordering']);
		}
	}

	/**
	 * @param $iduser
	 * @return void
	 */
	public function edit($iduser): void
	{
		// DATA
		$data = CmsFactory::model()->type('user')->get(['iduser' => $iduser, 'properties' => 'privileges,userCreator']);
		// VIEW
		CmsFactory::view()->user()->edit($data);
	}

	public function hasPrivileges(?array $privileges, int $function, string $action, string $namespace): bool
	{
		if ($privileges) {
			foreach ($privileges as $privilege) {
				if ($privilege['function'] == $function && $privilege['action'] == $action && $privilege['namespace'] == $namespace) {
					return true;
				}
			}
		}
		return false;
	}
}
