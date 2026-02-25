<?php
namespace Plinct\Cms\Model;

use Plinct\Cms\Model\Api\WithCurl;
use Plinct\Cms\Model\Api\WithDatabase;
use Plinct\Cms\Model\Authentication\Auth;
use Plinct\Cms\Model\Type\Type;

class Model
{
	/**
	 */
	public function api()
	{
		//return new WithDatabase();
		return new WithCurl();
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
