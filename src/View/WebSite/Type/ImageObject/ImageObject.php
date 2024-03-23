<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\ImageObject;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\CreativeWork\CreativeWork;
use Plinct\Cms\View\WebSite\Type\TypeInterface;

class ImageObject implements TypeInterface
{
	/**
	 * @return void
	 */
	protected function navBar(string $title = null)
	{
		CreativeWork::navbar();
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->title(_('Images'))
				->level(2)
				->newTab("/admin/imageObject", CmsFactory::view()->fragment()->icon()->home())
				->ready()
		);
		if ($title) {
			CmsFactory::view()->addHeader(
				CmsFactory::view()->fragment()->navbar()
					->title($title)
					->level(3)
					->ready()
			);
		}
	}


	public function index(?array $value)
	{
		self::navBar();
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('imageObject')->ready()
		);
	}

	public function new(?array $value): bool
	{
		return false;
	}

	public function edit(?array $data): bool
	{
		return false;
	}
}