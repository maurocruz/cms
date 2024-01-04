<?php
declare(strict_types=1);
namespace Plinct\Cms\WebSite\Type\LocalBusiness;

use Plinct\Cms\App;
use Plinct\Cms\CmsFactory;
use Plinct\Tool\DateTime;
use Plinct\Tool\Sitemap;

class LocalBusinessController
{
  /**
   * @param $params
   * @return array
   */
  public function index($params = null): array
  {
    $params2 = [ "format" => "ItemList", "orderBy" => "dateModified", "ordering" => "desc", "properties" => "additionalType,dateModified" ];
    $params3 = $params ? array_merge($params2, $params) : $params2;
    return CmsFactory::request()->api()->get("localBusiness",$params3)->ready();
  }
  /**
   * @param array $params
   * @return array
   */
  public function edit(array $params): array {
    $newParams = array_merge($params, [ "properties" => "*,location,address,organization,contactPoint,member,image" ]);
    return CmsFactory::request()->api()->get("localBusiness",$newParams)->ready();
  }
  /**
   * @return bool
   */
  public function new(): bool {
    return true;
  }
	/**
	 * @return void
	 */
  public function saveSitemap() {
    $dataSitemap = null;
    $data = CmsFactory::request()->api()->get('localBusiness', ['orderBy'=>'dateModified desc','properties'=>'image,dateModified','limit'=>'none'])->ready();
    foreach ($data as $value) {
      $id = $value['idlocalBusiness'];
      $dataSitemap[] = [
        "loc" => App::getURL() . "/t/localBusiness/$id",
        "lastmod" => DateTime::formatISO8601($value['dateModified']),
        "image" => $value['image']
      ];
    }
    (new Sitemap($_SERVER['DOCUMENT_ROOT'].'/'."sitemap-localBusiness.xml"))->saveSitemap($dataSitemap);
  }
}
