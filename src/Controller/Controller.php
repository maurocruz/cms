<?php
namespace Plinct\Cms\Controller;

use Plinct\Cms\Controller\Authentication\Authentication;
use Plinct\Cms\Controller\Configuration\ConfigurationController;
use Plinct\Cms\Controller\Routes\Routes;
use Plinct\Cms\Controller\User\User;
use Plinct\Cms\Controller\Type\TypeController;
use Psr\Http\Message\ServerRequestInterface;

class Controller
{

	/**
	 * @return string|null
	 */
	public function getApiHost(): ?string
	{
		return App::getApiHost();
	}

	public function setApiHost(string $apiHost): void
	{
		App::setApiHost($apiHost);
	}

	/**
	 * @return string|null
	 */
	public function getHost(): ?string
	{
		return App::getURL();
	}

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
	 * @return ConfigurationController
	 */
	public function configuration(): ConfigurationController
	{
		return new ConfigurationController();
	}

	/**
	 * @param string $type
	 * @return Type\Type
	 */
	public function type(string $type): Type\Type
	{
		return new Type\Type($type);
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
