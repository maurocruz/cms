<?php

declare(strict_types=1);

namespace Plinct\Cms\View\WebSite\Index;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\App;

class Index
{
	/**
	 * @return null
	 */
	public function view()
	{
		return CmsFactory::view()->addMain("<p>Control Panel CMSCruz - version " . App::getVersion() . ".</p>");
	}

}