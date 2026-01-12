<?php

declare(strict_types=1);

namespace Plinct\Cms\Controller\Request\Server\Type;

use Plinct\Cms\Controller\CmsFactory;
use Plinct\Cms\Controller\Request\Server\ServerAbstract;

class OrderItemServer
{
  /**
   * @param array $params
   * @return mixed
   */
  public function new(array $params)
  {
    $itemsOrdered = null;
    foreach ($params['items'] as $value) {
      if (isset($value['orderedItem'])) {
        CmsFactory::request()->api()->post("orderItem", $value)->ready();
        $itemsOrdered[] = $value['orderedItemType'];
				$referencesOrder = $value['referencesOrder'];
      }
    }
    // REGISTER HISTORY IN ORDER REFERENCE
    $history = new HistoryServer('order', $referencesOrder);
    $history->setSummary(sprintf("Added new order items: %s;", implode(", ", $itemsOrdered)));
    $history->register("CREATED")->ready();
	  // SET NEW DATE IN REFERENCE ORDER
	  CmsFactory::request()->api()->put('order',['idorder'=>$referencesOrder,'dateModified'=>date('Y-m-d H:i:s')])->ready();
		// RETURN
    return filter_input(INPUT_SERVER, 'HTTP_REFERER');
  }

  /**
   * @param $params
   * @return mixed|void
   */
  public function erase($params)
  {
    // HISTORY
    $history = new HistoryServer($params['tableHasPart'],$params['idHasPart']);
    $history->setSummary(sprintf("Deleted order item %s",$params['orderItemName']));
    $history->register("DELETE")->ready();
	  // SET NEW DATE IN REFERENCE ORDER
	  CmsFactory::request()->api()->put('order',['idorder'=>$params['idHasPart'],'dateModified'=>date('Y-m-d H:i:s')])->ready();
    // RESPONSE
    CmsFactory::request()->api()->delete('orderItem',["idorderItem"=>$params['idIsPartOf']])->ready();
		return  filter_input(INPUT_SERVER, 'HTTP_REFERER');
  }
}
