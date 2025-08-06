<?php
namespace Plinct\Cms\View\WebSite\Index;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\App;

class Index
{
	public function view(): void
	{
		CmsFactory::view()->addMain("<p>Control Panel CMSCruz - " . App::getVersion() . ".</p>");
	}
}
