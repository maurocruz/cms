<?php
declare(strict_types=1);
namespace Plinct\Cms\Request\User;

use Plinct\Cms\CmsFactory;

class User
{
	/**
	 * @param array $params
	 * @return mixed|string[]
	 */
	public function get(array $params = []) {
		return CmsFactory::request()->server()->api()->get('user', $params)->ready();
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
	}

	/**
	 * @param $iduser
	 * @param $params
	 * @return void
	 */
	public function edit($iduser, $params)
	{
		// DATA
		$data = CmsFactory::request()->user()->get(['iduser' => $iduser, 'properties' => 'privileges']);
		// VIEW
		CmsFactory::response()->view()->user()->edit($data);
	}
}
