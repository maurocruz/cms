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
		CreativeWork::navbar();
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
	 * @param array|null $value
	 * @return void
	 */
	public function index(?array $value): void
	{
		$this->navbar();
		CmsFactory::view()->addMain(CmsFactory::view()->fragment()->reactShell('mediaObject')->ready());
	}

	public function edit(?array $data)
	{
		// TODO: Implement edit() method.
	}

	public function new(?array $value)
	{
		// TODO: Implement new() method.
	}
}