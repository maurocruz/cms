<?php
namespace Plinct\Cms\Controller\Type\WebSite;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\Type\TypeControllerInterface;
use Plinct\Cms\Controller\Type\WebPage\WebPageController;

class WebSite implements TypeControllerInterface
{
	/**
	 * @param array $params
	 * @return bool
	 */
	public function webPage(array $params): bool
	{
		$idwebSite = $params['id'] ?? null;
		$idwebPage = $params['item'] ?? null;
		if ($idwebSite && !$idwebPage) {
			$dataWebSite = CmsFactory::model()->api()->get('webSite', ['idwebSite'=>$idwebSite, 'properties'=>'hasPart'])->ready();
			return CmsFactory::view()->webSite()->type('webSite')->setMethodName('webPage')->setData($dataWebSite[0])->ready();
		}
		if($idwebPage) {
			$webPageController = new WebPageController();
			return $webPageController->edit($params);
			//$dataWebPage = CmsFactory::model()->api()->get('webPage',['idwebPage'=>$idwebPage, 'properties'=>'isPartOf,hasPart'])->ready();
			//return CmsFactory::view()->webSite()->type('webPage')->setMethodName('edit')->setData($dataWebPage[0])->ready();
		}
		return false;
	}

	public function index(array $params): bool
	{
		return false;
	}

	public function new(array $params): bool
	{
		return false;
	}

	public function edit(array $params): bool
	{
		return false;
	}
}
