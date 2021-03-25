<?php
namespace Plinct\Cms\Controller;

use Plinct\Cms\Server\Api;
use Plinct\PDO\PDOConnect;
use Plinct\Api\Type\Order;

class OrderController implements ControllerInterface
{
    public function index($params = null): array {
        $nameLike = $params['search'] ?? null;
        $orderBy = $params['orderBy'] ?? null;
        $ordering = $params['ordering'] ?? null;
        $params2 = [ "format" => "ItemList", "properties" => "*,seller,customer,orderedItem", "orderBy" => "orderStatus='orderProcessing' DESC, orderDate DESC", "limit" => "50", "count" => "all" ];
        if ($orderBy && $orderBy != "orderedItem") {
            $params2['orderBy'] = "$orderBy $ordering";
        }
        if($nameLike) {
            return (new Order())->search($params2, $nameLike);
        }
        return Api::get("order", $params2);
    }

    public function edit(array $params): array {
        $params2 = [ "id" => $params['id'], "properties" => "*,customer,seller,orderedItem,partOfInvoice,history" ];
        $data = Api::get("order", $params2);
        // banner
        if ($data[0]['tipo'] == '4') {
            $idorder = $data[0]['idorder'];
            $paramsBanner = [ "where" => "`idorder`=$idorder" ];
            $bannerData = Api::get("banner",$paramsBanner);
            $data['banner'] = $bannerData[0] ?? null;
        }
        return $data;
    }

    public function new($params = null): ?array {
        $data = [];
        $item = $params['orderedItem'] ?? null;
        if ($item) {
            $itemType = $params['orderedItemType'];
            $orderedItem = Api::get($itemType, ["id" => $item, "properties" => "*,offers,provider"]);
            $data['orderedItem'] = $orderedItem[0];
            return $data;
        }
        return null;
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
            $dataOrderedItem = Api::get("orderItem", [ "orderItemNumber" => $value['idorder'] ]);
            $value['orderedItem'] = $dataOrderedItem;
            $data2[] = $value;
        }
        return $data2;
    }

    public function expired(): array
    {
        $dateLimit = self::translatePeriod(filter_input(INPUT_GET, 'period'));
        $params = [ "format" => "ItemList", "properties" => "*,customer,orderedItem", "orderStatus" => "orderProcessing", "orderBy" => "paymentDueDate asc" ];
        if($dateLimit) {
            $params['where'] = "paymentDueDate<'$dateLimit'";
        }
        return Api::get("order",$params);
    }

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
}