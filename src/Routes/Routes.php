<?php

declare(strict_types=1);

namespace Plinct\Cms\Routes;

use Slim\Routing\RouteCollectorProxy as Route;

class Routes
{
	public function authentication(Route $slim)
  {
		$authRoutes = require __DIR__ . '/AuthRoutes.php';
	  return $authRoutes($slim);
  }

  public function assets(Route $slim)
  {
		$route = require __DIR__ . '/assetRoutes.php';
	  return $route($slim);
  }

	public function type(Route $slim)
	{
		$route = require __DIR__.'/typeRoutes.php';
		return $route($slim);
	}

	public function enclave(Route $slim)
	{
		$route = require __DIR__.'/enclaveRoutes.php';
		return $route($slim);
	}

	public function home(Route $slim)
	{
		$route = include __DIR__ . '/routes.php';
		return $route($slim);
	}

  public function user(Route $slim)
  {
	  $route = require __DIR__.'/userRoutes.php';
	  return $route($slim);
  }
}