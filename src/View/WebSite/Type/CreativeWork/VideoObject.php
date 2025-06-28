<?php
namespace Plinct\Cms\View\WebSite\Type\CreativeWork;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;

class VideoObject extends CreativeWorkView implements TypeViewInterface
{
	public function __destruct()
	{
		CmsFactory::view()->addHeader(
			CmsFactory::view()->fragment()->navbar()
			->type('videoObject')
			->title(_('Video'))
			->level(3)
			->ready()
		);
	}

	public function index(?array $data, array $queryParams = null): void
	{
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('videoObject')->ready()
		);
	}

	public function edit(?array $data, array $queryParams = null): void
	{
		// TODO: Implement edit() method.
	}

	public function new(?array $data, array $queryParams = null): void
	{
		// TODO: Implement new() method.
	}
}