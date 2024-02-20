<?php

declare(strict_types=1);

namespace Plinct\Cms\Controller\Request\Type;

use Plinct\Cms\Controller\WebSite\Type\Article\Article;

class Type
{
	public function article(): Article
	{
		return new Article();
	}
}