<?php
declare(strict_types=1);
namespace Plinct\Cms\Model;

use Plinct\Cms\Model\Api\Api;
use Plinct\Cms\Model\Authentication\Auth;
use Plinct\Cms\Model\Type\Type;

class Model
{
	/**
	 * @return Api
	 */
	public function api(): Api {
		return new Api();
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
