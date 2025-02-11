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
		$params['properties'] = "seller,customer,invoice,acceptedOffer,orderedItem,action,hasOfferCatalog,itemOffered";
		$data = $idorder ? CmsFactory::model()->api()->get('order',$params)->ready() : [];
		return CmsFactory::view()->webSite()->type('order')->setData($data)->setMethodName('edit')->ready();
	}

	/**
	 * @param array $params
	 * @return bool
	 */
	public function expired(array $params): bool
	{
		$seller = $params['seller'] ?? null;
		$thingData = CmsFactory::model()->api()->get('thing',['idthing'=>$seller,'hasPart'=>true])->ready();
		$thingValue = $thingData[0] ?? null;

		$params = ['properties'=>'customer,orderedItem','orderStatus'=>'orderProcessing','orderBy'=>'paymentDueDate asc'];
		$dataOrder = CmsFactory::model()->api()->get('order',$params)->ready();
		return CmsFactory::view()->webSite()->type('order')->setData(['seller'=>$thingValue,'orders'=>$dataOrder])->setMethodName('expired')->ready();
	}
}
