<?php
declare(strict_types=1);
namespace Plinct\Cms\Controller\WebSite\Type\Place;

use Plinct\Cms\Controller\App;
use Plinct\Cms\Controller\CmsFactory;
use Plinct\Tool\DateTime;
use Plinct\Tool\Sitemap;

class PlaceController
{
	/**
	 * @return null
	 */
  public function index()
  {
		return null;
  }
  /**
   * @param null $params
   * @return bool
   */
  public function new($params = null): bool {
    return true;
  }
	/**
	 * @param array $params
	 * @return array
	 */
  public function edit(array $params): array {
    $params= array_merge($params, [ "properties" => "address" ]);
    return CmsFactory::request()->api()->get("place", $params)->ready();
  }
	/**
	 */
	public function saveSitemap() {
    $dataSitemap = null;
    $data =  CmsFactory::request()->api()->get("place", [ "orderBy" => "dateModified desc", "properties" => "*,image", 'limit'=>'none'])->ready();
    foreach ($data as $value) {
      $id = $value['idplace'];
      $dataSitemap[] = [
        "loc" => App::getURL() . "/t/place/$id",
        "lastmod" => DateTime::formatISO8601($value['dateModified']),
        "image" => $value['image']
      ];
    }
    (new Sitemap($_SERVER['DOCUMENT_ROOT'].'/'."sitemap-place.xml"))->saveSitemap($dataSitemap);
  }
}
