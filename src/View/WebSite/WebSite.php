<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite;

use Plinct\Cms\View\Authentication\AuthenticationView;
use Plinct\Cms\View\WebSite\Configuration\ConfigurationView;
use Plinct\Cms\View\WebSite\Index\Index;
use Plinct\Cms\View\WebSite\Type\Type;

class WebSite extends WebSiteFactoryAbstract
{
	/**
	 * @return AuthenticationView
	 */
	public function authenticationView(): AuthenticationView
	{
		return new AuthenticationView();
	}
	/**
	 * @return ConfigurationView
	 */
	public function configuration(): ConfigurationView
	{
		return new ConfigurationView();
	}

	/**
	 * @return Index
	 */
	public function index(): Index
	{
		return new Index();
	}

	/**
	 * @param string $type
	 * @return Type
	 */
	public function type(string $type): Type
	{
		return new Type($type);
	}
}
