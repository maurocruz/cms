<?php
declare(strict_types=1);
namespace Plinct\Cms\Controller\Type\Person;

use Plinct\Cms\Controller\App;
use Plinct\Cms\CmsFactory;
use Plinct\Tool\DateTime;
use Plinct\Tool\Sitemap;

class Person
{
	/**
	 * @param array $params
	 * @return bool
	 */
  public function edit(array $params): bool
  {
    $data = CmsFactory::model()->api()->get("person",['properties'=>'contactPoint,homeLocation,image,memberOf,hasCertification'] + $params)->ready();
		return CmsFactory::view()->webSite()->type('person')->setData($data)->setMethodName('edit')->ready();
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
      $data = CmsFactory::model()->api()->get('service',['provider'=>$id,'providerType'=>'person','id'=>$item,'properties'=>'provider,offer'])->ready();
    } else {
      $data = CmsFactory::model()->api()->get('person', ['idperson' => $id])->ready();
    }
    if ($action == 'new') {
      $data[0]['action'] = "new";
    } else {
      $data[0]['services'] = CmsFactory::model()->api()->get('service', ['format' => 'ItemList', 'provider' => $id, 'providerType' => 'person','orderBy'=>'dateModified desc'])->ready();
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
    $data = CmsFactory::model()->api()->get('person',['idperson'=>$id])->ready();
    if($action=='new') {
      $data[0]['action'] = 'new';
    } else {
      $data[0]['products'] = CmsFactory::model()->api()->get('product', ['format' => 'ItemList', 'manufacturer' => $id, 'manufacturerType' => 'person', 'orderBy' => 'dateModified desc'])->ready();
    }
    return $data[0];
  }
	/**
	 *
	 */
  public function saveSitemap()
  {
    $dataSitemap = null;
    $data = CmsFactory::model()->api()->get("person", [ "orderBy" => "dateModified desc", "properties" => "dateModified,image",'limit'=>'none'])->ready();
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
