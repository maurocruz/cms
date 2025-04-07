<?php
namespace Plinct\Cms\Controller\Type\Taxon;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\Type\TypeControllerInterface;

class TaxonController implements TypeControllerInterface
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
	 */
	public function edit(array $params): bool
	{
		$data = CmsFactory::model()->api()->get("taxon", $params)->ready();
		if (!empty($data)) {
			$taxonRank = $data[0]['taxonRank'];
			$parentTaxonType = $taxonRank == 'species' ? 'genus' : ($taxonRank == 'genus'
				? 'family'
				: []);
			$parentTaxonList = CmsFactory::model()->api()->get('taxon', ['taxonRank' => $parentTaxonType, 'orderBy' => 'name'])->ready();
			foreach ($parentTaxonList as $parentTaxonListValue) {
				$td = CmsFactory::toolBox()::typeBuilder($parentTaxonListValue);
				$idtaxon = $td->getId();
				$data['parentTaxonList'][$idtaxon] = $parentTaxonListValue['name'];
			}
		}
		return CmsFactory::view()->webSite()->type('taxon')->setData($data)->setMethodName('edit')->ready();
	}
}
