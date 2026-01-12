<?php
namespace Plinct\Cms\Controller\Type\Intangible;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\Type\TypeControllerInterface;

class ServiceController implements TypeControllerInterface
{
	/**
	 * @inheritDoc
	 * @throws Exception
	 */
	public function index(array $params): bool
	{
		$provider = $params['provider'] ?? null;
		if ($provider) {
				$dataThing = CmsFactory::model()->type('thing')->get(['idthing' => $provider, 'hasPart'=>true]);
		}
		if (isset($dataThing[0]) && $provider) {
			return CmsFactory::view()->webSite()->type('service')->setData($dataThing[0])->ready();
		} else {
			return CmsFactory::view()->fragment()->message()->noContent();
		}
	}

	/**
	 * @inheritDoc
	 * @throws Exception
	 */
	public function new(array $params): bool
	{
		$provider = $params['provider'] ?? null;
		if ($provider) {
			$dataThing = CmsFactory::model()->type('thing')->get(['idthing' => $provider, 'hasPart'=>true]);
		}
		return CmsFactory::view()->webSite()->type('service')->setData($dataThing[0] ?? [])->setMethodName('new')->ready();
	}

	/**
	 * @inheritDoc
	 * @throws Exception
	 */
	public function edit(array $params): bool
	{
		$idservice = $params['idservice'] ?? null;
		if ($idservice) {
			$dataService = CmsFactory::model()->type('service')->get(['idservice' => $idservice,'properties'=>'provider,offer']);
			if (isset($dataService[0]) && $dataService[0]) {
				return CmsFactory::view()->webSite()->type('service')->setData($dataService)->setMethodName('edit')->ready();
			} else {
				return CmsFactory::view()->fragment()->message()->noContent();
			}
		}
		return false;
	}
}
