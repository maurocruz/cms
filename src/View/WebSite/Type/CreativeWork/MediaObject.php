<?php
namespace Plinct\Cms\View\WebSite\Type\CreativeWork;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;

class MediaObject implements TypeViewInterface
{
	/**
	 * @return void
	 */
	public static function navbar(): void
	{
		CreativeWorkViewView::navbar();
		CmsFactory::view()->addHeader(CmsFactory::view()->fragment()->navbar()
			->type('mediaObject')
			->title(_("Media Object"))
			->level(3)
			->newTab('/admin/mediaObject', CmsFactory::view()->fragment()->icon()->home())
			->setModulesAvailable(['ImageObject','VideoObject'])
			->search()
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
		$this->navbar();
		CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('mediaObject')->ready());
	}

	public function edit(?array $data, array $queryParams = null)
	{
		// TODO: Implement edit() method.
	}

	public function new(?array $data, array $queryParams = null)
	{
		// TODO: Implement new() method.
	}
}