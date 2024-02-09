<?php

declare(strict_types=1);

use Plinct\Cms\Middleware\AuthenticationMiddleware;
use Plinct\Cms\Middleware\GatewayMiddleware;
use Plinct\Cms\Middleware\RemoveEndBar;
use Slim\Routing\RouteCollectorProxy as Route;

use Plinct\Cms\CmsFactory;

/**
 * ADMIN ROUTES
 */
return function (Route $route)
{
	CmsFactory::webSite()->create();

  $route->group('/admin', function(Route $route)
  {
	  /**
	   * ASSETS
	   */
	  CmsFactory::request()->routes()->assets($route);

    /**
     * AUTHENTICATION ROUTES
     */
		CmsFactory::request()->routes()->authentication($route);

	  /**
	   * USER
	   */
		$route->group('/user', function (Route $route) {
			CmsFactory::request()->routes()->user($route);
		});

    /**
     * ENCLAVE
     */
		CmsFactory::request()->routes()->enclave($route);

    /**
     * TYPES
     */
		CmsFactory::request()->routes()->type($route);

  })->addMiddleware(new GatewayMiddleware())
	  ->addMiddleware(new AuthenticationMiddleware());
};
