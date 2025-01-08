<?php
namespace Plinct\Cms\Controller\Type\Intangible;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\Type\TypeControllerInterface;

class OfferController implements TypeControllerInterface
{

	/**
	 * @inheritDoc
	 */
	public function index(array $params): bool
	{
		$offeredBy = $params['offeredBy'] ?? null;
		if ($offeredBy) {
			$dataThing = CmsFactory::model()->type('thing')->get(['idthing' => $offeredBy, 'hasPart'=>true]);
		}
		if (isset($dataThing[0])) {
			return CmsFactory::view()->webSite()->type('offer')->setData($dataThing[0])->ready();
		} else {
			return CmsFactory::view()->addMain(CmsFactory::view()->fragment()->message()->noContent());
		}
	}

	/**
	 * @inheritDoc
	 */
	public function new(array $params): bool
	{
		$offeredBy = $params['offeredBy'] ?? null;
		if ($offeredBy) {
			$dataThing = CmsFactory::model()->type('thing')->get(['idthing' => $offeredBy, 'hasPart'=>true]);
		}
		if (isset($dataThing[0])) {
			return CmsFactory::view()->webSite()->type('offer')->setData($dataThing[0])->setMethodName('new')->ready();
		} else {
			return CmsFactory::view()->addMain(CmsFactory::view()->fragment()->message()->noContent());
		}
	}

	/**
	 * @inheritDoc
	 */
	public function edit(array $params): bool
	{
		$idoffer = $params['idoffer'] ?? null;
		if ($idoffer) {
			$dataOffer = CmsFactory::model()->type('offer')->get(['idoffer' => $idoffer, 'properties'=>'itemOffered,offeredBy']);
			if (!empty($dataOffer)) {
				return CmsFactory::view()->webSite()->type('offer')->setData($dataOffer)->setMethodName('edit')->ready();
			}
		}
		return CmsFactory::view()->addMain(CmsFactory::view()->fragment()->message()->noContent());
	}
}
