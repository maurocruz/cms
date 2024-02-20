<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\ImageObject;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\TypeInterface;

class ImageObject extends ImageObjectView implements TypeInterface
{
	/**
	 * @return void
	 */
	protected function navBarLevel1()
	{
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->title(_('Images'))
				->level(2)
				->newTab("/admin/imageObject", CmsFactory::view()->fragment()->icon()->home())
				->newTab("/admin/imageObject/new", CmsFactory::view()->fragment()->icon()->plus())
				->ready()
		);
	}

	protected function navBarLevel2($title)
	{
		self::navBarLevel1();
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
				->title($title)
				->level(3)
				->ready()
		);
	}

	public function index(?array $value)
	{
		self::navBarLevel1();
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('imageObject')->ready()
		);
	}

	public function new(?array $value)
	{
		self::navBarLevel2(_('Add'));
		CmsFactory::view()->addMain(parent::upload($data['listLocation'] ?? null, $data['listKeywords'] ?? null));
	}

	public function edit(?array $data): bool
	{
		return false;
	}


	public function getForm(string $tableHasPart, string $idHasPart, array $data = null): array
	{
		return [];
	}
}