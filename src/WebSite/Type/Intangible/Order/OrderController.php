<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Type\Intangible\Order;

use Plinct\Cms\CmsFactory;
use Plinct\Tool\ArrayTool;

class OrderController
{
  /**
   * @param $customerName
   * @param $id
   * @return array
   */
  public function indexWithPartOf($customerName, $id): array
  {
    $period = filter_input(INPUT_GET, 'period') ?? '-5 year';
    if($period == 'all') $period = "all";
    if($period == 'last2years') $period = "-2 year";
    if($period == 'last5years') $period = "-5 year";
    $dataAgo = date("Y-m-d", strtotime($period, time()));
    if ($customerName) {
      $data = self::byCustomerName($customerName, $id, $dataAgo);
    } else {
      $data = CmsFactory::request()->api()->get('order', [
        "format" => "ItemList",
        "properties" => "*,customer,seller,orderedItem",
        "seller" => $id,
        "sellerType" => "Organization",
        "where" => "orderdate>'$dataAgo'",
        "orderBy" => "dateModified desc, orderDate desc"
      ])->ready();
    }
    $data['itemListOrder'] = $period;
    return $data;
  }

  /**
   * @param $itemId
   * @param $id
   * @return array
   */
  public function editWithPartOf($itemId, $id): array
	{
    $data = CmsFactory::request()->api()->get('order', [ "id" => $itemId, "properties" => "*,customer,orderedItem,partOfInvoice,history" ])->ready();
    $data[0]['orderedItem'] = CmsFactory::request()->api()->get("orderItem", [ "referencesOrder" => $itemId, "properties" => "*,orderedItem,offer" ])->ready();
    $data[0]['seller'] = CmsFactory::request()->api()->get("organization", [ "id" => $id, "properties" => "name,hasOfferCatalog" ])->ready()[0];
    $data[0]['seller']['hasOfferCatalog'] = CmsFactory::request()->api()->get("offer", [ "format" => "ItemList", "offeredBy" => $id, "offeredByType" => "Organization", "properties" => "itemOffered", "availability" => "InStock", "where" => "`validThrough`>CURDATE()" ])->ready();
    return $data;
  }

  /**
   * @param $seller
   * @return array
   */
  public function payment($seller): array
	{
    $itemList = [];
    $orderedItems = null;

    $date = self::translatePeriod(filter_input(INPUT_GET, 'period'));
    $where = "(orderStatus='orderProcessing' OR orderStatus='orderSuspended')";
    $dataOrder = CmsFactory::request()->api()->get('order',['properties'=> '*,partOfInvoice,orderedItem,customer', 'where'=>$where, 'seller'=>$seller])->ready();

    // ORDER
    foreach ($dataOrder as $itemOrder) {
      // ORDERED ITEM
      if ($itemOrder['orderedItem']) {
        $orderedItemsArray =[];
        foreach ($itemOrder['orderedItem'] as $valueOrederedItem) {
            $orderedItemsArray[] = $valueOrederedItem['orderedItem']['name'];
        }
        $orderedItems = implode(', ', $orderedItemsArray);
        unset($orderedItemsArray);
      }

      // INVOICES
      if ($itemOrder['partOfInvoice']) {
        foreach ($itemOrder['partOfInvoice'] as $key => $valueInvoice) {

          // installments var
          $numberOfInvoices = count($itemOrder['partOfInvoice']);
          $installments = $numberOfInvoices - $key . '/' . $numberOfInvoices;

          // condition if payment due and period
          if ($valueInvoice['paymentDate'] == '0000-00-00' && (!$date || $valueInvoice['paymentDueDate'] <= $date)) {
            $itemList[] = [
              'idorder' => $itemOrder['idorder'],
              'paymentDueDate' => $valueInvoice['paymentDueDate'],
              'totalPaymentDue' => $valueInvoice['totalPaymentDue'],
              'customerName' => $itemOrder['customer']['name'],
              'installments' => $installments,
              'orderedItems' => $orderedItems,
              'orderStatus' => $itemOrder['orderStatus']
            ];
          }
        }
      }
    }

    return !empty($itemList) ? ArrayTool::sortByName($itemList,'paymentDueDate') : $itemList;
  }

  /**
   * @return array
   */
  public function expired(): array
  {
    $dateLimit = self::translatePeriod(filter_input(INPUT_GET, 'period'));
    $params = [ "format" => "ItemList", "properties" => "*,customer,orderedItem", "orderStatus" => "orderProcessing", "orderBy" => "paymentDueDate asc" ];

    if($dateLimit) {
      $params['where'] = "paymentDueDate<'$dateLimit'";
    }
    return CmsFactory::request()->api()->get("order",$params)->ready();
  }

  /**
   * @param $get
   * @return false|string|null
   */
  static private function translatePeriod($get)
  {
    switch ($get) {
      case "past":
        return date("Y-m-d");
      case "current_month":
        return date('Y-m-t');
      default:
        return null;
    }
  }

  /**
   * @param $customerName
   * @param $id
   * @param $dataAgo
   * @return array
   */
  private static function byCustomerName($customerName, $id, $dataAgo): array
  {
    $dataOrder = null;

    $dataOrganization = CmsFactory::request()->api()->get('organization', [ "properties" => "name", "nameLike" => $customerName ])->ready();
    $dataPerson = CmsFactory::request()->api()->get('person', [ "nameLike" => $customerName ])->ready();
    $dataLocalBusiness = CmsFactory::request()->api()->get('localBusiness', [ "nameLike" => $customerName ])->ready();

    $array = array_merge($dataOrganization,$dataPerson,$dataLocalBusiness);

    if (!empty($array)) {
      foreach ($array as $valueCustomer) {
        $customerId = ArrayTool::searchByValue($valueCustomer['identifier'], 'id', 'value');
        $customerType = $valueCustomer['@type'];

        $newParams = [
          "properties" => "*,customer,seller,orderedItem",
          "customer" => $customerId,
          "customerType" => $customerType,
          "seller" => $id,
          "sellerType" => "Organization",
          "where" => "orderdate>'$dataAgo'",
          "orderBy" => "orderDate desc"
        ];

        $dataCustomer = CmsFactory::request()->api()->get('order', $newParams)->ready();

        if (!empty($dataCustomer)) {
          foreach ($dataCustomer as $item) {
            $dataOrder[] = ["item" => $item];
          }
        }
      }

      return [ "numberOfItems" => $dataOrder ? count($dataOrder) : '0', "itemListElement" => $dataOrder ];

    } else {
      return [ "numberOfItems" => '0', "itemListElement" => null ];
    }
  }
}
