<?php

declare(strict_types=1);

namespace Plinct\Cms\Controller\Request;

use Plinct\Cms\Controller\Request\Api\Api;
use Plinct\Cms\Controller\Request\Server\Server;
use Plinct\Cms\Controller\Request\Type\Type;
use Plinct\Cms\Controller\Request\User\User;
use Plinct\Cms\Controller\Routes\Routes;

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
