<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\CreativeWork;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\TypeInterface;

class MediaObject implements TypeInterface
{
	private function navbar()
	{
		CreativeWork::navbar();
		CmsFactory::view()->addHeader(CmsFactory::view()->fragment()->navbar()
			->type('mediaObject')
			->title(_("Media Object"))
			->level(3)
			->newTab('/admin/mediaObject', CmsFactory::view()->fragment()->icon()->home(16,16))
			->newTab('/admin/imageObject', _("Images"))
			->newTab('/admin/videoObject', _("Videos"))
			->ready()
		);
	}

	public function index(?array $value)
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