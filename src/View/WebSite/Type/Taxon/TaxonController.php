<?php
declare(strict_types=1);
namespace Plinct\Cms\Controller\WebSite\Type\Taxon;

use Plinct\Cms\Controller\App;
use Plinct\Cms\Controller\CmsFactory;
use Plinct\Tool\DateTime;
use Plinct\Tool\Sitemap;

class TaxonController
{
  /**
   *
   */
  public function index()
  {
		return null;
  }
  /**
   * @param array $params
   * @return array
   */
  public function edit(array $params): array
  {
    $data = CmsFactory::request()->api()->get("taxon", $params)->ready();
    if (!empty($data)) {
      $taxonRank = $data[0]['taxonRank'];
      $parentTaxonType = $taxonRank == 'species' ? 'genus' : ($taxonRank == 'genus'
        ? 'family'
        : []);
      $parentTaxonList = CmsFactory::request()->api()->get('taxon', ['taxonRank' => $parentTaxonType, 'orderBy' => 'name'])->ready();
      foreach ($parentTaxonList as $parentTaxonListValue) {
        $data['parentTaxonList'][$parentTaxonListValue['idtaxon']] = $parentTaxonListValue['name'];
      }
    }
    return $data;
  }
  /**
   * @return bool
   */
  public function new(): bool {
    return true;
  }
	/**
	 *
	 */
  public function saveSitemap()
  {
    $dataforPage = [];
    $dataForType = [];
    $params = [ "orderBy" => "taxonRank", "properties" => "url,dateModified,image", 'limit'=>'none' ];
    $data = CmsFactory::request()->api()->get("taxon", $params)->ready();
    // for type pages
    foreach ($data as $valueForType) {
      $id = $valueForType['idtaxon'];
      $lastmod = DateTime::formatISO8601($valueForType['dateModified']);
      $dataForType[] = [
        "loc" => App::getURL() . "/t/taxon/$id",
        "lastmod" => $lastmod,
        "image" => $valueForType['image']
      ];
    }
    // for url (herbariodigital)
    foreach ($data as $valueForPage) {
      $lastmod = DateTime::formatISO8601($valueForPage['dateModified']);
      $url = $valueForPage['url'];
      $dataforPage[] = [
        "loc" => TaxonController . phpApp::getURL() . $url,
        "lastmod" => $lastmod,
        "image" => $valueForPage['image']
      ];
    }
    (new Sitemap($_SERVER['DOCUMENT_ROOT'].'/'."sitemap-taxon.xml"))->saveSitemap(array_merge($dataforPage, $dataForType));
  }
}
