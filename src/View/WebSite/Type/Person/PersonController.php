<?php
declare(strict_types=1);
namespace Plinct\Cms\Controller\WebSite\Type\Person;

use Plinct\Cms\Controller\App;
use Plinct\Cms\Controller\CmsFactory;
use Plinct\Tool\DateTime;
use Plinct\Tool\Sitemap;

class PersonController
{
  /**
   * @param null $params
   * @return array
   */
  public function index($params = null): array
  {
    $params2 = [ "format" => "ItemList", "orderBy" => "dateModified", "ordering" => "desc", "properties" => "dateModified" ];
    $params3 = $params ? array_merge($params2, $params) : $params2;
    return CmsFactory::request()->api()->get("person", $params3)->ready();
  }
  /**
   * @param array $params
   * @return array
   */
  public function edit(array $params): array
  {
    $params = array_merge($params, [ "properties" => "*,contactPoint,address,image" ]);
    return CmsFactory::request()->api()->get("person", $params)->ready();
  }
  /**
   * @param null $params
   * @return bool
   */
  public function new($params = null): bool {
    return true;
  }
  /**
   * @param null $params
   * @return array
   */
  public function service($params = null): array
  {
    $id = $params['idperson'] ?? null;
    $action = $params['action'] ?? null;
    $item = $params['item'] ?? null;
    if ($item) {
      $data = CmsFactory::request()->api()->get('service',['provider'=>$id,'providerType'=>'person','id'=>$item,'properties'=>'provider,offer'])->ready();
    } else {
      $data = CmsFactory::request()->api()->get('person', ['idperson' => $id])->ready();
    }
    if ($action == 'new') {
      $data[0]['action'] = "new";
    } else {
      $data[0]['services'] = CmsFactory::request()->api()->get('service', ['format' => 'ItemList', 'provider' => $id, 'providerType' => 'person','orderBy'=>'dateModified desc'])->ready();
    }
    return $data[0];
  }
  /**
   * PRODUCT BY PERSON
   *
   * @param null $params
   * @return mixed
   */
  public function product($params = null)
  {
    $id = $params['idperson'] ?? null;
    $action = $params['action'] ?? null;
    // LIST PRODUCTS BY PERSON
    $data = CmsFactory::request()->api()->get('person',['idperson'=>$id])->ready();
    if($action=='new') {
      $data[0]['action'] = 'new';
    } else {
      $data[0]['products'] = CmsFactory::request()->api()->get('product', ['format' => 'ItemList', 'manufacturer' => $id, 'manufacturerType' => 'person', 'orderBy' => 'dateModified desc'])->ready();
    }
    return $data[0];
  }
	/**
	 *
	 */
  public function saveSitemap()
  {
    $dataSitemap = null;
    $data = CmsFactory::request()->api()->get("person", [ "orderBy" => "dateModified desc", "properties" => "dateModified,image",'limit'=>'none'])->ready();
    $loc = App::getURL() ."/t/Person/";
    foreach ($data as $value) {
      $id = $value['idperson'];
      $lastmod = DateTime::formatISO8601($value['dateModified']);
      $dataSitemap[] = [
        "loc" => $loc.$id,
        "lastmod" => $lastmod,
        "image" => $value['image']
      ];
    }
    (new Sitemap($_SERVER['DOCUMENT_ROOT'].'/'."sitemap-person.xml"))->saveSitemap($dataSitemap);
  }
}
