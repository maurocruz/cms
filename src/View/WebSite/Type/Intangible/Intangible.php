<?php
namespace Plinct\Cms\View\WebSite\Type\Intangible;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;

class Intangible implements TypeViewInterface
{
	/**
	 * @return void
	 */
	public static function navbar(): void
	{
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->title(_('Intangible'))
				->type('Intanglble')
				->newTab('/admin/intangible',CmsFactory::view()->fragment()->icon()->home())
				->ready()
		);
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function index(?array $data, array $queryParams = null): void
	{
		self::navbar();
		CmsFactory::view()->addMain(
			[ "<h2>"._('Modules enabled')."</h2>",
				"<ul>",
				"<li><a href='/admin/contactPoint'>"._('Contact point')."</a></li>",
			"</ul>"]
		);
	}

	/**
	 * @param array|null $data
	 * @param array|null $queryParams
	 * @return void
	 */
	public function edit(?array $data, array $queryParams = null)
	{
		// TODO: Implement edit() method.
	}

	/**
	 * @param array|null $value
	 * @param array|null $queryParams
	 * @return void
	 */
	public function new(?array $value, array $queryParams = null)
	{
		// TODO: Implement new() method.
	}
}
