<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type;

use Plinct\Cms\WebSite\Type\ImageObject\ImageObjectView;
use Plinct\Cms\WebSite\Type\Intangible\Intangible;

class Type
{
	/**
	 * @return ImageObjectView
	 */
	public function imageObject(): ImageObjectView
	{
		return new ImageObjectView();
	}

	/**
	 * @return Intangible
	 */
	public function intangible(): Intangible
	{
		return new Intangible();
	}


}