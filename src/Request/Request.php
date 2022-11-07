<?php

declare(strict_types=1);

namespace Plinct\Cms\Request;

use Plinct\Cms\Request\Api\Api;
use Plinct\Cms\Request\Server\Server;
use Plinct\Cms\Request\Type\Type;
use Plinct\Cms\Request\User\User;
use Plinct\Cms\Routing\Routes;

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
	 * @return Routes
	 */
	public function routes(): Routes
	{
		return new Routes();
	}

	/**
	 * @return User
	 */
	public function user(): User {
		return new User();
	}

	/**
	 * @return Server
	 */
	public function server(): Server
	{
		return new Server();
	}

	/**
	 * @return Type
	 */
	public function type(): Type {
		return new Type();
	}
}
