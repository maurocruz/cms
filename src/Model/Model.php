<?php
namespace Plinct\Cms\Model;

use Plinct\Cms\Controller\App;
use Plinct\Cms\Model\Api\Api;
use Plinct\Cms\Model\Api\WithConnectBd;
use Plinct\Cms\Model\Authentication\Auth;
use Plinct\Cms\Model\Type\Type;

class Model
{
	/**
	 * @return Api|WithConnectBd
	 */
	public function api(): Api|WithConnectBd
	{
		if (App::isRemoteApi()) {
			return new Api();
		} else {
			return new WithConnectBd();
		}

	}

	/**
	 * @return Auth
	 */
	public function auth(): Auth {
		return new Auth();
	}

	/**
	 * @param string $type
	 * @return Type
	 */
	public function type(string $type): Type
	{
		return new Type($type);
	}
}
