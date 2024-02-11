<?php

declare(strict_types=1);

namespace Plinct\Cms\Controller\Request\Type;

use Plinct\Cms\Controller\WebSite\Type\Article\ArticleController;

class Type
{
	public function article(): ArticleController
	{
		return new ArticleController();
	}
}