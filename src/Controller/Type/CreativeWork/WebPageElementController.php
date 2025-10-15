<?php
namespace Plinct\Cms\Controller\Type\CreativeWork;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\Type\TypeControllerInterface;

class WebPageElementController implements TypeControllerInterface
{

	/**
	 * @inheritDoc
	 * @throws Exception
	 */
	public function index(array $params): bool
	{
		$idHasPart = $params['idHasPart'] ?? null;
		$typeHasPart = $params['typeHasPart'] ?? null;
		if ($idHasPart && $typeHasPart) {
			$data = CmsFactory::model()->type('webPage')->get(['creativeWork'=>$idHasPart,'properties'=>'isPartOf']);
			return CmsFactory::view()->webSite()->type('webPageElement')->setData($data)->setMethodName('index')->ready();
		}
		return false;
	}

	/**
	 * @inheritDoc
	 * @throws Exception
	 */
	public function new(array $params): bool
	{
		$idHasPart = $params['idHasPart'] ?? null;
		$typeHasPart = $params['typeHasPart'] ?? null;
		if ($idHasPart && $typeHasPart) {
			$data = CmsFactory::model()->type($typeHasPart)->get(['creativeWork'=>$idHasPart,'properties'=>'isPartOf']);
			return CmsFactory::view()->webSite()->type('webPageElement')->setData($data)->setMethodName('new')->ready();
		}
		return false;
	}

	/**
	 * @inheritDoc
	 * @throws Exception
	 */
	public function edit(array $params): bool
	{
		$data = CmsFactory::model()->type('webPageElement')->get(['properties'=>'hasPart,isPartOf'] + $params);
		return CmsFactory::view()->webSite()->type('webPageElement')->setData($data)->setMethodName('edit')->ready();
	}
}
