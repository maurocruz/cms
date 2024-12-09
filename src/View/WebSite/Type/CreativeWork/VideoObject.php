<?php
declare(strict_types=1);
namespace Plinct\Cms\View\WebSite\Type\CreativeWork;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\TypeViewInterface;

class VideoObject implements TypeViewInterface
{
	private function navbar()
	{
		CreativeWork::navbar();
		CmsFactory::view()->addHeader(CmsFactory::view()->fragment()->navbar()
			->type('videoObject')
			->title(_('Video'))
			->level(3)
			->ready());
	}

	public function index(?array $value)
	{
		self::navBar();
		CmsFactory::view()->addMain(
			CmsFactory::view()->fragment()->reactShell('videoObject')->ready()
		);
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