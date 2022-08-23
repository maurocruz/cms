<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Intangible;

class Intangible
{
	public function contactPoint(): ContactPoint
	{
		return new ContactPoint();
	}
}
