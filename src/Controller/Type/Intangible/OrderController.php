<?php
namespace Plinct\Cms\Controller\Type\Intangible;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\Type\TypeControllerInterface;

class OrderController implements TypeControllerInterface
{
	/**
	 * @param array $params
	 * @return bool
	 */
	public function index(array $params): bool
	{
		$seller = $params['seller'] ?? null;
		$data = CmsFactory::model()->api()->get('thing',['idthing'=>$seller,'hasPart'=>true])->ready();
		if (isset($data[0])) {
			return CmsFactory::view()->webSite()->type('order')->setData($data[0])->setQueryParams($params)->setMethodName('index')->ready();
		} else {
			return CmsFactory::view()->addMain(CmsFactory::view()->fragment()->message()->warning("No seller found"));
		}
	}

	/**
	 * @param array $params
	 * @return bool
	 */
	public function new(array $params): bool
	{
		$seller = $params['seller'];
		$data = CmsFactory::model()->api()->get('thing',['idthing'=>$seller,'hasPart'=>true])->ready();
		if (isset($data[0])) {
			return CmsFactory::view()->webSite()->type('order')->setData($data[0])->setMethodName('new')->ready();
		} else {
			return CmsFactory::view()->addMain(CmsFactory::view()->fragment()->message()->warning("No seller found"));
		}
	}

	/**
	 * @param array $params
	 * @return bool
	 */
	public function edit(array $params): bool
	{
		$idorder = $params['idorder'] ?? null;
		$params['properties'] = "seller";
		$data = $idorder ? CmsFactory::model()->api()->get('order',$params)->ready() : [];
		return CmsFactory::view()->webSite()->type('order')->setData($data)->setMethodName('edit')->ready();
	}
}
