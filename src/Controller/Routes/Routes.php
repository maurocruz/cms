<?php
declare(strict_types=1);
namespace Plinct\Cms\Controller\Routes;

use Slim\Routing\RouteCollectorProxy as Route;

class Routes
{
	/**
	 * @param Route $slim
	 * @return mixed
	 */
	public function authentication(Route $slim)
  {
		$authRoutes = require __DIR__ . '/AuthRoutes.php';
	  return $authRoutes($slim);
  }

	/**
	 * @param Route $slim
	 * @return mixed
	 */
  public function assets(Route $slim)
  {
		$route = require __DIR__ . '/assetRoutes.php';
	  return $route($slim);
  }

	/**
	 * @param Route $route
	 * @return mixed
	 */
	public function config(Route $route)	{
		$configRoutes = require __DIR__.'/configRoutes.php';
		return $configRoutes($route);
	}
	/**
	 * @param Route $slim
	 * @return mixed
	 */
	public function type(Route $slim)
	{
		$route = require __DIR__ . '/typeRoutes.php';
		return $route($slim);
	}

	/**
	 * @param Route $slim
	 * @return mixed
	 */
	public function enclave(Route $slim)
	{
		$route = require __DIR__ . '/enclaveRoutes.php';
		return $route($slim);
	}

	/**
	 * @param Route $slim
	 * @return mixed
	 */
	public function home(Route $slim)
	{
		$route = include __DIR__ . '/routes.php';
		return $route($slim);
	}

	/**
	 * @param Route $slim
	 * @return mixed
	 */
  public function user(Route $slim)
  {
	  $route = require __DIR__ . '/userRoutes.php';
	  return $route($slim);
  }
}