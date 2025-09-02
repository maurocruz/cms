<?php
namespace Plinct\Cms\Controller\Type\Taxon;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\Type\ThingController;
use Plinct\Cms\Controller\Type\TypeControllerInterface;

class TaxonController extends ThingController implements TypeControllerInterface
{
	/**
	 * @param array $params
	 * @return bool
	 */
	public function index(array $params): bool
	{
		return CmsFactory::view()->webSite()->type('taxon')->setData([])->ready();
	}

	/**
	 * @param array $params
	 * @return bool
	 */
	public function new(array $params): bool
	{
		return false;
	}

	/**
	 * @param array $params
	 * @return bool
	 * @throws Exception
	 */
	public function edit(array $params): bool
	{
		$data = CmsFactory::model()->type("taxon")->get($params);
		if (isset($data[0])) {
			$taxonRank = $data[0]['taxonRank'];
			$parentTaxonType = $taxonRank == 'species' ? 'genus' : ($taxonRank == 'genus'
				? 'family'
				: []);
			$parentTaxonList = CmsFactory::model()->type('taxon')->get(['taxonRank' => $parentTaxonType, 'orderBy' => 'name', 'limit'=>'none']);
			foreach ($parentTaxonList as $parentTaxonListValue) {
				$td = CmsFactory::toolBox()::typeBuilder($parentTaxonListValue);
				$idtaxon = $td->getId();
				$data['parentTaxonList'][$idtaxon] = $parentTaxonListValue['name'];
			}
		}
		return CmsFactory::view()->webSite()->type('taxon')->setData($data)->setMethodName('edit')->ready();
	}
}
