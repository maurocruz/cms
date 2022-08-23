<?php

declare(strict_types=1);

namespace Plinct\Cms\Request;

use Plinct\Cms\Request\Api\Api;
use Plinct\Cms\Request\Type\Type;
use Plinct\Cms\Request\User\User;

class Request
{
	/**
	 * @return Api
	 */
	public function api(): Api
	{
		return new Api();
	}
	/**
	 * @return User
	 */
	public function user(): User {
		return new User();
	}

	public function type(): Type {
		return new Type();
	}
}
