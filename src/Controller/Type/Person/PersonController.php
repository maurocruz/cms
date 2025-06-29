<?php
namespace Plinct\Cms\Controller\Type\Person;

use Exception;
use Plinct\Cms\CmsFactory;
use Plinct\Cms\Controller\Type\TypeControllerInterface;
use Plinct\Cms\Helpers\Sitemap;

class PersonController implements TypeControllerInterface
{
	public function index(array $params): bool
	{
		return CmsFactory::view()->webSite()->type('person')->ready();
	}

	public function new(array $params): bool
	{
		return CmsFactory::view()->webSite()->type('person')->setMethodName('new')->ready();
	}

	/**
	 * @param array $params
	 * @return bool
	 * @throws Exception
	 */
  public function edit(array $params): bool
  {
    $data = CmsFactory::model()->api()->get("person",['properties'=>'contactPoint,address,hasCertification'] + $params)->ready();
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
  public function product($params = null): mixed
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
	 * @return bool
	 * @throws Exception
	 */
  public function sitemap(): bool
  {
		$data = CmsFactory::model()->type('person')->get(['orderBy'=>'dateModified','ordering'=>'desc','fields'=>'name,url,dateModified']);
    $dataSitemap = Sitemap::buildSimpleSitemap($data);
	  return CmsFactory::view()->webSite()->type('person')->setData($dataSitemap)->setMethodName('sitemap')->ready();
  }
}
