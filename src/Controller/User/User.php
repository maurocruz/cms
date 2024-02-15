<?php
declare(strict_types=1);
namespace Plinct\Cms\Controller\User;

use Plinct\Cms\CmsFactory;

class User
{
	/**
	 * @param array $params
	 * @return mixed|string[]
	 */
	public function get(array $params = []) {
		return CmsFactory::model()->api()->get('user', $params)->ready();
	}

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
	public function index($params)
	{
		$params['orderBy'] = $params['orderBy'] ?? 'dateModified';
		$params['ordering'] = $params['ordering'] ?? 'desc';
		$params['apiToken'] = CmsFactory::controller()->user()->userLogged()->getToken();
		// DATA
		$data = CmsFactory::model()->api()->get('user', $params)->ready();

		if (isset($data['status']) && $data['status'] == 'fail') {
			// fail
			CmsFactory::view()->addMain(CmsFactory::response()->message()->warning($data['message']));
		} else {
			// view
			CmsFactory::view()->user()->index($data, $params['orderBy'], $params['ordering']);
		}
	}

	/**
	 * @param $iduser
	 * @return void
	 */
	public function edit($iduser)	{
		// DATA
		$data = CmsFactory::controller()->user()->get(['iduser' => $iduser]);
		// VIEW
		CmsFactory::view()->user()->edit($data);
	}
}
