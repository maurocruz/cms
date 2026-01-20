<?php
namespace Plinct\Cms\Controller\Type\Intangible;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\Type\TypeControllerInterface;

class OfferController implements TypeControllerInterface
{

	/**
	 * @inheritDoc
	 * @throws Exception
	 */
	public function index(array $params): bool
	{
		return CmsFactory::view()->webSite()->type('offer')->setQueryParams($params)->ready();
	}

	/**
	 * @inheritDoc
	 * @throws Exception
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
	 * @throws Exception
	 */
	public function edit(array $params): bool
	{
		$idoffer = $params['idoffer'] ?? null;
		if ($idoffer) {
			$dataOffer = CmsFactory::model()->type('offer')->get(['idoffer' => $idoffer, 'properties'=>'itemOffered']);
			if (!empty($dataOffer)) {
				return CmsFactory::view()->webSite()->type('offer')->setData($dataOffer)->setMethodName('edit')->ready();
			}
		}
		return CmsFactory::view()->addMain(CmsFactory::view()->fragment()->message()->noContent());
	}
}
