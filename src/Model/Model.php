<?php
namespace Plinct\Cms\Model;

use Plinct\Cms\Model\Api\Connect;
use Plinct\Cms\Model\Authentication\Auth;
use Plinct\Cms\Model\Type\Type;

class Model
{
	/**
	 * @return Connect
	 */
	public function api(): Connect
	{
		return new Connect();
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
