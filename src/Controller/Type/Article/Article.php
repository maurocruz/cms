<?php
declare(strict_types=1);
namespace Plinct\Cms\Controller\Type\Article;

use DateTime;
use Plinct\Cms\CmsFactory;

class Article
{
	/**
	 * @param array $params
	 * @return bool
	 */
  public function edit(array $params): bool
  {
    $params2 = [ "properties" => "*" ];
    $params3 = $params ? array_merge($params, $params2) : $params2;
		$data = CmsFactory::model()->api()->get("article", $params3)->ready();
		return CmsFactory::view()->webSite()->type('article')->setData($data)->setMethodName('edit')->ready();
  }

	/**
	 * @param array $params
	 * @return array
	 */
	public function update(array $params): array
	{
		$creativeWorkStatus = $params['creativeWorkStatus'];
		$datePublished = $params['datePublished'];
		if ($creativeWorkStatus == 'published' && ($datePublished == '' || $datePublished == '00-00-00 00:00:00')) {
			$params['datePublished'] = (new DateTime())->format('Y:m:d h:i:s');
		} else if($creativeWorkStatus !== 'published') {
			$params['datePublished'] = '';
		}
		return $params;
	}
}
