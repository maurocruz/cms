<?php
declare(strict_types=1);
namespace Plinct\Cms\Controller\Type\Article;

use Plinct\Cms\CmsFactory;

class Article
{
	/**
	 * @param array $params
	 * @return bool
	 */
  public function edit(array $params): bool
  {
    $params2 = [ "properties" => "*,author" ];
    $params3 = $params ? array_merge($params, $params2) : $params2;
		$data = CmsFactory::model()->api()->get("article", $params3)->ready();
		return CmsFactory::view()->webSite()->type('article')->setData($data)->setMethodName('edit')->ready();
  }
}
