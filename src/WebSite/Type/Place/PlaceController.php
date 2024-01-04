<?php
declare(strict_types=1);
namespace Plinct\Cms\WebSite\Type\Place;

use Plinct\Cms\App;
use Plinct\Cms\CmsFactory;
use Plinct\Tool\DateTime;
use Plinct\Tool\Sitemap;

class PlaceController
{
  /**
   * @param null $params
   * @return array
   */
  public function index($params = null): array
  {
    // TODO Habilitar busca (search via get query string)
    // TODO aumentar largura dos campos de latitude e longitude no banco de dados
    $params = array_merge([ "format" => "ItemList", "orderBy" => "dateModified", "ordering" => "desc" ], (array)$params);
    return CmsFactory::request()->api()->get("place", $params)->ready();
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
    $params= array_merge($params, [ "properties" => "address,image" ]);
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
