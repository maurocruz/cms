<?php
declare(strict_types=1);
namespace Plinct\Cms\Controller\WebSite\Type\Organization;

use Plinct\Cms\CmsFactory;
use Plinct\Cms\View\WebSite\Type\Intangible\Order\OrderController;

class Organization
{
  /**
   * @param array $params
   * @return bool
   */
  public function edit(array $params): bool
  {
	  $data = CmsFactory::model()->api()->get("organization",['properties'=>'contactPoint,location,image'] + $params)->ready();
	  return CmsFactory::view()->webSite()->type('organization')->setData($data)->setMethodName('edit')->ready();
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
}
