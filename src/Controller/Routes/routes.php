<?php

use Plinct\Cms\Http\Middleware\AuthenticationMiddleware;
use Plinct\Cms\Http\Middleware\MessageOrientedMiddleware;
use Plinct\Cms\Http\Middleware\RemoteProcedureCallMiddleware;
use Slim\Routing\RouteCollectorProxy as Route;

use Plinct\Cms\CmsFactory;

/**
 * ADMIN ROUTES
 */
return function (Route $route)
{
	CmsFactory::view()->createWebSite();

  $route->group('/admin', function(Route $route)
  {
    /**
     * AUTHENTICATION ROUTES
     */
		CmsFactory::controller()->routes()->authentication($route);

		/**
	   * CONFIGURATION ROUTES
	   */
		CmsFactory::controller()->routes()->config($route);

	  /**
	   * USER
	   */
		$route->group('/user', function (Route $route) {
			CmsFactory::controller()->routes()->user($route);
		});

    /**
     * ENCLAVE
     */
		CmsFactory::controller()->routes()->enclave($route);

    /**
     * TYPES
     */
		CmsFactory::controller()->routes()->type($route);

  })->add(new MessageOrientedMiddleware())
	  ->addMiddleware(new AuthenticationMiddleware())
	  ->addMiddleware(new RemoteProcedureCallMiddleware());
};
