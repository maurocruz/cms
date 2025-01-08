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
		$seller = $params['seller'];
		$data = CmsFactory::model()->api()->get('thing',['idthing'=>$seller,'hasPart'=>true])->ready();
		if (isset($data[0])) {
			return CmsFactory::view()->webSite()->type('order')->setData($data[0])->setMethodName('index')->ready();
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
		$seller = $params['seller'] ?? null;
		$queryArray['hasPart'] = true;
		$queryArray['properties'] = "seller,customer,invoice,acceptedOffer,orderedItem,action,hasOfferCatalog,itemOffered";
		$queryArray['availability'] = "InStock";
		$queryArray['isValidThrough'] = true;
		if ($idorder) {
			$queryArray['idorder'] = $idorder;
		}
		if ($seller) {
			$queryArray['seller'] = $seller;
		}
		$data = CmsFactory::model()->api()->get('order',$queryArray)->ready();
		return CmsFactory::view()->webSite()->type('order')->setData($data)->setMethodName('edit')->ready();
	}

	public function invoice(array $params): bool
	{
		$seller = $params['seller'] ?? null;
		if ($seller) {
			$dataSeller = CmsFactory::model()->api()->get('thing',['idthing'=>$seller,'hasPart'=>true])->ready();
			if (isset($dataSeller[0])) {
				$valueSeller = $dataSeller[0];
			}
		}
		//$dataOrder = CmsFactory::model()->api()->get('order',$params)->ready();
		return CmsFactory::view()->webSite()->type('order')->setData(['seller'=>$valueSeller])->setMethodName('payment')->ready();
	}
}
