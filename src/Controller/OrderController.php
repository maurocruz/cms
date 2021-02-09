<?php

namespace Plinct\Cms\Controller;

use Plinct\Api\Server\PDOConnect;
use Plinct\Api\Type\Banner;
use Plinct\Api\Type\Order;

class OrderController implements ControllerInterface
{
    public function index($params = null): array
    {
        $nameLike = $params['search'] ?? null;
        $orderBy = $params['orderBy'] ?? null;
        $ordering = $params['ordering'] ?? null;
        $params2 = [ "format" => "ItemList", "properties" => "*,seller,customer,orderedItem", "orderBy" => "orderStatus='orderProcessing' DESC, orderDate DESC", "limit" => "200" ];
        if ($orderBy && $orderBy != "orderedItem") {
            $params2['orderBy'] = "$orderBy $ordering";
        }
        if($nameLike) {
            return (new Order())->search($params2, $nameLike);
        }
        return (new Order())->get($params2);
    }

    public function edit(array $params): array
    {
        $params2 = [ "id" => $params['id'], "properties" => "*,customer,seller,orderedItem,partOfInvoice,history" ];
        $data = (new Order())->get($params2);
        // banner
        if ($data[0]['tipo'] == '4') {
            $idorder = $data[0]['idorder'];
            $paramsBanner = [ "where" => "`idorder`=$idorder" ];
            $bannerData = (new Banner())->get($paramsBanner);
            $data['banner'] = $bannerData[0] ?? null;
        }
        return $data;
    }

    public function new($params = null): ?array
    {
        $data = [];
        $item = $params['orderedItem'] ?? null;
        if ($item) {
            $itemType = $params['orderedItemType'];
            $classType = "\\Plinct\\Api\\Type\\".ucfirst($itemType);
            $orderedItem = (new $classType())->get(["id" => $item, "properties" => "*,offers,provider"]);
            $data['orderedItem'] = $orderedItem[0];
            return $data;
        }
        return null;
    }

    public function payment(): array
    {
        $date = self::translatePeriod(filter_input(INPUT_GET, 'period'));

        $query = "SELECT `invoice`.paymentDueDate, `localBusiness`.name, `invoice`.totalPaymentDue, `order`.idorder, `contratostipos`.contrato_name, `order`.orderStatus, (SELECT COUNT(*) FROM `invoice` WHERE `invoice`.referencesOrder=`order`.idorder) as number_parc FROM `invoice`, `order`, localBusiness, contratostipos WHERE (`invoice`.paymentDate = '0000-00-00' OR `invoice`.paymentDate is null) AND `invoice`.referencesOrder=`order`.idorder and `order`.orderStatus!='' AND `order`.customer=localBusiness.idlocalBusiness AND `order`.tipo=contratostipos.idcontratostipo";
        $query .= $date ? " AND `invoice`.paymentDueDate <= '$date'" : null;
        $query .= " ORDER BY `invoice`.paymentDueDate ASC;";

        return PDOConnect::run($query);
    }

    public function expired(): array
    {
        $dateLimit = self::translatePeriod(filter_input(INPUT_GET, 'period'));

        $query = "SELECT `order`.*, localBusiness.name, contratostipos.contrato_name FROM `order`, contratostipos, localBusiness WHERE `order`.orderStatus='orderProcessing' AND contratostipos.idcontratostipo=`order`.tipo AND `order`.customer=localBusiness.idlocalBusiness";
        $query .= $dateLimit ? " AND `order`.paymentDueDate < '$dateLimit'" : null;
        $query .= " ORDER BY `order`.paymentDueDate ASC;";

        return PDOConnect::run($query);
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