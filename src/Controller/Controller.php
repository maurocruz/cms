<?php
declare(strict_types=1);
namespace Plinct\Cms\Controller;

use Plinct\Cms\Controller\Configuration\Configuration;
use Plinct\Cms\Controller\Routes\Routes;
use Plinct\Cms\Controller\User\User;

class Controller
{
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
	public function user(): User
	{
		return new User();
	}

	/**
	 * @return Configuration
	 */
	public function configuration(): Configuration
	{
		return new Configuration();
	}
}
