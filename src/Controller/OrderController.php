<?php
namespace Plinct\Cms\Controller;

use Plinct\Cms\Server\Api;
use Plinct\PDO\PDOConnect;
use Plinct\Tool\ArrayTool;

class OrderController {

    public function indexWithPartOf($customerName, $id): array {
        $dataAgo = date("Y-m-d", strtotime("-2 year", time()));
        if ($customerName) {
            return self::byCustomerName($customerName, $id, $dataAgo);
        } else {
            return Api::get('order', ["format" => "ItemList", "properties" => "*,customer,seller,orderedItem", "seller" => $id, "sellerType" => "Organization", "where" => "orderdate>'$dataAgo'", "orderBy" => "orderDate desc"]);
        }
    }

    public function editWithPartOf($itemId, $id) {
        $data = Api::get('order', [ "id" => $itemId, "properties" => "*,customer,orderedItem,partOfInvoice,history" ]);
        $data[0]['orderedItem'] = Api::get("orderItem", [ "referencesOrder" => $itemId, "properties" => "*,orderedItem,offer" ]);
        $data[0]['seller'] = Api::get("organization", [ "id" => $id, "properties" => "name,hasOfferCatalog" ])[0];
        $data[0]['seller']['hasOfferCatalog'] = Api::get("offer", [ "format" => "ItemList", "offeredBy" => $id, "offeredByType" => "Organization", "properties" => "itemOffered", "availability" => "InStock", "where" => "`validThrough`>CURDATE()" ] );
        return $data;
    }

    public function payment(): array {
        $data2 = [];
        $date = self::translatePeriod(filter_input(INPUT_GET, 'period'));
        $query = "select `order`.idorder, `order`.orderStatus, `invoice`.paymentDueDate, `invoice`.totalPaymentDue, `order`.customer, `order`.customerType, (SELECT COUNT(*) FROM `invoice` WHERE `invoice`.referencesOrder=`order`.idorder) as totalOfInstallments, (SELECT COUNT(*) FROM `invoice` WHERE `invoice`.referencesOrder=`order`.idorder AND invoice.paymentDate is not null AND invoice.paymentDate!='0000-00-00')+1 as numberOfTheInstallments";
        $query .= " FROM `invoice`, `order`";
        $query .= " WHERE (invoice.paymentDate is null OR invoice.paymentDate='0000-00-00')";
        $query .= " AND `order`.idorder= `invoice`.referencesOrder AND `order`.orderStatus!='OrderCancelled' AND `order`.orderStatus!='OrderDelivered'";
        $query .= $date ? " AND invoice.paymentDueDate <= '$date'" : null;
        $query .= " ORDER BY `invoice`.paymentDueDate";
        $query .= ";";
        $data =  PDOConnect::run($query);
        foreach ($data as $value) {
            $id = $value['customer'];
            $table = lcfirst($value['customerType']);
            $idName = "id".$table;
            // CUSTOMER
            $dataCustomer = PDOConnect::run("SELECT * FROM $table WHERE `$idName`=$id;");
            $value['customer'] = $dataCustomer[0] ?? null;
            // ORDERED ITEM
            $dataOrderedItem = Api::get("orderItem", [ "referencesOrder" => $value['idorder'] ]);
            $value['orderedItem'] = $dataOrderedItem;
            $data2[] = $value;
        }
        return $data2;
    }

    public function expired(): array {
        $dateLimit = self::translatePeriod(filter_input(INPUT_GET, 'period'));
        $params = [ "format" => "ItemList", "properties" => "*,customer,orderedItem", "orderStatus" => "orderProcessing", "orderBy" => "paymentDueDate asc" ];
        if($dateLimit) {
            $params['where'] = "paymentDueDate<'$dateLimit'";
        }
        return Api::get("order",$params);
    }

    static private function translatePeriod($get) {
        switch ($get) {
            case "past":
                return date("Y-m-d");
            case "current_month":
                return date('Y-m-t');
            default:
                return null;
        }
    }

    private static function byCustomerName($customerName, $id, $dataAgo): array {
        $dataOrder = null;
        $dataOrganization = Api::get('organization', [ "properties" => "name", "nameLike" => $customerName ]);
        $dataPerson = Api::get('person', [ "nameLike" => $customerName ]);
        $dataLocalBusiness = Api::get('localBusiness', [ "nameLike" => $customerName ]);
        $array = array_merge($dataOrganization,$dataPerson,$dataLocalBusiness);
        if (!empty($array)) {
            foreach ($array as $valueCustomer) {
                $customerId = ArrayTool::searchByValue($valueCustomer['identifier'], 'id', 'value');
                $customerType = $valueCustomer['@type'];
                $newParams = ["properties" => "*,customer,seller,orderedItem", "customer" => $customerId, "customerType" => $customerType, "seller" => $id, "sellerType" => "Organization", "where" => "orderdate>'$dataAgo'", "orderBy" => "orderDate desc"];
                $dataCustomer = Api::get('order', $newParams);
                if (!empty($dataCustomer)) {
                    $dataOrder[] = ["item" => $dataCustomer[0]];
                }
            }
            return [ "numberOfItems" => count($dataOrder), "itemListElement" => $dataOrder ];
        } else {
            return [ "numberOfItems" => '0', "itemListElement" => null ];
        }
    }
}