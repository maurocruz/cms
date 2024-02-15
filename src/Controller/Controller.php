<?php
declare(strict_types=1);
namespace Plinct\Cms\Controller;

use Plinct\Cms\Controller\Authentication\Authentication;
use Plinct\Cms\Controller\Configuration\Configuration;
use Plinct\Cms\Controller\Routes\Routes;
use Plinct\Cms\Controller\Type\Type;
use Plinct\Cms\Controller\User\User;
use Plinct\Cms\Controller\Type\TypeController;
use Psr\Http\Message\ServerRequestInterface;

class Controller
{
	/**
	 * @return Authentication
	 */
	public function Authentication(): Authentication
	{
		return new Authentication();
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

	/**
	 * @return Type
	 */
	public function type(): Type
	{
		return new Type();
	}

	/**
	 * @param ServerRequestInterface $request
	 * @return TypeController
	 */
	public function typeController(ServerRequestInterface $request): TypeController
	{
		return new TypeController($request);
	}
}
