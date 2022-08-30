<?php

declare(strict_types=1);

namespace Plinct\Cms;

use Plinct\Cms\Middleware\Middleware;
use Plinct\Cms\Request\Request;
use Plinct\Cms\Response\Response;
use Plinct\Cms\WebSite\WebSite;

use Slim\App as Slim;

class CmsFactory
{
  /**
   * @param Slim $slim
   * @return App
   */
  public static function create(Slim $slim): App {
		return new App($slim);
  }

	/**
	 * @return Middleware
	 */
	public static function middleware(): Middleware
	{
		return new Middleware();
	}

	/**
	 * @return Request
	 */
	public static function request(): Request
	{
		return new Request();
	}

	/**
	 * @return Response
	 */
	public static function response(): Response
	{
		return new Response();
	}


	/**
	 * @return WebSite
	 */
	public static function webSite(): WebSite
	{
		return new WebSite();
	}
}
