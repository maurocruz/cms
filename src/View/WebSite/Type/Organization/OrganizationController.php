<?php
declare(strict_types=1);
namespace Plinct\Cms\Controller\WebSite\Type\Organization;

use Plinct\Cms\Controller\App;
use Plinct\Cms\Controller\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Intangible\Order\OrderController;
use Plinct\Tool\DateTime;
use Plinct\Tool\Sitemap;

class OrganizationController
{
  /**
   * @param null $params
   * @return array
   */
  public function index($params = null): array
  {
    $paramsSet = [ "format" => "ItemList", "properties" => "name,additionalType,dateModified", "orderBy" => "dateModified", "ordering" => "desc" ];
    $paramsGet = $params ? array_merge($paramsSet, $params) : $paramsSet;
    return CmsFactory::request()->api()->get("organization", $paramsGet)->ready();
  }
  /**
   * @param array $params
   * @param bool $allProperties
   * @return array
   */
  public function edit(array $params, bool $allProperties = true): array
  {
    $paramsGet['idorganization'] = $params['idorganization'] ?? $params['id'] ?? null;
    if ($allProperties ) $paramsGet['properties'] = "*,address,location,contactPoint,member";
    return CmsFactory::request()->server()->api()->get("organization", $paramsGet)->ready();
  }
  /**
   * @return bool
   */
  public function new(): bool {
    return true;
  }
  /**
   * SERVICE IS PART OF
   * @param array $params
   * @return array
   */
  public function service(array $params): array {
    $itemId = $params['item'] ?? null;
    if ($itemId) {
      $data = CmsFactory::request()->api()->get('service', [ "id" => $itemId, "properties" => "*,provider,offers" ])->ready();
    } else {
      $data = $this->edit($params);
      $data[0]['services'] = CmsFactory::request()->api()->get('service', ["format" => "ItemList", "properties" => "*", "provider" => $params['id'], "orderBy" => "dateModified DESC" ])->ready();
    }
    return $data[0];
  }
  /**
   *  PRODUCT IS PART OF
   * @param array $params
   * @return array
   */
  public function product(array $params): array
  {
    $id = $params['idorganization'] ?? null;
    $itemId = $params['item'] ?? null;
    $action = $params['action'] ?? null;
    $data = CmsFactory::request()->api()->get("organization", [ "idorganization" => $id, "properties" => "*,address,location,contactPoint,member,image" ])->ready();
    if ($itemId) {
      $data[0]['action'] = "edit";
      $productData = CmsFactory::request()->api()->get('product', [ "idproduct" => $itemId, "properties" => "*,manufacturer,offers,image" ])->ready();
      $data[0]['product'] = $productData[0];
    } else {
      if ($action == 'new') {
        $data[0]['action'] = 'new';
      } else {
        $data[0]['products'] = CmsFactory::request()->api()->get('product', ["format" => "ItemList", "properties" => "*", "manufacturer" => $id])->ready();
      }
    }
    return $data[0];
  }
  /**
   * @param array $params
   * @return array
   */
  public function order(array $params): array
  {
    // PARAMS
    $itemId = $params['item'] ?? null;
    $id = $params['id'];
    $customerName = $params['customerName'] ?? null;
    $action = filter_input(INPUT_GET, 'action');
    // ITEM
    if ($itemId):
      $data = (new OrderController())->editWithPartOf($itemId, $id);
    // PAYMENT
    elseif($action == "payment"):
      $data = $this->edit($params);
      $data[0]['orders'] = (new OrderController())->payment($id);
    // EXPIRED
    elseif($action == "expired"):
      $data = $this->edit($params);
      $data[0]['orders'] = (new OrderController())->expired();
    // ACTION
    elseif ($action == 'new'):
      $data = $this->edit($params, false);
    // LIST
    else:
      $data = $this->edit($params);
      $data[0]['orders'] = (new OrderController())->indexWithPartOf($customerName, $id);
    endif;
    return $data[0];
  }
	/**
	 */
	public function saveSitemap()
  {
    $dataSitemap = null;
    $data = CmsFactory::request()->api()->get('organization', ['properties'=>'image,dateModified','orderBy'=>'dateModified desc','limit'=>'none'])->ready();
    foreach ($data as $value) {
      $id = $value['idorganization'];
      $dataSitemap[] = [
        "loc" => App::getURL() . "/t/organization/$id",
        "lastmod" => DateTime::formatISO8601($value['dateModified']),
        "image" => $value['image']
      ];
    }
    (new Sitemap($_SERVER['DOCUMENT_ROOT'].'/'."sitemap-organization.xml"))->saveSitemap($dataSitemap);
  }
}
