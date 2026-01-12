<?php
namespace Plinct\Cms\Controller\Routes;

use Slim\Routing\RouteCollectorProxy as Route;

class Routes
{
	/**
	 * @param Route $slim
	 * @return mixed
	 */
	public function authentication(Route $slim): mixed
	{
		$authRoutes = require __DIR__ . '/AuthRoutes.php';
	  return $authRoutes($slim);
  }

	/**
	 * @param Route $route
	 * @return mixed
	 */
	public function config(Route $route): mixed
	{
		$configRoutes = require __DIR__.'/configRoutes.php';
		return $configRoutes($route);
	}
	/**
	 * @param Route $slim
	 * @return mixed
	 */
	public function type(Route $slim): mixed
	{
		$route = require __DIR__ . '/typeRoutes.php';
		return $route($slim);
	}

	/**
	 * @param Route $slim
	 * @return mixed
	 */
	public function enclave(Route $slim): mixed
	{
		$route = require __DIR__ . '/enclaveRoutes.php';
		return $route($slim);
	}

	/**
	 * @param Route $slim
	 * @return mixed
	 */
	public function home(Route $slim): mixed
	{
		$route = include __DIR__ . '/routes.php';
		return $route($slim);
	}

	/**
	 * @param Route $slim
	 * @return mixed
	 */
  public function user(Route $slim): mixed
  {
	  $route = require __DIR__ . '/userRoutes.php';
	  return $route($slim);
  }
}
