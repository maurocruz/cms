<?php
declare(strict_types=1);

use Plinct\Cms\Controller\Middleware\AuthenticationMiddleware;
use Plinct\Cms\Controller\Middleware\RemoteProcedureCallMiddleware;
use Plinct\Cms\Controller\Middleware\MessageOrientedMiddleware;
use Slim\Routing\RouteCollectorProxy as Route;

use Plinct\Cms\CmsFactory;

/**
 * ADMIN ROUTES
 */
return function (Route $route)
{
	CmsFactory::view()->createWebSite();

  $route->group('/admin', function(Route $route) {
	  /**
	   * ASSETS
	   */
	  CmsFactory::controller()->routes()->assets($route);

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

  })->addMiddleware(new MessageOrientedMiddleware())
	  ->addMiddleware(new AuthenticationMiddleware())
	  ->addMiddleware(new RemoteProcedureCallMiddleware());
};
