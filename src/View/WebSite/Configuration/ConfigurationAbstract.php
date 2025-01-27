<?php
namespace Plinct\Cms\View\WebSite\Configuration;

use Plinct\Cms\CmsFactory;

abstract class ConfigurationAbstract
{
	/**
	 * @return void
	 */
	protected function navbar()
	{
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->title(_('Configuration'))
				->newTab('/admin/configuration', CmsFactory::view()->fragment()->icon()->home(16,16))
				->newTab('/admin/configuration/sitemap', _('Sitemap'))
				->ready()
		);
	}
}
