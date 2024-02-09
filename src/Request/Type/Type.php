<?php

declare(strict_types=1);

namespace Plinct\Cms\Request\Type;

use Plinct\Cms\WebSite\Type\Article\ArticleController;

class Type
{
	public function article(): ArticleController
	{
		return new ArticleController();
	}
}