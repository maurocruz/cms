<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\CreativeWork;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;

class VideoObject implements TypeViewInterface
{
	private function navbar(): void
	{
		CreativeWork::navbar();
		CmsFactory::view()->addHeader(CmsFactory::view()->fragment()->navbar()
			->type('videoObject')
			->title(_('Video'))
			->level(3)
			->ready());
	}

	public function index(?array $data, array $queryParams = null): void
	{
		self::navBar();
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('videoObject')->ready()
		);
	}

	public function edit(?array $data, array $queryParams = null)
	{
		// TODO: Implement edit() method.
	}

	public function new(?array $value, array $queryParams = null)
	{
		// TODO: Implement new() method.
	}
}